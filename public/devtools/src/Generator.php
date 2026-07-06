<?php

declare(strict_types=1);

namespace DevTools;

/**
 * المسؤول الرئيسي عن إنشاء صفحة جديدة كاملة:
 * Controller + View + Routes + (Model / Middleware اختياريين) + ترجمات
 */
final class Generator
{
    /**
     * نقطة الدخول: تستقبل بيانات الفورم (POST) وتُرجع [message, error]
     *
     * @return array{0: string, 1: string} [$message, $error]
     */
    public static function createPage(array $post, string $basePath): array
    {
        $pageName        = trim($post['page_name'] ?? '');
        $pageType        = $post['page_type'] ?? 'normal';
        $createModel     = isset($post['create_model']);
        $createMiddleware = isset($post['create_middleware']);

        if ($pageName === '') {
            return ['', '❌ من فضلك أدخل اسم الصفحة.'];
        }

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $pageName)) {
            return ['', '❌ اسم الصفحة غير صالح. استخدم أحرف إنجليزية وأرقام و_ فقط.'];
        }

        $className = ucfirst($pageName) . 'Controller';
        $modelName = ucfirst($pageName) . 'Model';
        $viewName  = strtolower($pageName);
        $routeUri  = strtolower($pageName);

        $controllerFile  = $basePath . "/app/Controllers/{$className}.php";
        $viewFile        = $basePath . "/app/Views/{$viewName}.php";
        $modelFile       = $basePath . "/app/Models/{$modelName}.php";
        $middlewareFile  = $basePath . "/app/Core/Middleware/{$className}Middleware.php";
        $routesFile      = $basePath . '/routes/web.php';

        if (file_exists($controllerFile)) {
            return ['', "⚠️ الكنترولر {$className} موجود بالفعل."];
        }

        if (file_exists($viewFile)) {
            return ['', "⚠️ ملف الـ View {$viewName}.php موجود بالفعل."];
        }

        try {
            // 1. الكنترولر
            file_put_contents($controllerFile, self::buildController($className, $viewName, $pageType));

            // 2. الـ View
            file_put_contents($viewFile, self::buildView($pageType));

            // 3. الروتات
            $newRoutes = RouteManager::generateRoutes($routeUri, $className, $pageType);
            RouteManager::appendToRoutesFile($routesFile, $newRoutes);

            // 4. Model (اختياري)
            if ($createModel && !file_exists($modelFile)) {
                file_put_contents($modelFile, self::buildModel($modelName));
            }

            // 5. Middleware (اختياري)
            if ($createMiddleware && !file_exists($middlewareFile)) {
                file_put_contents($middlewareFile, self::buildMiddleware($className));
            }

            // 6. الترجمات
            Translator::addTranslationsFor($pageName, $basePath);

            // 7. رسالة النجاح
            $message = self::buildSuccessMessage(
                $pageName,
                $className,
                $viewName,
                $modelName,
                $routeUri,
                $pageType,
                $createModel,
                $createMiddleware
            );

            return [$message, ''];
        } catch (\Throwable $e) {
            return ['', '❌ حدث خطأ: ' . $e->getMessage()];
        }
    }

    private static function buildSuccessMessage(
        string $pageName,
        string $className,
        string $viewName,
        string $modelName,
        string $routeUri,
        string $pageType,
        bool $createModel,
        bool $createMiddleware
    ): string {
        $routeDescriptions = RouteManager::getRouteDescriptions($routeUri, $pageType);

        $message = "✅ تم إنشاء الصفحة <strong>{$pageName}</strong> بنجاح!";
        $message .= "<br>📄 Controller: <code>app/Controllers/{$className}.php</code>";
        $message .= "<br>📄 View: <code>app/Views/{$viewName}.php</code>";
        $message .= "<br>🔗 Routes: <code>" . implode('</code>, <code>', $routeDescriptions) . "</code>";

        if ($createModel) {
            $message .= "<br>📦 Model: <code>app/Models/{$modelName}.php</code>";
        }

        if ($createMiddleware) {
            $message .= "<br>🛡️ Middleware: <code>app/Core/Middleware/{$className}Middleware.php</code>";
        }

        $message .= "<br>🌐 افتح: <a href='/{$routeUri}' target='_blank'>http://localhost/{$routeUri}</a>";
        $message .= "<br><br>🔒 <strong>تذكر حذف مجلد devtools بعد الانتهاء أو حمايته.</strong>";

        return $message;
    }

    private static function buildController(string $className, string $viewName, string $type): string
    {
        $useModel = '';
        $extraMethods = '';

        if ($type === 'form') {
            $useModel = 'use App\\Models\\' . ucfirst($viewName) . 'Model;';
            $extraMethods = <<<EOT

    public function store(): void
    {
        if (!\$this->verifyCsrf()) {
            Session::flash('error', __('session_expired'));
            \$this->redirect('/{$viewName}');
        }

        \$name = \$this->input('name', '');
        \$email = \$this->input('email', '');

        // هنا ضع منطق الحفظ في قاعدة البيانات
        // مثال: \$model = new " . ucfirst($viewName) . "Model();
        // \$model->insert([...]);

        Session::flash('success', __('form_submitted'));
        \$this->redirect('/{$viewName}');
    }
EOT;
        } elseif ($type === 'protected') {
            $useModel = 'use App\\Core\\Session;';
            $extraMethods = <<<EOT

    public function __construct()
    {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
    }
EOT;
        }

        return <<<EOT
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
{$useModel}

final class {$className} extends Controller
{
    {$extraMethods}

    public function index(): void
    {
        \$this->view('{$viewName}', [
            'title' => __('page_title'),
            'message' => __('page_message'),
        ], 'layouts.app');
    }
}
EOT;
    }

    private static function buildView(string $type): string
    {
        if ($type === 'form') {
            return <<<EOT
<div class="page-container" style="max-width: 500px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <h1><?= htmlspecialchars(\$title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars(\$message, ENT_QUOTES, 'UTF-8') ?></p>

    <?php if (isset(\$error) && !empty(\$error)): ?>
        <div style="background:#fef2f2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:16px;">
            <?= htmlspecialchars(\$error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="_csrf_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

        <label style="display:block; margin:12px 0 6px; font-weight:600;">الاسم</label>
        <input type="text" name="name" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;" required>

        <label style="display:block; margin:12px 0 6px; font-weight:600;">البريد الإلكتروني</label>
        <input type="email" name="email" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:8px;" required>

        <button type="submit" style="width:100%; margin-top:20px; padding:12px; background:#111827; color:#fff; border:none; border-radius:8px; font-size:15px; cursor:pointer;">
            إرسال
        </button>
    </form>
</div>
EOT;
        }

        // الصفحات العادية والمحمية (نفس المحتوى)
        return <<<EOT
<div class="page-container" style="max-width: 700px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <h1><?= htmlspecialchars(\$title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p><?= htmlspecialchars(\$message, ENT_QUOTES, 'UTF-8') ?></p>
</div>
EOT;
    }

    private static function buildModel(string $modelName): string
    {
        return <<<EOT
<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class {$modelName} extends Model
{
    protected string \$table = '{$modelName}';
}
EOT;
    }

    private static function buildMiddleware(string $className): string
    {
        return <<<EOT
<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Session;

final class {$className}Middleware
{
    public function handle(): bool
    {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }
        return true;
    }
}
EOT;
    }
}
