<?php

declare(strict_types=1);

namespace App\Controllers;

final class ReceptionController
{
    public function show(): void
    {
        require dirname(__DIR__, 2) . '/resources/views/receptionist/index.php';
    }
}
