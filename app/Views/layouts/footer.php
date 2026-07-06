</main>

<footer class="site-footer">
    <div class="wrap">
        &copy; <?= date('Y') ?> <?= htmlspecialchars($_ENV['APP_NAME'] ?? __('site_name'), ENT_QUOTES, 'UTF-8') ?> — <?= __('copyright') ?>
    </div>
</footer>

<?php
// تفعيل Cache Busting للـ JS (نفس الفكرة)
$jsFile = BASE_PATH . '/public/assets/js/app.js';
$jsVer  = file_exists($jsFile) ? filemtime($jsFile) : '1.0';
?>
<script src="/assets/js/app.js?v=<?= $jsVer ?>"></script>

</body>
</html>