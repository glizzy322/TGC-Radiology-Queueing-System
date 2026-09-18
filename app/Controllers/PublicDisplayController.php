<?php

declare(strict_types=1);

namespace App\Controllers;

final class PublicDisplayController
{
    public function show(): void
    {
        header('Referrer-Policy: strict-origin-when-cross-origin');
        require dirname(__DIR__, 2) . '/resources/views/public-display/index.php';
    }
}
