<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/Controllers/PublicDisplayController.php';

(new \App\Controllers\PublicDisplayController())->show();
