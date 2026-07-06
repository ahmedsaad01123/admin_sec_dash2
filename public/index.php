<?php

declare(strict_types=1);

/**
 * =====================================================================
 *  Front Controller
 *  كل الطلبات القادمة للتطبيق بتمر من هنا أولاً (بفضل .htaccess)
 * =====================================================================
 */

// مسار المشروع الرئيسي (فوق مجلد public مباشرة)
define('BASE_PATH', dirname(__DIR__));

// تحميل الـ Autoloader الخاص بينا (بديل بسيط عن composer autoload)
require BASE_PATH . '/app/Core/Autoloader.php';

// تحميل الدوال المساعدة
require_once BASE_PATH . '/app/Core/helpers.php';

use App\Core\App;
use App\Core\Autoloader;
use App\Core\Env;

// تسجيل الـ Autoloader عشان يقدر يحمّل كل الكلاسات تلقائيًا
(new Autoloader())->register();

// تحميل متغيرات البيئة من ملف .env
Env::load(BASE_PATH . '/.env');

// تحميل إعدادات التطبيق
$config = require BASE_PATH . '/config/config.php';

// ضبط عرض الأخطاء حسب البيئة (منعًا لتسريب معلومات حساسة في بيئة الإنتاج)
if ($config['app']['debug'] === true) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

date_default_timezone_set('Africa/Cairo');

// تشغيل التطبيق
$app = new App($config);
$app->run();
