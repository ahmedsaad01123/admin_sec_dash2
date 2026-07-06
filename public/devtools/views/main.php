<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أدوات المطور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-icon">🛠️</div>
        <div>
            <h1>أدوات المطور</h1>
            <div class="subtitle">Developer Tools</div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <span class="alert-icon">✅</span>
            <div><?= $message ?></div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span>
            <div><?= $error ?></div>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="create_page">

        <div class="form-group">
            <label>
                إنشاء صفحة جديدة
                <span class="label-hint">(Controller + View + Route)</span>
            </label>
            <div class="input-wrapper">
                <span class="input-icon">📄</span>
                <input type="text" name="page_name" placeholder="مثال: About, Contact, Blog" required autofocus>
            </div>
        </div>

        <div class="form-group">
            <label>نوع الصفحة</label>
            <div class="input-wrapper">
                <span class="input-icon">⚙️</span>
                <select name="page_type" id="pageType">
                    <option value="normal">📄 عادية (About, Contact)</option>
                    <option value="form">📝 بنموذج (Login, Register)</option>
                    <option value="protected">🔒 محمية (Dashboard)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>خيارات إضافية</label>
            <div class="checkbox-group" style="display:flex; flex-direction:column; gap:10px;">
                <label style="display:flex; flex-direction:column; align-items:flex-start; gap:2px; cursor:pointer;">
                    <span style="display:flex; align-items:center; gap:6px;">
                        <input type="checkbox" name="create_model" id="createModel">
                        إنشاء Model
                    </span>
                    <span style="font-size:12px; color:#6b7280; font-weight:400; margin-right:26px;">
                        📦 للتعامل مع قاعدة البيانات (جلب/حفظ/تعديل البيانات)
                    </span>
                </label>
                <label style="display:flex; flex-direction:column; align-items:flex-start; gap:2px; cursor:pointer;">
                    <span style="display:flex; align-items:center; gap:6px;">
                        <input type="checkbox" name="create_middleware" id="createMiddleware">
                        إنشاء Middleware
                    </span>
                    <span style="font-size:12px; color:#6b7280; font-weight:400; margin-right:26px;">
                        🛡️ لحماية الصفحات ومنع الوصول غير المصرح به
                    </span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn">
            <span class="btn-icon">🚀</span>
            إنشاء الصفحة
        </button>
    </form>

    <div class="security-note">
        <span style="font-size:20px;">⚠️</span>
        <div>
            <strong>تنبيه أمان:</strong> هذه الأداة تنشئ ملفات في المشروع.<br>
            تأكد من استخدامها فقط في بيئة التطوير المحلية.<br>
            <strong>أنصحك بحذف مجلد <code>devtools</code> فور الانتهاء.</strong>
        </div>
    </div>
</div>

<script src="assets/app.js"></script>
</body>
</html>
