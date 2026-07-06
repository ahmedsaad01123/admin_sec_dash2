<?php
// Header Template - يمكن استدعاؤه في أي صفحة
// استخدم: include_once __DIR__ . '/../templates/header.php';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?? 'ar' ?>" data-dir="<?= $dir ?? 'rtl' ?>" class="<?= $dir ?? 'rtl' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $csrfToken ?? '' ?>">
    <meta name="robots" content="noindex,nofollow">
    <title><?= $pageTitle ?? 'لوحة التحكم' ?></title>
    
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="/dist/css/bootstrap.css"/>
    <?php if (($dir ?? 'rtl') === 'rtl'): ?>
    <link rel="stylesheet" href="/dist/css/bootstrap-rtl.min.css"/>
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/dist/css/font-awesome-4.7.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/dist/css/app.css"/>
</head>
<body>
    <div id="notifications">
        <div class="arrowHolder">
            <div class="arrow"></div>
        </div>
        <div class="inner">
            <div class="header">
                <button onclick="window.App.user.notifications.holder.hide()" class="close">
                <span>×</span>
                </button>
                <div>
                    <h4 class="inline">
                    الإشعارات
                    </h4>
                    <a href="#" class="btn btn-xs btn-link margin-before-5">
                    ضبط
                    </a>
                </div>
                <div class="btn-group btn-group-xs">
                    <a href="#" data-toggle="tooltip" title="عرض كافة الإشعارات" class="btn btn-link">
                    <i class="fa fa-bars"></i> عرض الكل
                    </a>
                    <a onclick="window.App.user.notifications.markAllRead(); return false" data-toggle="tooltip" title="جعلها جميع الإشعارات مقروءة" href="" class="markAllRead btn btn-link" style="display: none;">
                    <i class="fa fa-check-square"></i> جعلها جميعاً مقروءة
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="content">
                <div class="noNotification hint style2">
                    ليس لديك أي إشعارات بعد!
                </div>
            </div>
        </div>
    </div>
