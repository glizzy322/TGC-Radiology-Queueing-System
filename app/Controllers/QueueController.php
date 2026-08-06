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
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
