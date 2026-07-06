document.addEventListener('DOMContentLoaded', function () {
    const pageTypeSelect = document.getElementById('pageType');
    const modelCheckbox = document.getElementById('createModel');
    const middlewareCheckbox = document.getElementById('createMiddleware');

    function updateCheckboxes() {
        const selected = pageTypeSelect.value;

        // تفعيل تلقائي حسب النوع
        if (selected === 'form') {
            modelCheckbox.checked = true;
            // نترك middleware على حاله (لا نغيره)
        } else if (selected === 'protected') {
            middlewareCheckbox.checked = true;
            // نترك model على حاله (لا نغيره)
        }
        // normal: لا نفعّل أي شيء تلقائياً، ونحترم اختيار المستخدم
    }

    pageTypeSelect.addEventListener('change', updateCheckboxes);
    updateCheckboxes(); // تشغيل عند التحميل
});
