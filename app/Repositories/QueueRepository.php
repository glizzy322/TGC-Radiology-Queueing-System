<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class QueueRepository
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getTodayState(): array
    {
        $today = date('Y-m-d');
        
        $stmt = $this->db->prepare("
            SELECT t.*, p.code as procedure_code, c.code as patient_type 
            FROM queue_tickets t
            JOIN procedures p ON t.procedure_id = p.id
            JOIN patient_categories c ON t.category_id = c.id
            WHERE DATE(t.created_at) = ?
            ORDER BY c.priority_rank ASC, t.created_at ASC
        ");
        $stmt->execute([$today]);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $state = [
            'queues' => ['xray' => [], 'ultrasound' => [], 'ctscan' => []],
            'serving' => ['xray' => [], 'ultrasound' => [], 'ctscan' => []],
            'completed' => []
        ];

        foreach ($tickets as $t) {
            $key = 'xray';
            if (str_starts_with($t['procedure_code'], 'US') || str_starts_with($t['procedure_code'], 'UT')) $key = 'ultrasound';
            if (str_starts_with($t['procedure_code'], 'CT')) $key = 'ctscan';
            
            $formatted = [
                'id' => $t['ticket_code'],
                'displayId' => preg_replace('/^(.+)-\d{8}-(\d+)$/', '$1-$2', $t['ticket_code']),
                'procedureKey' => $key,
                'patientType' => $t['patient_type'],
                'status' => $t['status'],
                'servingSlot' => isset($t['serving_slot']) ? (int)$t['serving_slot'] : null,
                'createdAt' => $t['created_at'],
                'calledAt' => $t['serving_time'],
                'completedAt' => $t['completed_time']
            ];

            if ($t['status'] === 'waiting') {
                $state['queues'][$key][] = $formatted;
            } elseif ($t['status'] === 'serving') {
                $state['serving'][$key][] = $formatted;
            } elseif ($t['status'] === 'completed') {
                $state['completed'][] = $formatted;
            }
        }

        return $state;
    }

    public function callNext(string $procedureKey, int $staffId, int $slotIndex = 0): ?array
    {
        \App\Services\QueueAccess::check($this->db, $staffId, $procedureKey, $slotIndex);
        $this->db->beginTransaction();
        try {
            // Find procedure id based on key
            $prefix = $procedureKey === 'xray' ? 'XR' : ($procedureKey === 'ultrasound' ? 'UT' : 'CT');
            $today = date('Y-m-d');
            
            // A procedure row serializes callers even when the requested slot is empty.
            $lock = $this->db->prepare('SELECT id FROM procedures WHERE code LIKE ? ORDER BY id FOR UPDATE');
            $lock->execute(["$prefix%"]);
            $lock->fetchAll();
            $occupied = $this->db->prepare("SELECT t.id FROM queue_tickets t JOIN procedures p ON p.id = t.procedure_id WHERE p.code LIKE ? AND t.status = 'serving' AND COALESCE(t.serving_slot, 0) = ? AND DATE(t.created_at) = ? FOR UPDATE");
            $occupied->execute(["$prefix%", $slotIndex, $today]);
            if ($occupied->fetch()) throw new \DomainException('Serving slot is occupied', 409);
            // Find oldest waiting ticket with highest priority
            $stmt = $this->db->prepare("
                SELECT t.id, t.ticket_code 
                FROM queue_tickets t
                JOIN procedures p ON t.procedure_id = p.id
                JOIN patient_categories c ON t.category_id = c.id
                WHERE p.code LIKE ? AND t.status = 'waiting' AND DATE(t.created_at) = ?
                ORDER BY c.priority_rank ASC, t.created_at ASC 
                LIMIT 1 FOR UPDATE
            ");
            $stmt->execute(["$prefix%", $today]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                $this->db->rollBack();
                return null;
            }

            // Update ticket
            $update = $this->db->prepare("UPDATE queue_tickets SET status = 'serving', serving_slot = ?, serving_time = CURRENT_TIMESTAMP WHERE id = ?");
            $update->execute([$slotIndex, $ticket['id']]);

            // Add event
            $event = $this->db->prepare("INSERT INTO queue_events (ticket_id, actor_id, action) VALUES (?, ?, 'called')");
            $event->execute([$ticket['id'], $staffId]);

            $this->db->commit();
            return $ticket;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function completeTicket(string $ticketCode, int $staffId): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT id, procedure_id, serving_slot FROM queue_tickets WHERE ticket_code = ? AND status = 'serving' FOR UPDATE");
            $stmt->execute([$ticketCode]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                $this->db->rollBack();
                return false;
            }

            $procedure = $this->db->prepare('SELECT code FROM procedures WHERE id = ?');
            $procedure->execute([$ticket['procedure_id']]);
            $code = $procedure->fetchColumn();
            $key = str_starts_with($code, 'XR') ? 'xray' : (str_starts_with($code, 'CT') ? 'ctscan' : 'ultrasound');
            \App\Services\QueueAccess::check($this->db, $staffId, $key, (int)($ticket['serving_slot'] ?? 0));
            $update = $this->db->prepare("UPDATE queue_tickets SET status = 'completed', completed_time = CURRENT_TIMESTAMP WHERE id = ?");
            $update->execute([$ticket['id']]);

            $event = $this->db->prepare("INSERT INTO queue_events (ticket_id, actor_id, action) VALUES (?, ?, 'completed')");
            $event->execute([$ticket['id'], $staffId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
