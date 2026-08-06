<?php
namespace App\Controllers;
use App\Repositories\ReportRepository;

class ReportController
{
    private function requireAuth()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_role'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function tickets()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;

        try {
            $repo = new ReportRepository();
            $data = $repo->getTicketsByDateRange($start, $end);
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
