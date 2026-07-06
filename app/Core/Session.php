<?php

declare(strict_types=1);

namespace App\Core;

/**
 * إدارة الجلسات (Sessions) بإعدادات حماية آمنة
 * (httponly, samesite, secure حسب البيئة)
 */
final class Session
{
    public static function start(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_set_cookie_params([
            'lifetime' => $config['lifetime'] * 60,
            'path'     => '/',
            'secure'   => $config['secure'],   // لازم يبقى true لو الموقع شغال على HTTPS
            'httponly' => true,                // يمنع الوصول للكوكيز من الـ JavaScript
            'samesite' => 'Lax',                // حماية بسيطة من هجمات CSRF
        ]);

        session_start();
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * رسائل مؤقتة (Flash Messages) بتظهر مرة واحدة بس بعد أول قراءة
     */
    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $data = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $data;
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}
