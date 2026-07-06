<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

require BASE_PATH . '/vendor/autoload.php';

$config = require BASE_PATH . '/config/config.php';

$app = new \App\Core\App($config);
$app->run();
