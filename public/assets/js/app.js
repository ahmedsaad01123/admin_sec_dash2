/**
 * الملف الرئيسي للـ JavaScript الخاص بالواجهة الأمامية
 * يمكنك إضافة أي تفاعلات أو استدعاءات AJAX هنا
 */
document.addEventListener('DOMContentLoaded', function() {
    //console.log('✅ النظام جاهز - MVC Structure with Assets');
    
    // مثال: إضافة تأثير ترحيبي بسيط (يمكنك حذفه)
    const badge = document.querySelector('.badge');
    if (badge) {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.2s';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }
});

// =============================================
//  Dark Mode Toggle
// =============================================
(function() {
    const toggle = document.getElementById('darkModeToggle');
    if (!toggle) return;

    // استرجاع الحالة المحفوظة
    const currentMode = localStorage.getItem('darkMode');
    if (currentMode === 'enabled') {
        document.documentElement.classList.add('dark');
        toggle.textContent = '☀️';
    }

    // حدث الضغط على الزر
    toggle.addEventListener('click', function() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        this.textContent = isDark ? '☀️' : '🌙';
    });
})();