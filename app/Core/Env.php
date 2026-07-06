<?php

declare(strict_types=1);

namespace App\Core;

/**
 * كلاس بسيط لقراءة ملف .env وتحويله لمتغيرات بيئة
 * بديل خفيف عن مكتبة vlucas/phpdotenv عشان نفضل من غير Dependencies خارجية
 */
final class Env
{
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        if (!is_file($path)) {
            throw new \RuntimeException("ملف الإعدادات .env غير موجود في: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // تجاهل التعليقات والأسطر الفارغة
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode('=', $line, 2));

            // إزالة علامات التنصيص المحيطة بالقيمة لو موجودة
            $value = trim($value, "\"'");

            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("{$name}={$value}");
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        // تحويل النصوص الشائعة لقيم PHP حقيقية (true/false/null)
        return match (strtolower((string) $value)) {
            'true' => true,
            'false' => false,
            'null', '' => null,
            default => $value,
        };
    }
}
