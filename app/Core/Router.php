<?php

declare(strict_types=1);

namespace App\Core;

/**
 * راوتر بسيط بيدعم:
 * - GET / POST / PUT / DELETE
 * - باراميترات ديناميكية زي: /users/{id}
 * - Groups مع Prefix و Middleware قابلين للتداخل (Nested)
 */
final class Router
{
    private array $routes = [];

    private string $groupPrefix = '';

    /** @var array<class-string> */
    private array $groupMiddleware = [];

    public function get(string $uri, string $action, array $options = []): void
    {
        $this->addRoute('GET', $uri, $action, $options);
    }

    public function post(string $uri, string $action, array $options = []): void
    {
        $this->addRoute('POST', $uri, $action, $options);
    }

    public function put(string $uri, string $action, array $options = []): void
    {
        $this->addRoute('PUT', $uri, $action, $options);
    }

    public function delete(string $uri, string $action, array $options = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $options);
    }

    /**
     * تجميع روتات تحت Prefix وMiddleware مشتركين، وبيدعم التداخل
     * (مجموعة جوه مجموعة) عشان نقدر نعمل مثلاً روتات login بدون حماية
     * وروتات تانية جوه نفس الـ admin محتاجة تسجيل دخول
     */
    public function group(string $prefix, \Closure $callback, array $middleware = []): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = trim($previousPrefix . '/' . trim($prefix, '/'), '/');
        $this->groupMiddleware = [...$previousMiddleware, ...$middleware];

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $uri, string $action, array $options = []): void
    {
        $uri = trim($this->groupPrefix . '/' . trim($uri, '/'), '/');

        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $uri);
        $pattern = '#^' . $pattern . '$#u';

        $routeMiddleware = $this->groupMiddleware;
        if (isset($options['middleware'])) {
            $routeMiddleware = array_merge($routeMiddleware, (array) $options['middleware']);
        }

        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'action'     => $action,
            'middleware' => $routeMiddleware,
        ];
    }

    /**
     * @return array{action: string, params: array, middleware: array}|null
     */
    public function resolve(string $method, string $uri): ?array
    {
        $path = trim((string) parse_url($uri, PHP_URL_PATH), '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter(
                    $matches,
                    static fn(int|string $key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                return [
                    'action'     => $route['action'],
                    'params'     => $params,
                    'middleware' => $route['middleware'],
                ];
            }
        }

        return null;
    }
}