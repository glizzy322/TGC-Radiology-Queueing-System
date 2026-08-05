<?php

declare(strict_types=1);

namespace App\Controllers;

final class LandingController
{
    public function show(): void
    {
        require dirname(__DIR__, 2) . '/resources/views/landing/index.php';
    }
}
