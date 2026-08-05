<?php

declare(strict_types=1);

return [
    'name' => 'TGC Radiology Queueing System',
    'environment' => getenv('APP_ENV') ?: 'local',
    'base_url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Manila',
];
