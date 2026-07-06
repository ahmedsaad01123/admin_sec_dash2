<?php

declare(strict_types=1);

namespace DevTools;

/**
 * مسؤول عن إضافة الترجمات الأساسية لملفات اللغة (ar.php / en.php)
 * عند إنشاء صفحة جديدة.
 */
final class Translator
{
    public static function addTranslationsFor(string $pageName, string $basePath): void
    {
        $translations = [
            'ar' => [
                'page_title'   => 'الصفحة',
                'page_message' => 'مرحباً بك في الصفحة الجديدة',
            ],
            'en' => [
                'page_title'   => 'Page',
                'page_message' => 'Welcome to the new page',
            ],
        ];

        foreach (['ar.php', 'en.php'] as $langFile) {
            $langPath = $basePath . "/lang/{$langFile}";

            if (!file_exists($langPath)) {
                continue;
            }

            $langContent = file_get_contents($langPath);
            $langKey = ($langFile === 'ar.php') ? 'ar' : 'en';

            foreach ($translations[$langKey] as $key => $value) {
                if (strpos($langContent, "'{$key}'") === false) {
                    $langContent = str_replace('];', "    '{$key}' => '{$value}',\n];", $langContent);
                }
            }

            file_put_contents($langPath, $langContent);
        }
    }
}
