<?php

declare(strict_types=1);

/**
 * ==========================================================
 *  أدوات المطور (Developer Tools) - نقطة الدخول
 *  يدعم: صفحات عادية، صفحات بنماذج، صفحات محمية
 * ==========================================================
 */

define('BASE_PATH', dirname(__DIR__, 2));

require __DIR__ . '/src/Translator.php';
require __DIR__ . '/src/RouteManager.php';
require __DIR__ . '/src/Generator.php';

use DevTools\Generator;

// منع التشغيل في بيئة الإنتاج
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_ENV=production') !== false) {
        die('⚠️ تم تعطيل أدوات المطور في بيئة الإنتاج. غيّر APP_ENV=local في .env لتشغيلها.');
    }
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_page') {
    [$message, $error] = Generator::createPage($_POST, BASE_PATH);
}

require __DIR__ . '/views/main.php';
