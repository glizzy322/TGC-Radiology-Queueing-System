<?php
namespace App\Controllers;
use App\Repositories\QueueRepository;

class QueueController
{
    public function state()
    {
        header('Content-Type: application/json');
        try {
            $repo = new QueueRepository();
            $state = $repo->getTodayState();
            echo json_encode(['status' => 'success', 'data' => $state]);
        } catch (\Exception $e) {
            http_response_code(in_array($e->getCode(), [400, 403, 409], true) ? $e->getCode() : 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function requireRole(string|array $roles)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userRole = $_SESSION['user_role'] ?? '';
        if ($userRole === 'administrator') return; // admin = god mode
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($userRole, $allowed, true)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function call()
    {
        $this->requireRole('radiology_staff');
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $procedureKey = $input['procedure_key'] ?? '';
        $slotIndex = $input['slot_index'] ?? 0;

        if (!is_string($procedureKey) || !in_array($procedureKey, ['xray', 'ultrasound', 'ctscan'], true) || !is_int($slotIndex)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing procedure_key']);
            return;
        }

        try {
            $repo = new QueueRepository();
            $ticket = $repo->callNext($procedureKey, $_SESSION['user_id'], $slotIndex);
            if ($ticket) {
                echo json_encode(['status' => 'success', 'ticket' => $ticket]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No waiting tickets for this procedure']);
            }
        } catch (\Exception $e) {
            http_response_code(in_array($e->getCode(), [400, 403, 409], true) ? $e->getCode() : 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function complete()
    {
        $this->requireRole('radiology_staff');
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $ticketCode = $input['ticket_code'] ?? '';

        if (!$ticketCode) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ticket_code']);
            return;
        }

        try {
            $repo = new QueueRepository();
            $success = $repo->completeTicket($ticketCode, $_SESSION['user_id']);
            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found or not serving']);
            }
        } catch (\Exception $e) {
            http_response_code(in_array($e->getCode(), [400, 403, 409], true) ? $e->getCode() : 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
