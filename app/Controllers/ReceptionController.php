<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProcedureService;
use App\Repositories\CategoryRepository;

final class ReceptionController
{
    public function show(): void
    {
        $procedureService = new ProcedureService();
        $categoryRepo = new CategoryRepository();

        $procedures = $procedureService->getAllProcedures();
        $categories = $categoryRepo->getAll();

        // Variables extracted here will be available in the view
        extract([
            'procedures' => $procedures,
            'categories' => $categories
        ]);

        require dirname(__DIR__, 2) . '/resources/views/receptionist/index.php';
    }
}
