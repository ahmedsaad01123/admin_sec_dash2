<?php

declare(strict_types=1);

namespace App\Core;

/**
 * أوتولودر بسيط بيحمّل الكلاسات تلقائيًا حسب الـ Namespace
 * مثال: App\Controllers\HomeController  =>  app/Controllers/HomeController.php
 * الفكرة زي PSR-4 بالظبط بس من غير الحاجة لـ composer
 */
final class Autoloader
{
    private const NAMESPACE_PREFIX = 'App\\';

    public function register(): void
    {
        spl_autoload_register($this->loadClass(...));
    }

    private function loadClass(string $class): void
    {
        if (!str_starts_with($class, self::NAMESPACE_PREFIX)) {
            return; // مش من الـ Namespace بتاعنا، سيبه لأوتولودر تاني (لو موجود)
        }

        $relativeClass = substr($class, strlen(self::NAMESPACE_PREFIX));
        $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($path)) {
            require_once $path;
        }
    }
}
