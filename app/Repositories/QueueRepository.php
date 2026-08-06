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
}
