<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
|--------------------------------------------------------------------------
| قراءة ملف .env
|--------------------------------------------------------------------------
*/

$envFile = dirname(__DIR__, 3) . '/.env';

if (!file_exists($envFile)) {
    exit('<h3 style="color:red">❌ ملف .env غير موجود.</h3>');
}

$env = [];

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {

    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    if (!str_contains($line, '=')) {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);

    $env[trim($key)] = trim($value, "\"'");
}

/*
|--------------------------------------------------------------------------
| الاتصال بقاعدة البيانات
|--------------------------------------------------------------------------
*/

try {

    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $env['DB_HOST'],
            $env['DB_PORT'] ?? '3306',
            $env['DB_DATABASE'],
            $env['DB_CHARSET'] ?? 'utf8mb4'
        ),
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
        ]
    );

} catch (PDOException $e) {

    exit(
        '<h2 style="color:red">❌ فشل الاتصال بقاعدة البيانات</h2>
        <pre>' . htmlspecialchars($e->getMessage()) . '</pre>'
    );
}

/*
|--------------------------------------------------------------------------
| قراءة schema.sql
|--------------------------------------------------------------------------
*/

$sqlFile = __DIR__ . '/schema.sql';

if (!file_exists($sqlFile)) {
    exit('<h3 style="color:red">❌ ملف schema.sql غير موجود.</h3>');
}

$sql = file_get_contents($sqlFile);

if ($sql === false || trim($sql) === '') {
    exit('<h3 style="color:red">❌ ملف schema.sql فارغ.</h3>');
}

/*
|--------------------------------------------------------------------------
| تنفيذ schema.sql
|--------------------------------------------------------------------------
*/

try {

    $pdo->exec($sql);

    echo '
    <div style="
        max-width:700px;
        margin:40px auto;
        padding:20px;
        border:1px solid #28a745;
        background:#eafaf1;
        color:#155724;
        border-radius:8px;
        font-family:Tahoma;
    ">
        <h2>✅ تم تنفيذ schema.sql بنجاح.</h2>
    </div>';
    echo '
    <div style="max-width:700px;margin:50px auto;padding:20px;border:1px solid #28a745;background:#eafaf1;color:#155724;border-radius:8px;font-family:Tahoma;">
        <p style="color:#721c24;background:#f8d7da;padding:10px;border-radius:4px;">
            ⚠️ <strong>تذكير أمني:</strong> احذف هذا المجلد فوراً من السيرفر.
        </p>
    </div>
    ';    

} catch (PDOException $e) {

    echo '
    <div style="
        max-width:900px;
        margin:40px auto;
        padding:20px;
        border:1px solid #dc3545;
        background:#fff5f5;
        color:#721c24;
        border-radius:8px;
        font-family:Tahoma;
    ">
        <h2>❌ حدث خطأ أثناء تنفيذ schema.sql</h2>

        <pre style="
            background:#f8f9fa;
            padding:15px;
            border:1px solid #ddd;
            overflow:auto;
        ">' . htmlspecialchars($e->getMessage()) . '</pre>
    </div>';
}