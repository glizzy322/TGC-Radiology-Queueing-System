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
            ORDER BY t.created_at ASC
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
            if (str_starts_with($t['procedure_code'], 'UT')) $key = 'ultrasound';
            if (str_starts_with($t['procedure_code'], 'CT')) $key = 'ctscan';
            
            $formatted = [
                'id' => $t['ticket_code'],
                'procedureKey' => $key,
                'patientType' => $t['patient_type'],
                'status' => $t['status'],
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

    public function callNext(string $procedureKey, int $staffId): ?array
    {
        $this->db->beginTransaction();
        try {
            // Find procedure id based on key
            $prefix = $procedureKey === 'xray' ? 'XR' : ($procedureKey === 'ultrasound' ? 'UT' : 'CT');
            $today = date('Y-m-d');
            
            // Find oldest waiting ticket
            $stmt = $this->db->prepare("
                SELECT t.id, t.ticket_code 
                FROM queue_tickets t
                JOIN procedures p ON t.procedure_id = p.id
                WHERE p.code LIKE ? AND t.status = 'waiting' AND DATE(t.created_at) = ?
                ORDER BY t.created_at ASC 
                LIMIT 1 FOR UPDATE
            ");
            $stmt->execute(["$prefix%", $today]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                $this->db->rollBack();
                return null;
            }

            // Update ticket
            $update = $this->db->prepare("UPDATE queue_tickets SET status = 'serving', serving_time = CURRENT_TIMESTAMP WHERE id = ?");
            $update->execute([$ticket['id']]);

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
            $stmt = $this->db->prepare("SELECT id FROM queue_tickets WHERE ticket_code = ? AND status = 'serving' FOR UPDATE");
            $stmt->execute([$ticketCode]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                $this->db->rollBack();
                return false;
            }

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
