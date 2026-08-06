<?php

namespace App\Controllers;

use App\Services\ProcedureService;

class TestController
{
    public function index()
    {
        $service = new ProcedureService();
        $procedures = $service->getAllProcedures();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'data' => $procedures]);
    }
}
