<?php

declare(strict_types=1);

/**
 * ==========================================================
 *  إدارة الصفحات (Pages Manager)
 *  عرض جميع الصفحات الموجودة وحذفها
 * ==========================================================
 */

// منع التشغيل في بيئة الإنتاج
$envFile = dirname(__DIR__, 2) . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_ENV=production') !== false) {
        die('⚠️ تم تعطيل أداة إدارة الصفحات في بيئة الإنتاج.');
    }
}

define('BASE_PATH', dirname(__DIR__, 2));
define('PAGES_MANAGER_PATH', __DIR__);

// تحميل المكتبات المساعدة
require_once PAGES_MANAGER_PATH . '/src/PageScanner.php';
require_once PAGES_MANAGER_PATH . '/src/PageDeleter.php';

use PagesManager\PageScanner;
use PagesManager\PageDeleter;

$message = '';
$error = '';
$pages = [];

// ==========================================================
//  معالجة طلب حذف صفحة
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_page') {
    
    $pageName = trim($_POST['page_name'] ?? '');
    $deleteModel = isset($_POST['delete_model']);
    $deleteMiddleware = isset($_POST['delete_middleware']);
    
    if (empty($pageName)) {
        $error = '❌ اسم الصفحة غير صحيح.';
    } else {
        try {
            $result = PageDeleter::delete($pageName, $deleteModel, $deleteMiddleware);
            if ($result['success']) {
                $message = "✅ تم حذف الصفحة <strong>{$pageName}</strong> بنجاح!";
                if (!empty($result['deleted_files'])) {
                    $message .= "<br>📄 تم حذف الملفات: <code>" . implode('</code>, <code>', $result['deleted_files']) . "</code>";
                }
                if (!empty($result['errors'])) {
                    $message .= "<br>⚠️ بعض الملفات لم تُحذف: <code>" . implode('</code>, <code>', $result['errors']) . "</code>";
                }
            } else {
                $error = "❌ " . $result['message'];
            }
        } catch (Throwable $e) {
            $error = "❌ حدث خطأ: " . $e->getMessage();
        }
    }
}

// ==========================================================
//  مسح الصفحات الموجودة
// ==========================================================
$pages = PageScanner::scan();
$totalPages = count($pages);

// عرض الصفحة
include PAGES_MANAGER_PATH . '/views/main.php';