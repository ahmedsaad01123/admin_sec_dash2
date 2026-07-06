<?php

declare(strict_types=1);

/**
 * ==========================================================
 *  معالج تثبيت النظام (Installer)
 *  الاستخدام: افتح الرابط /install مرة واحدة بعد رفع المشروع
 *  ملحوظة أمان مهمة: احذف مجلد install بالكامل (أو غيّر اسمه)
 *  فور ما تخلص التثبيت عشان محدش يقدر يشغّله تاني أو يعيد التثبيت.
 * ==========================================================
 */

// جذر المشروع (مجلدين لفوق من install/index.php => public/install => public => الجذر)
define('BASE_PATH', dirname(__DIR__, 2));

$lockFile = BASE_PATH . '/storage/installed.lock';

// لو النظام اتثبت قبل كده، امنع إعادة التثبيت
if (is_file($lockFile)) {
    http_response_code(403);
    die('
        <div style="font-family:Tahoma,sans-serif;max-width:600px;margin:80px auto;text-align:center;color:#374151">
            <h2>النظام متثبت بالفعل ✅</h2>
            <p>لو عايز تعيد التثبيت، احذف الملف: <code>storage/installed.lock</code> يدويًا من السيرفر.</p>
            <p style="color:#dc2626">وننصحك بشدة إنك تحذف مجلد install بالكامل دلوقتي لو لسه موجود.</p>
        </div>
    ');
}

$step = $_POST['step'] ?? ($_GET['step'] ?? '1');
$errors = [];
$success = null;

/**
 * فحص متطلبات السيرفر
 */
function checkRequirements(): array
{
    $checks = [];

    $checks[] = [
        'label' => 'إصدار PHP 8.4 أو أحدث',
        'pass'  => version_compare(PHP_VERSION, '8.4.0', '>='),
        'note'  => 'الإصدار الحالي: ' . PHP_VERSION,
    ];

    $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'session'];
    foreach ($extensions as $ext) {
        $checks[] = [
            'label' => "إضافة PHP: {$ext}",
            'pass'  => extension_loaded($ext),
            'note'  => extension_loaded($ext) ? 'موجودة' : 'غير مفعّلة على السيرفر',
        ];
    }

    $checks[] = [
        'label' => 'إمكانية الكتابة في مجلد storage/logs',
        'pass'  => is_writable(BASE_PATH . '/storage/logs'),
        'note'  => BASE_PATH . '/storage/logs',
    ];

    $checks[] = [
        'label' => 'إمكانية الكتابة في جذر المشروع (لإنشاء ملف .env)',
        'pass'  => is_writable(BASE_PATH),
        'note'  => BASE_PATH,
    ];

    return $checks;
}

/**
 * توليد باسورد عشوائي قوي وسهل القراءة (من غير حروف ملخبطة زي O و 0)
 */
function generateRandomPassword(int $length = 12): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
    $password = '';

    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $password;
}

/**
 * كتابة ملف .env تلقائيًا بالبيانات المدخلة
 */
function writeEnvFile(array $data): bool
{
    $appKey = bin2hex(random_bytes(16));

    $content = <<<ENV
APP_NAME="{$data['app_name']}"
APP_ENV=production
APP_DEBUG=false
APP_URL={$data['app_url']}
APP_KEY={$appKey}

DB_HOST={$data['db_host']}
DB_PORT={$data['db_port']}
DB_DATABASE={$data['db_database']}
DB_USERNAME={$data['db_username']}
DB_PASSWORD={$data['db_password']}
DB_CHARSET=utf8mb4

SESSION_LIFETIME=120
SESSION_SECURE=false

ADMIN_PREFIX=admin
ENV;

    return (bool) file_put_contents(BASE_PATH . '/.env', $content);
}

/**
 * تنفيذ ملف database/schema.sql على قاعدة البيانات
 */
function runSchema(PDO $pdo): void
{
    $sqlPath = BASE_PATH . '/database/schema.sql';

    if (!is_file($sqlPath)) {
        throw new RuntimeException('ملف database/schema.sql غير موجود.');
    }

    $sql = file_get_contents($sqlPath);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if ($statement !== '') {
            $pdo->exec($statement);
        }
    }
}

$requirements = checkRequirements();
$allPassed = !in_array(false, array_column($requirements, 'pass'), true);

/**
 * معالجة إرسال بيانات قاعدة البيانات وتنفيذ التثبيت الفعلي
 */
