<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Controllers/LandingController.php';

(new \App\Controllers\LandingController())->show();
