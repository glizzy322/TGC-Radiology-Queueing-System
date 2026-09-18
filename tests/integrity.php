<?php
// Uses only a newly created, randomly named disposable database.
require dirname(__DIR__) . '/bootstrap/app.php';
$config = require dirname(__DIR__) . '/config/database.php';
$db = new PDO("mysql:host={$config['host']};port={$config['port']};charset=utf8mb4", $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$name = 'rqs_test_' . bin2hex(random_bytes(6));
$db->exec("CREATE DATABASE `$name`");
function check(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); echo "PASS $message\n"; }
function rejected(callable $action, int $code): void {
    try { $action(); } catch (Exception $e) { check($e->getCode() === $code, 'Expected rejection ' . $code); return; }
    throw new RuntimeException('Expected rejection');
}
try {
    putenv('DB_DATABASE=' . $name);
    $db->exec("USE `$name`");
    $files = in_array('--numbered', $argv, true)
        ? ['01_core_and_users.sql', '02_queue_and_tracking.sql', '03_ads_and_audit.sql', '04_seed_data.sql', '05_seed_staff.sql']
        : ['init_database.sql'];
    foreach ($files as $file) $db->exec(file_get_contents(dirname(__DIR__) . '/database/migrations/' . $file));
    $db->exec(file_get_contents(dirname(__DIR__) . '/database/migrations/06_queue_integrity.sql'));
    $db->exec(file_get_contents(dirname(__DIR__) . '/database/migrations/07_youtube_live_ads.sql'));
    check(true, 'Clean schema and repeatable upgrade migration');
    $repo = new App\Repositories\TicketRepository();
    $queue = new App\Repositories\QueueRepository();
    $staff = $db->query("SELECT id FROM staff_users WHERE name = 'X-Ray 1'")->fetchColumn();
    $issuer = $db->query("SELECT id FROM staff_users WHERE name = 'Receptionist User'")->fetchColumn();
    $db->exec("INSERT INTO queue_tickets(ticket_code,procedure_id,category_id,issuer_id,created_at) VALUES ('XRAY-001',1,1,1,CURRENT_TIMESTAMP - INTERVAL 1 DAY)");
    $first = $repo->generateTicket(1, 'FORGED', 1, $issuer);
    check($first['ticket_code'] === 'XRAY-' . date('Ymd') . '-001', 'Date-qualified identifier and server-owned prefix');
    check((int)$first['issuer_id'] === (int)$issuer, 'Authenticated issuer persisted');
    rejected(fn() => $repo->generateTicket(1, 'XRAY', 1, $staff), 403);
    $second = $repo->generateTicket(1, 'XRAY', 1, $issuer);
    check($second['ticket_code'] !== $first['ticket_code'], 'Sequential unique tickets');
    rejected(fn() => $queue->callNext('xray', $staff, 1), 403);
    rejected(fn() => $queue->callNext('xray', $staff, -1), 400);
    $called = $queue->callNext('xray', $staff, 0);
    check($called['ticket_code'] === $first['ticket_code'], 'Assigned room can call');
    rejected(fn() => $queue->callNext('xray', $staff, 0), 409);
    $other = $db->query("SELECT id FROM staff_users WHERE name = 'X-Ray 2'")->fetchColumn();
    rejected(fn() => $queue->completeTicket($called['ticket_code'], $other), 403);
    check($queue->completeTicket($called['ticket_code'], $staff), 'Assigned room can complete');
    check($queue->callNext('xray', $staff, 0)['ticket_code'] === $second['ticket_code'], 'Completion releases slot');
    check(!App\Services\MediaValidation::youtubeUrl('https://youtube.com.evil.test/watch?v=123'), 'Reject spoofed YouTube host');
    check(!App\Services\MediaValidation::youtubeUrl('javascript:alert(1)'), 'Reject unsafe URL scheme');
    check(App\Services\MediaValidation::youtubeUrl('https://youtu.be/abcdefghijk'), 'Accept YouTube URL');
    $image = dirname(__DIR__) . '/public/images/slide1.jpg';
    check(App\Services\MediaValidation::extension($image, 'photo.jpg') === 'jpg', 'Accept genuine image');
    rejected(fn() => App\Services\MediaValidation::extension($image, 'shell.php'), 0);
    rejected(fn() => App\Services\MediaValidation::extension(__FILE__, 'fake.jpg'), 0);
    foreach (['issue', 'call'] as $action) {
        $workers = [];
        for ($i = 0; $i < 2; $i++) {
            $process = proc_open([PHP_BINARY, __DIR__ . '/integrity-worker.php', $action], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            $workers[] = [$process, $pipes];
        }
        $results = [];
        foreach ($workers as [$process, $pipes]) {
            $results[] = json_decode(stream_get_contents($pipes[1]), true);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]); fclose($pipes[2]);
            check(proc_close($process) === 0 && $error === '', 'Concurrent worker completed');
        }
        if ($action === 'issue') {
            check(isset($results[0]['result']['ticket_code'], $results[1]['result']['ticket_code']) &&
                $results[0]['result']['ticket_code'] !== $results[1]['result']['ticket_code'], 'Concurrent first tickets have unique codes');
        } else {
            check(count(array_filter($results, fn($r) => isset($r['result']['id']))) === 1 &&
                count(array_filter($results, fn($r) => ($r['error'] ?? null) === 409)) === 1, 'Concurrent calls cannot occupy the same slot');
        }
    }
} finally {
    $db->exec("DROP DATABASE `$name`");
}
