<?php

declare(strict_types=1);

/*
 * Planned Laragon/MySQL connection settings. The prototype does not yet
 * create a database connection; use environment variables when persistence
 * is implemented.
 */
return [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_DATABASE') ?: 'tgc_radiology_queue',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4',
];
