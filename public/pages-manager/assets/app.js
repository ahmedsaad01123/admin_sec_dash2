function confirmDelete(pageName, hasModel, hasMiddleware) {
    const modal = document.getElementById('deleteModal');
    const pageNameSpan = document.getElementById('deletePageName');
    const pageInput = document.getElementById('deletePageInput');
    const modelCheck = document.getElementById('deleteModelCheck');
    const middlewareCheck = document.getElementById('deleteMiddlewareCheck');
    const modelInput = document.getElementById('deleteModelInput');
    const middlewareInput = document.getElementById('deleteMiddlewareInput');

    // تعيين اسم الصفحة
    pageNameSpan.textContent = pageName;
    pageInput.value = pageName;

    // إظهار/إخفاء خيارات الحذف الإضافية
    document.getElementById('deleteOptions').style.display = (hasModel || hasMiddleware) ? 'block' : 'none';
    
    if (hasModel) {
        modelCheck.style.display = 'inline-block';
        modelCheck.checked = true;
        modelCheck.disabled = false;
    } else {
        modelCheck.style.display = 'none';
        modelCheck.checked = false;
        modelCheck.disabled = true;
    }

    if (hasMiddleware) {
        middlewareCheck.style.display = 'inline-block';
        middlewareCheck.checked = true;
        middlewareCheck.disabled = false;
    } else {
        middlewareCheck.style.display = 'none';
        middlewareCheck.checked = false;
        middlewareCheck.disabled = true;
    }

    // تحديث الـ hidden inputs عند تغيير الـ checkboxes
    modelCheck.onchange = function() {
        modelInput.value = this.checked ? '1' : '0';
    };
    middlewareCheck.onchange = function() {
        middlewareInput.value = this.checked ? '1' : '0';
    };

    // إعادة تعيين القيم الحالية
    modelInput.value = '1';
    middlewareInput.value = '1';

    // عرض المودال
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// إغلاق المودال بالضغط على ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// إغلاق المودال بالضغط خارج المحتوى
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});