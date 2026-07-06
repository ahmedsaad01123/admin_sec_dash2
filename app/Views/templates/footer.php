<?php
// Footer Template - يمكن استدعاؤه في أي صفحة
// استخدم: include_once __DIR__ . '/../templates/footer.php';
?>
    <!-- JavaScript Files -->
    <script src="/dist/js/jquery.min.js"></script>
    <script src="/dist/js/bootstrap.min.js"></script>
    
    <?php if (isset($includeSessionCheck) && $includeSessionCheck): ?>
    <script>
        // Check session integrity every 5 minutes
        setInterval(() => {
            fetch('/session-check')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        window.location.href = '/login?error=session_expired';
                    }
                })
                .catch(console.error);
        }, 5 * 60 * 1000);
    </script>
    <?php endif; ?>
    
    <?php if (isset($includeCSRFRefresh) && $includeCSRFRefresh): ?>
    <script>
        // Auto-refresh CSRF token every 30 minutes
        setInterval(() => {
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    const csrfInput = document.querySelector('input[name="csrf_token"]');
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    if (csrfInput) csrfInput.value = data.token;
                    if (csrfMeta) csrfMeta.setAttribute('content', data.token);
                })
                .catch(console.error);
        }, 30 * 60 * 1000);
    </script>
    <?php endif; ?>
    
    <?php if (isset($customJS)): ?>
        <?= $customJS ?>
    <?php endif; ?>
    
    <!-- Notifications JavaScript -->
    <script src="/dist/js/notifications.js"></script>
</body>
</html>