if ($step === '2' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $appName    = trim($_POST['app_name'] ?? 'MyApp');
    $appUrl     = trim($_POST['app_url'] ?? '');
    $dbHost     = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort     = trim($_POST['db_port'] ?? '3306');
    $dbName     = trim($_POST['db_database'] ?? '');
    $dbUser     = trim($_POST['db_username'] ?? '');
    $dbPass     = (string) ($_POST['db_password'] ?? '');
    $adminUser  = trim($_POST['admin_username'] ?? 'admin');
    $adminEmail = trim($_POST['admin_email'] ?? '');

    if ($appUrl === '' || $dbName === '' || $dbUser === '') {
        $errors[] = 'من فضلك املأ كل الحقول المطلوبة.';
    }

    if (empty($errors)) {
        try {
            // الاتصال بدون تحديد قاعدة بيانات الأول عشان ننشئها لو مش موجودة
            $pdo = new PDO(
                "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4",
                $dbUser,
                $dbPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");

            runSchema($pdo);

            $generatedPassword = generateRandomPassword(12);
            $hashedPassword = password_hash($generatedPassword, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                'INSERT INTO admin_users (username, email, password) VALUES (:username, :email, :password)'
            );
            $stmt->execute([
                'username' => $adminUser,
                'email'    => $adminEmail !== '' ? $adminEmail : null,
                'password' => $hashedPassword,
            ]);

            writeEnvFile([
                'app_name'    => $appName,
                'app_url'     => $appUrl,
                'db_host'     => $dbHost,
                'db_port'     => $dbPort,
                'db_database' => $dbName,
                'db_username' => $dbUser,
                'db_password' => $dbPass,
            ]);

            // قفل التثبيت عشان محدش يشغّله تاني
            file_put_contents(BASE_PATH . '/storage/installed.lock', date('Y-m-d H:i:s'));

            $success = [
                'admin_username' => $adminUser,
                'admin_password' => $generatedPassword,
            ];
        } catch (Throwable $e) {
            $errors[] = 'حصل خطأ أثناء التثبيت: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت النظام</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f6f8; margin: 0; color: #1f2937; }
        .container { max-width: 640px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        h1 { font-size: 22px; margin-top: 0; }
        .check { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .pass { color: #059669; font-weight: 600; }
        .fail { color: #dc2626; font-weight: 600; }
        label { display: block; margin: 14px 0 6px; font-size: 14px; font-weight: 600; }
        input { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
        button { margin-top: 24px; width: 100%; padding: 12px; background: #111827; color: #fff; border: none; border-radius: 8px; font-size: 15px; cursor: pointer; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }
        .error-box { background: #fef2f2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .success-box { background: #ecfdf5; color: #065f46; padding: 20px; border-radius: 10px; text-align: center; }
        .credential { background: #111827; color: #f9fafb; padding: 14px; border-radius: 8px; margin: 16px 0; font-family: monospace; font-size: 15px; }
        .warning { background: #fffbeb; color: #92400e; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-top: 16px; }
    </style>
</head>
<body>
<div class="container">

    <?php if ($success): ?>
        <h1>🎉 تم التثبيت بنجاح</h1>
        <div class="success-box">
            <p>احفظ بيانات دخول الأدمن دي الآن، مش هتظهر تاني:</p>
            <div class="credential">
                اسم المستخدم: <strong><?= htmlspecialchars($success['admin_username'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                كلمة المرور: <strong><?= htmlspecialchars($success['admin_password'], ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
        </div>
        <div class="warning">
            ⚠️ لأسباب أمنية: احذف مجلد <code>install</code> بالكامل من السيرفر دلوقتي،
            وغيّر كلمة المرور دي فور أول تسجيل دخول.
        </div>

    <?php elseif ($step === '2'): ?>
        <h1>بيانات قاعدة البيانات والأدمن</h1>

        <?php foreach ($errors as $error): ?>
            <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="step" value="2">

            <label>اسم التطبيق</label>
            <input type="text" name="app_name" value="<?= htmlspecialchars($_POST['app_name'] ?? 'MyApp', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>رابط الموقع (APP_URL)</label>
            <input type="text" name="app_url" placeholder="https://example.com" value="<?= htmlspecialchars($_POST['app_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>DB Host</label>
            <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? '127.0.0.1', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>DB Port</label>
            <input type="text" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>اسم قاعدة البيانات</label>
            <input type="text" name="db_database" value="<?= htmlspecialchars($_POST['db_database'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>DB Username</label>
            <input type="text" name="db_username" value="<?= htmlspecialchars($_POST['db_username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>DB Password</label>
            <input type="password" name="db_password" value="">

            <label>اسم مستخدم الأدمن</label>
            <input type="text" name="admin_username" value="<?= htmlspecialchars($_POST['admin_username'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>" required>

            <label>بريد الأدمن (اختياري)</label>
            <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit">تثبيت النظام الآن</button>
        </form>

    <?php else: ?>
        <h1>فحص متطلبات السيرفر</h1>

        <?php foreach ($requirements as $check): ?>
            <div class="check">
                <span><?= htmlspecialchars($check['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="<?= $check['pass'] ? 'pass' : 'fail' ?>">
                    <?= $check['pass'] ? '✔ ' . htmlspecialchars($check['note'], ENT_QUOTES, 'UTF-8') : '✘ ' . htmlspecialchars($check['note'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        <?php endforeach; ?>

        <form method="post" action="?step=2">
            <button type="submit" <?= $allPassed ? '' : 'disabled' ?>>
                <?= $allPassed ? 'المتابعة لبيانات قاعدة البيانات' : 'لازم تصلّح المتطلبات الناقصة الأول' ?>
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>