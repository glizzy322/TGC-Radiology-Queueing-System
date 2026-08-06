<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Exception;

class TicketRepository
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function generateTicket(int $procedureId, string $procedurePrefix, int $categoryId, int $issuerId): array
    {
        $this->db->beginTransaction();

        try {
            $today = date('Y-m-d');
            
            // Lock the counter row
            $stmt = $this->db->prepare("SELECT last_sequence_number FROM queue_counters WHERE date = ? AND procedure_id = ? FOR UPDATE");
            $stmt->execute([$today, $procedureId]);
            $counter = $stmt->fetch();

            $nextSeq = 1;
            if ($counter) {
                $nextSeq = (int) $counter['last_sequence_number'] + 1;
                $update = $this->db->prepare("UPDATE queue_counters SET last_sequence_number = ? WHERE date = ? AND procedure_id = ?");
                $update->execute([$nextSeq, $today, $procedureId]);
            } else {
                $insert = $this->db->prepare("INSERT INTO queue_counters (date, procedure_id, last_sequence_number) VALUES (?, ?, ?)");
                $insert->execute([$today, $procedureId, $nextSeq]);
            }

            $ticketCode = sprintf("%s-%03d", $procedurePrefix, $nextSeq);

            // Insert Ticket
            $insertTicket = $this->db->prepare("INSERT INTO queue_tickets (ticket_code, procedure_id, category_id, issuer_id) VALUES (?, ?, ?, ?)");
            $insertTicket->execute([$ticketCode, $procedureId, $categoryId, $issuerId]);
            $ticketId = $this->db->lastInsertId();

            // Insert Audit Event
            $insertEvent = $this->db->prepare("INSERT INTO queue_events (ticket_id, actor_id, action) VALUES (?, ?, ?)");
            $insertEvent->execute([$ticketId, $issuerId, 'issued']);

            $this->db->commit();

            // Fetch complete ticket data to return
            $getTicket = $this->db->prepare("
                SELECT t.*, p.name as procedure_name, p.code as procedure_code, c.code as category_code 
                FROM queue_tickets t
                JOIN procedures p ON t.procedure_id = p.id
                JOIN patient_categories c ON t.category_id = c.id
                WHERE t.id = ?
            ");
            $getTicket->execute([$ticketId]);
            return $getTicket->fetch();

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
