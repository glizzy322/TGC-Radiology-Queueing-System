<?php

namespace App\Controllers;

use App\Services\TicketService;

class TicketController
{
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $role = $_SESSION['user_role'] ?? '';
        if ($role !== 'receptionist' && $role !== 'administrator') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['procedure_id']) || !isset($input['category_id']) || !isset($input['procedure_prefix'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $service = new TicketService();
        try {
            $ticket = $service->createTicket(
                (int) $input['procedure_id'],
                $input['procedure_prefix'],
                (int) $input['category_id']
            );
            echo json_encode(['status' => 'success', 'ticket' => $ticket]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate ticket', 'message' => $e->getMessage()]);
        }
    }
}
