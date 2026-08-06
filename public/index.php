<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

// Route the request
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestMethod, $requestUri);
