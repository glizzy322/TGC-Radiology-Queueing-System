<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class ReportRepository
{
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getTicketsByDateRange(?string $startDate, ?string $endDate): array
    {
        $query = "
            SELECT t.*, p.code as procedure_code, c.code as patient_type 
            FROM queue_tickets t
            JOIN procedures p ON t.procedure_id = p.id
            JOIN patient_categories c ON t.category_id = c.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($startDate) {
            $query .= " AND DATE(t.created_at) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $query .= " AND DATE(t.created_at) <= ?";
            $params[] = $endDate;
        }
        
        $query .= " ORDER BY t.created_at ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $generated = [];
        $completed = [];

        foreach ($tickets as $t) {
            $key = 'xray';
            if (str_starts_with($t['procedure_code'], 'UT') || str_starts_with($t['procedure_code'], 'US')) $key = 'ultrasound';
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

            $generated[] = $formatted;
            
            if ($t['status'] === 'completed') {
                $completed[] = $formatted;
            }
        }

        return [
            'generatedTickets' => $generated,
            'completedTickets' => $completed
        ];
    }
}
