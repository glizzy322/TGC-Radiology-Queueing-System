<?php
require dirname(__DIR__) . '/bootstrap/app.php';
if (!preg_match('/^rqs_test_[a-f0-9]{12}$/', getenv('DB_DATABASE') ?: '')) {
    throw new RuntimeException('Worker requires disposable test database');
}
try {
    $result = $argv[1] === 'issue'
        ? (new App\Repositories\TicketRepository())->generateTicket(2, 'UT', 1, 1)
        : (new App\Repositories\QueueRepository())->callNext('ultrasound', 1, 0);
    echo json_encode(['result' => $result]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getCode(), 'message' => $e->getMessage()]);
}
