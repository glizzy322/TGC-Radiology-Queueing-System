<?php
// Explicit local acceptance test. Creates inactive media and removes only its own ads.
require dirname(__DIR__) . '/bootstrap/app.php';
$base = 'http://127.0.0.1:8000';
$failures = 0;
function result(bool $ok, string $message): void {
    global $failures;
    if (!$ok) $failures++;
    echo ($ok ? 'PASS ' : 'FAIL ') . $message . PHP_EOL;
}
function request($client, string $path, $body = null): array {
    global $base;
    curl_setopt_array($client, [CURLOPT_URL => $base . $path, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 15, CURLOPT_POST => $body !== null, CURLOPT_HTTPHEADER => is_string($body) ? ['Content-Type: application/json'] : []]);
    if ($body !== null) curl_setopt($client, CURLOPT_POSTFIELDS, $body);
    $text = curl_exec($client);
    if ($text === false) throw new RuntimeException(curl_error($client));
    return [curl_getinfo($client, CURLINFO_RESPONSE_CODE), $text, json_decode($text, true)];
}
function login(string $name) {
    $client = curl_init();
    curl_setopt($client, CURLOPT_COOKIEFILE, '');
    [$status] = request($client, '/login', ['name' => $name, 'password' => 'password']);
    result($status === 302, "$name login");
    return $client;
}
$admin = login('System Admin');
$rooms = ['X-Ray 1' => ['xray', 0], 'X-Ray 2' => ['xray', 1], 'Ultrasound 1' => ['ultrasound', 0], 'Ultrasound 2' => ['ultrasound', 1], 'CT Scan' => ['ctscan', 0]];
foreach ($rooms as $name => [$key, $slot]) {
    $client = login($name);
    [$status, $page] = request($client, '/dashboard');
    result($status === 200 && str_contains($page, 'const myRoom = ' . json_encode(['key' => $key, 'slot' => $slot])), "$name dashboard uses assigned room");
    [$status, $body] = request($client, '/api/queue/call', json_encode(['procedure_key' => $key === 'xray' ? 'ctscan' : 'xray', 'slot_index' => 0]));
    result($status === 403, "$name cannot call another room through API ($status: $body)");
    curl_close($client);
}
$client = login('X-Ray 1');
[$status] = request($client, '/api/queue/call', json_encode(['procedure_key' => 'xray', 'slot_index' => 1]));
result($status === 403, 'X-Ray 1 cannot use X-Ray 2 slot');
[$status] = request($client, '/api/queue/call', json_encode(['procedure_key' => 'xray', 'slot_index' => -1]));
result($status === 400, 'Invalid slot rejected');
curl_close($client);
$testTicket = $argv[1] ?? null;
if ($testTicket) {
    $client = login('X-Ray 1');
    [$status] = request($client, '/api/queue/call', json_encode(['procedure_key' => 'xray', 'slot_index' => 0]));
    result($status === 409, 'Occupied X-Ray 1 slot rejected by server');
    curl_close($client);
    $client = login('X-Ray 2');
    [$status] = request($client, '/api/queue/complete', json_encode(['ticket_code' => $testTicket]));
    result($status === 403, 'Other room cannot complete the test ticket');
    curl_close($client);
}
$files = [];
$ads = [];
try {
    $image = imagecreatetruecolor(32, 32);
    foreach (['jpg' => 'imagejpeg', 'png' => 'imagepng', 'gif' => 'imagegif', 'webp' => 'imagewebp'] as $ext => $writer) {
        $path = dirname(__DIR__) . '/storage/logs/acceptance-' . bin2hex(random_bytes(6)) . '.' . $ext;
        $files[] = $path;
        $writer($image, $path);
        [$status, $body, $data] = request($admin, '/api/ads', ['media' => new CURLFile($path), 'active' => 'false', 'duration' => '5']);
        result($status === 200 && ($data['status'] ?? '') === 'success', "$ext upload: $status");
        if (isset($data['data']['id'])) $ads[] = $data['data']['id'];
        if ($status !== 200) echo substr($body, 0, 250) . PHP_EOL;
    }
    [$status, , $data] = request($admin, '/api/ads', ['youtube_url' => 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', 'youtube_live' => 'true', 'active' => 'false']);
    result($status === 200 && ($data['data']['is_live'] ?? 0) == 1, 'YouTube live setting persisted');
    if (isset($data['data']['id'])) $ads[] = $data['data']['id'];
    foreach (['fake.jpg', 'shell.php', 'fake.mp4', 'fake.webm'] as $name) {
        [$status] = request($admin, '/api/ads', ['media' => new CURLFile(__FILE__, 'application/octet-stream', $name), 'active' => 'false']);
        result($status === 400, "Reject disguised/unsupported $name");
    }
    foreach (['https://www.youtube.com/watch?v=aqz-KE-bpKQ' => 200, 'http://youtube.com/watch?v=test' => 400, 'https://youtube.com.evil.example/watch?v=test' => 400, 'https://example.com/video' => 400] as $url => $expected) {
        [$status, , $data] = request($admin, '/api/ads', ['youtube_url' => $url, 'active' => 'false', 'duration' => '5']);
        result($status === $expected, 'YouTube URL validation: ' . $url);
        if (isset($data['data']['id'])) $ads[] = $data['data']['id'];
    }
} finally {
    foreach ($ads as $id) request($admin, '/api/ads/delete', json_encode(['id' => $id]));
    foreach ($files as $file) if (is_file($file)) unlink($file);
    curl_close($admin);
}
exit($failures ? 1 : 0);
