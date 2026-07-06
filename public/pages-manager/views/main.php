<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الصفحات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/pages-manager/assets/style.css?v=<?= time() ?>">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-icon">📋</div>
        <div>
            <h1>إدارة الصفحات</h1>
            <div class="subtitle">Pages Manager — <span id="pageCount"><?= $totalPages ?></span> صفحة</div>
        </div>
        <div style="margin-right:auto;">
            <a href="/public/devtools/" class="btn-small">🛠️ أدوات المطور</a>
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

    <?php if (empty($pages)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>لا توجد صفحات</h3>
            <p>لم يتم إنشاء أي صفحات بعد. استخدم <a href="/public/devtools/">أدوات المطور</a> لإنشاء صفحة جديدة.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الصفحة</th>
                        <th>الملفات</th>
                        <th>الروتات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $index => $page): ?>
                        <tr data-page="<?= htmlspecialchars($page['name'], ENT_QUOTES, 'UTF-8') ?>">
                            <td><?= $index + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($page['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <br>
                                <span class="status-badge status-<?= $page['view'] ? 'active' : 'warning' ?>">
                                    <?= $page['view'] ? '✅' : '⚠️' ?> <?= $page['view'] ? 'View موجود' : 'View مفقود' ?>
                                </span>
                            </td>
                            <td>
                                <div class="file-list">
                                    <div class="file-item <?= file_exists($page['controller']) ? 'exists' : 'missing' ?>">
                                        📄 Controller
                                    </div>
                                    <div class="file-item <?= $page['view'] ? 'exists' : 'missing' ?>">
                                        🖼️ View
                                    </div>
                                    <?php if ($page['model']): ?>
                                        <div class="file-item exists">📦 Model</div>
                                    <?php endif; ?>
                                    <?php if ($page['middleware']): ?>
                                        <div class="file-item exists">🛡️ Middleware</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($page['routes'])): ?>
                                    <?php foreach ($page['routes'] as $route): ?>
                                        <code class="route-line"><?= htmlspecialchars($route, ENT_QUOTES, 'UTF-8') ?></code>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color:#9ca3af; font-size:12px;">لا توجد روتات</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-delete" onclick="confirmDelete('<?= htmlspecialchars($page['name'], ENT_QUOTES, 'UTF-8') ?>', <?= $page['model'] ? 'true' : 'false' ?>, <?= $page['middleware'] ? 'true' : 'false' ?>)">
                                    🗑️ حذف
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="security-note">
        <span style="font-size:20px;">⚠️</span>
        <div>
            <strong>تنبيه:</strong> حذف صفحة سيؤدي إلى حذف الملفات المرتبطة بها نهائياً.<br>
            تأكد من أنك لا تحتاجها قبل الحذف.
            <strong>أنصحك بحذف مجلد <code>pages-manager</code> فور الانتهاء.</strong>
        </div>
    </div>
</div>

<!-- نموذج التأكيد (مخفي) -->
<div id="deleteModal" class="modal" style="display:none;">
    <div class="modal-content">
        <h2>⚠️ تأكيد الحذف</h2>
        <p>هل أنت متأكد من حذف صفحة <strong id="deletePageName"></strong>؟</p>
        
        <div id="deleteOptions" style="margin: 16px 0;">
            <label style="display:block; margin:8px 0;">
                <input type="checkbox" id="deleteModelCheck" checked>
                🗑️ حذف الـ Model أيضاً
            </label>
            <label style="display:block; margin:8px 0;">
                <input type="checkbox" id="deleteMiddlewareCheck" checked>
                🗑️ حذف الـ Middleware أيضاً
            </label>
        </div>

        <form id="deleteForm" method="post">
            <input type="hidden" name="action" value="delete_page">
            <input type="hidden" name="page_name" id="deletePageInput">
            <input type="hidden" name="delete_model" id="deleteModelInput" value="1">
            <input type="hidden" name="delete_middleware" id="deleteMiddlewareInput" value="1">
            
            <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:20px;">
                <button type="button" class="btn-cancel" onclick="closeModal()">إلغاء</button>
                <button type="submit" class="btn-danger">🗑️ تأكيد الحذف</button>
            </div>
        </form>
    </div>
</div>

<script src="/public/pages-manager/assets/app.js?v=<?= time() ?>"></script>
</body>
</html>