<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    private Router $router;

    public function __construct(private readonly array $config)
    {
        Config::set($config);
        Session::start($config['session']);
        // تحميل اللغة
        $this->loadLanguage();

        $this->router = new Router();
        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require BASE_PATH . '/routes/web.php';
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $route = $this->router->resolve($method, $uri);

        if ($route === null) {
            $this->abort(404, 'الصفحة المطلوبة غير موجودة');
            return;
        }

        // تنفيذ الـ Middleware قبل الوصول للكنترولر (لو موجود)
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if ($middleware->handle() !== true) {
                return;
            }
        }

        [$controllerName, $methodName] = explode('@', $route['action']);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            $this->abort(500, "الكنترولر {$controllerClass} غير موجود");
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            $this->abort(500, "الميثود {$methodName} غير موجودة في {$controllerClass}");
            return;
        }

        try {
            call_user_func_array([$controller, $methodName], $route['params']);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function abort(int $status, string $message): void
    {
        http_response_code($status);

        $viewPath = BASE_PATH . "/app/Views/errors/{$status}.php";

        if (is_file($viewPath)) {
            require $viewPath;
        } else {
            echo $message;
        }
    }

    private function handleException(\Throwable $e): void
    {
        $logFile = BASE_PATH . '/storage/logs/app.log';
        $entry = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        file_put_contents($logFile, $entry, FILE_APPEND);

        http_response_code(500);

        if (Config::get('app.debug')) {
            echo "<pre>{$entry}</pre>";
        } else {
            $this->abort(500, 'حدث خطأ غير متوقع، برجاء المحاولة لاحقًا');
        }
    }


    private function loadLanguage(): void
    {
        \App\Core\Language::init();
    }    
}