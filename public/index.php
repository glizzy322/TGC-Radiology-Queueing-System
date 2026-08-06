<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap/app.php';

// Route the request
$router = new \App\Core\Router();
require dirname(__DIR__) . '/routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the app is not served from the root domain, remove the base path
$scriptName = dirname($_SERVER['SCRIPT_NAME']); // e.g. /TGC-Radiology-Queueing-System/public
// Check if the requestURI starts with the script directory
if ($scriptName !== '/' && $scriptName !== '\\' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
} else {
    // Check if the root directory of the project is in the URI (e.g. they didn't include /public in the URL but .htaccess rewrote it)
    $projectDir = dirname($scriptName); // e.g. /TGC-Radiology-Queueing-System
    if ($projectDir !== '/' && $projectDir !== '\\' && strpos($requestUri, $projectDir) === 0) {
        $requestUri = substr($requestUri, strlen($projectDir));
    }
}

// Ensure the URI always starts with a slash if it became empty
if ($requestUri === '' || $requestUri === false) {
    $requestUri = '/';
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestMethod, $requestUri);
