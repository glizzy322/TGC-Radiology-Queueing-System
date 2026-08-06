<?php

declare(strict_types=1);

if (!isset($router)) {
    return; // To prevent issues if required directly without router
}

$router->get('/', [\App\Controllers\LandingController::class, 'show']);
$router->get('/receptionist', [\App\Controllers\ReceptionController::class, 'show']);
$router->get('/public-display', [\App\Controllers\PublicDisplayController::class, 'show']);
$router->get('/test-db', [\App\Controllers\TestController::class, 'index']);
