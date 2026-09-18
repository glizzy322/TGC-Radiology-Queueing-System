<?php
require dirname(__DIR__) . '/bootstrap/app.php';
if (isset($argv[1])) {
    session_save_path(dirname(__DIR__) . '/storage/logs');
    session_start();
    $_SESSION = [];
    if ($argv[1] !== 'anonymous') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'administrator';
        $_SESSION['playback_token'] = 'expected-token';
    }
    if ($argv[1] === 'wrong-token') $_SERVER['HTTP_X_PLAYBACK_TOKEN'] = 'wrong-token';
    register_shutdown_function(function () { fwrite(STDERR, (string)http_response_code()); session_destroy(); });
    (new App\Controllers\AdvertisementController())->playback();
    exit;
}
foreach (['anonymous', 'missing-token', 'wrong-token'] as $case) {
    $process = proc_open([PHP_BINARY, __FILE__, $case], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    $body = json_decode(stream_get_contents($pipes[1]), true);
    $status = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    if (proc_close($process) !== 0 || $status !== '403' || empty($body['error'])) throw new RuntimeException('Failed ' . $case);
    echo "PASS Playback rejects $case\n";
}
