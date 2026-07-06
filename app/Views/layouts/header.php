<?php
// تحديد اللغة والاتجاه تلقائياً
use App\Core\Language;

$currentLang = Language::current(); // 'ar' أو 'en'
$dir = ($currentLang === 'ar') ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? __('site_name'), ENT_QUOTES, 'UTF-8') ?></title>

    <?php
    $cssFile = BASE_PATH . '/public/assets/css/app.css';
    $jsFile  = BASE_PATH . '/public/assets/js/app.js';
    $cssVer  = file_exists($cssFile) ? filemtime($cssFile) : '1.0';
    $jsVer   = file_exists($jsFile) ? filemtime($jsFile) : '1.0';
    ?>

    <link rel="stylesheet" href="/assets/css/app.css?v=<?= $cssVer ?>">
    <?php if ($dir === 'rtl'): ?>
    <link rel="stylesheet" href="/assets/css/app-rtl.css?v=<?= $cssVer ?>">
    <?php endif; ?>
</head>
<body>

<header class="site-header">
    <div class="wrap">
        <div class="logo">
            <a href="/"><?= htmlspecialchars($_ENV['APP_NAME'] ?? __('site_name'), ENT_QUOTES, 'UTF-8') ?></a>
        </div>
        <nav>
            <a href="/"><?= __('home') ?></a>

            <!-- تبديل اللغة -->
            <form method="post" action="/switch-language" style="display:inline-block; margin-inline-start: 16px;">
                <input type="hidden" name="_csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <button type="submit" name="lang" value="ar" style="background:transparent; border:none; color:#d1d5db; cursor:pointer; font-size:14px; padding:0 4px;">
                    🇸🇦 عربي
                </button>
                <span style="color:#4b5563;">|</span>
                <button type="submit" name="lang" value="en" style="background:transparent; border:none; color:#d1d5db; cursor:pointer; font-size:14px; padding:0 4px;">
                    🇬🇧 English
                </button>
            </form>

            <!-- ===== زر الوضع الليلي ===== -->
            <button id="darkModeToggle" style="background:transparent; border:none; color:#d1d5db; cursor:pointer; font-size:20px; margin-inline-start:16px;" aria-label="تبديل الوضع الليلي">
                🌙
            </button>
        </nav>
    </div>
</header>

<main>