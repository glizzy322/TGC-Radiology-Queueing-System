<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProcedureService;
use App\Repositories\CategoryRepository;
use App\Services\AuthService;

final class DashboardController
{
    public function show(): void
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }

        $userRole = $_SESSION['user_role'] ?? 'unknown';
        $userName = $_SESSION['user_name'] ?? 'Unknown User';

        $assignment = \App\Core\Database::getConnection()->prepare('SELECT assigned_procedure, assigned_slot FROM staff_users WHERE id = ?');
        $assignment->execute([$_SESSION['user_id']]);
        $assigned = $assignment->fetch(\PDO::FETCH_ASSOC);
        $assignedRoom = $userRole !== 'administrator' && !empty($assigned['assigned_procedure'])
            ? ['key' => $assigned['assigned_procedure'], 'slot' => (int)$assigned['assigned_slot']]
            : null;

        $procedureService = new ProcedureService();
        $categoryRepo = new CategoryRepository();

        $procedures = $procedureService->getAllProcedures();
        $categories = $categoryRepo->getAll();

        // Variables extracted here will be available in the view
        extract([
            'procedures' => $procedures,
            'categories' => $categories,
            'userRole' => $userRole,
            'userName' => $userName
        ]);

        require dirname(__DIR__, 2) . '/resources/views/dashboard/index.php';
    }
}
