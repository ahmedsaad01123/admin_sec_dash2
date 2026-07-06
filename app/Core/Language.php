<?php

declare(strict_types=1);

namespace App\Core;

final class Language
{
    private static array $translations = [];
    private static string $currentLang = 'ar'; // افتراضي عربي

    /**
     * تحميل اللغة المحددة
     */
    public static function load(string $lang): void
    {
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'ar';

        self::$currentLang = $lang;
        $filePath = BASE_PATH . "/lang/{$lang}.php";

        if (is_file($filePath)) {
            self::$translations = require $filePath;
        } else {
            self::$translations = [];
        }

        // حفظ اللغة في الجلسة
        Session::set('lang', $lang);
    }

    /**
     * الحصول على الترجمة لمفتاح معين مع دعم استبدال المتغيرات
     * مثال: __('home_title') => 'الصفحة الرئيسية'
     * مثال: __('home_badge', ['version' => '8.4']) => 'PHP 8.4 • هيكل MVC ✓'
     */
    public static function get(string $key, array $replacements = []): string
    {
        $text = self::$translations[$key] ?? $key;

        foreach ($replacements as $placeholder => $value) {
            $text = str_replace("{{$placeholder}}", (string) $value, $text);
        }

        return $text;
    }

    /**
     * الحصول على اللغة الحالية
     */
    public static function current(): string
    {
        return self::$currentLang;
    }

    /**
     * تغيير اللغة (يتم استدعاؤها من الـ Controller)
     */
    public static function switch(string $lang): void
    {
        self::load($lang);
    }

    /**
     * تهيئة اللغة من الجلسة أو القيمة الافتراضية
     */
    public static function init(): void
    {
        $lang = Session::get('lang', 'ar');
        self::load($lang);
    }
}