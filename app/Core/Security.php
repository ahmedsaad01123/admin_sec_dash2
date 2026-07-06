<?php

declare(strict_types=1);

namespace App\Core;

/**
 * دوال حماية عامة يستخدمها التطبيق:
 * - حماية من CSRF
 * - تنظيف المدخلات من محاولات XSS
 * - تشفير والتحقق من كلمات المرور
 */
final class Security
{
    public static function generateCsrfToken(): string
    {
        if (!Session::has('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }

        return Session::get('_csrf_token');
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        $stored = Session::get('_csrf_token');

        // hash_equals بتمنع هجمات الـ Timing Attack عند مقارنة الـ Tokens
        return $token !== null && $stored !== null && hash_equals($stored, $token);
    }

    /**
     * تنظيف نص أو مصفوفة من أكواد HTML/JS الضارة (Escaping)
     *
     * ملحوظة: الدالة دي مبقتش بتتنادى تلقائيًا على المدخلات في Controller::input()،
     * لأن الـ Escaping التلقائي وقت الإدخال بيسبب مشاكل (بيانات مشوّهة في الداتابيز،
     * Escaping مضاعف وقت الطباعة). استخدم دالة e() في الـ Views للـ Escaping وقت
     * الطباعة بدلاً من كده. الدالة دي متسابة هنا لو احتجتها في حالة خاصة
     * (زي تنظيف نص قبل تخزينه في مكان بيتفسر كـ HTML مباشرة من غير Escaping تاني).
     */
    public static function sanitize(mixed $input): mixed
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }

        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }

        return $input;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
