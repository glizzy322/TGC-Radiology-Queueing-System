<?php

declare(strict_types=1);

if (!isset($router)) {
    return; // To prevent issues if required directly without router
}

$router->get('/', [\App\Controllers\LandingController::class, 'show']);
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'show']);
$router->get('/public-display', [\App\Controllers\PublicDisplayController::class, 'show']);
$router->get('/test-db', [\App\Controllers\TestController::class, 'index']);
$router->post('/api/tickets', [\App\Controllers\TicketController::class, 'store']);
$router->get('/api/queue', [\App\Controllers\QueueController::class, 'state']);
$router->post('/api/queue/call', [\App\Controllers\QueueController::class, 'call']);
$router->post('/api/queue/complete', [\App\Controllers\QueueController::class, 'complete']);
$router->get('/api/reports/tickets', [\App\Controllers\ReportController::class, 'tickets']);
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'processLogin']);
$router->post('/logout', [\App\Controllers\AuthController::class, 'logout']);
