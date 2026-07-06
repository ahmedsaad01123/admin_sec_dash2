<?php
// تحديد اللغة والاتجاه
use App\Core\Language;

$currentLang = Language::current(); // 'ar' أو 'en'
$dir = ($currentLang === 'ar') ? 'rtl' : 'ltr';

// إعداد المتغيرات
$pageTitle = $title ?? 'لوحة التحكم';
$csrfToken = $csrf_token ?? '';
$includeSessionCheck = true;
$includeCSRFRefresh = true;

// تضمين القوالب
include_once BASE_PATH . '/app/Views/templates/header.php';
include_once BASE_PATH . '/app/Views/templates/sidebar.php';
?>

<!-- محتوى الصفحة هنا -->
<div id="container" class="container-fluid">
    <div id="content" class="dashboardPage">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><?= htmlspecialchars($page_title ?? $title ?? 'لوحة التحكم', ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            
            <!-- Language Switcher -->
            <div>
                <form method="post" action="/switch-language" style="display:inline-block;">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" name="lang" value="ar" class="btn btn-sm <?= $currentLang === 'ar' ? 'btn-primary' : 'btn-outline-secondary' ?> me-1">
                        🇸🇦 عربي
                    </button>
                    <button type="submit" name="lang" value="en" class="btn btn-sm <?= $currentLang === 'en' ? 'btn-primary' : 'btn-outline-secondary' ?>">
                        🇬🇧 English
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Content -->
        <?= $content ?? '' ?>
    </div>
</div>

<?php
include_once BASE_PATH . '/app/Views/templates/footer.php';
?>
