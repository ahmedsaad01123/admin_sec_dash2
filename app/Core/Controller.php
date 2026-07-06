<?php

declare(strict_types=1);

namespace App\Core;

/**
 * الكلاس الأساسي لكل الكنترولرز
 * بيوفر دوال مساعدة مشتركة: عرض Views (مع أو من غير Layout), إرجاع JSON, Redirect, قراءة المدخلات
 */
abstract class Controller
{
    /**
     * عرض ملف View، مع إمكانية لفّه جوه Layout (header/footer) اختياريًا
     *
     * مثال بدون Layout (زي صفحات الأدمن الحالية):
     *   $this->view('admin.dashboard', ['title' => '...']);
     *
     * مثال مع Layout (زي الصفحة الرئيسية):
     *   $this->view('home.index', ['title' => '...'], 'layouts.app');
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data, EXTR_SKIP);

        // ✅ حماية من Path Traversal: نمنع .. و \ و / في بداية اسم الـ View
        if (str_contains($view, '..') || str_contains($view, '\\') || str_starts_with($view, '/')) {
            throw new \RuntimeException('Invalid view path.');
        }

        $viewPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException("ملف الـ View غير موجود: {$view}");
        }

        // لو مفيش Layout، اعرض الصفحة زي ما هي (السلوك القديم زي ما هو)
        if ($layout === null) {
            require $viewPath;
            return;
        }

        // ✅ نفس الحماية للـ Layout
        if (str_contains($layout, '..') || str_contains($layout, '\\') || str_starts_with($layout, '/')) {
            throw new \RuntimeException('Invalid layout path.');
        }

        // مع وجود Layout: نجمع محتوى الصفحة في متغير الأول
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $layout) . '.php';

        if (!is_file($layoutPath)) {
            throw new \RuntimeException("ملف الـ Layout غير موجود: {$layout}");
        }

        // $content ومتغيرات $data (title, ...إلخ) كلهم متاحين جوه الـ Layout
        require $layoutPath;
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * قراءة قيمة واحدة من المدخلات (GET أو POST)
     *
     * ملحوظة مهمة: القيمة بترجع "خام" (بعد trim بس) من غير أي Escaping.
     * الـ Escaping بتاع HTML مسؤولية الـ View وقت الطباعة (استخدم دالة e())،
     * مش مسؤولية طبقة الإدخال. لو عملنا Escaping هنا، هيبقى فيه Escaping
     * مضاعف وقت الطباعة، وكمان البيانات هتتخزن في الداتابيز بشكل مشوّه
     * (زي إن "&" تتحول لـ "&amp;" قبل ما تتخزن أصلاً).
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        $value = $this->all()[$key] ?? $default;

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * كل المدخلات (GET + POST) بعد trim بس، من غير Escaping
     * تُستخدم مع الـ Validator أو لما تحتاج تمرر كل البيانات مرة واحدة
     */
    protected function all(): array
    {
        $data = array_merge($_GET, $_POST);

        return array_map(
            static fn(mixed $value): mixed => is_string($value) ? trim($value) : $value,
            $data
        );
    }

    protected function verifyCsrf(): bool
    {
        return Security::verifyCsrfToken($_POST['_csrf_token'] ?? null);
    }

    /**
     * يتحقق من المدخلات حسب قواعد معينة (شوف App\Core\Validator)
     * لو فيه أخطاء: بيحفظها في الـ Session (Flash) مع البيانات القديمة
     * ويعمل Redirect تلقائي، وبيوقف تنفيذ باقي الكود في الميثود.
     *
     * مثال الاستخدام في أي كنترولر:
     *   $data = $this->validate([
     *       'name'   => 'required|string|min:3',
     *       'email'  => 'required|email',
     *       'amount' => 'required|numeric|min:1',
     *   ]);
     *   // لو وصلنا هنا يبقى كل البيانات سليمة ومتاحة في $data
     *
     * @return array البيانات بعد التحقق (لو نجح التحقق)
     */
    protected function validate(array $rules, ?string $redirectTo = null): array
    {
        $data = $this->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            Session::flash('errors', $validator->errors());
            Session::flash('old', $data);

            $this->redirect($redirectTo ?? ($_SERVER['HTTP_REFERER'] ?? '/'));
        }

        return $data;
    }
}