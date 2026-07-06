<?php

declare(strict_types=1);

namespace DevTools;

/**
 * مسؤول عن توليد وإضافة الروتات (Routes) لملف routes/web.php
 */
final class RouteManager
{
    private const START_MARKER = '// AUTO-GENERATED ROUTES START';
    private const END_MARKER   = '// AUTO-GENERATED ROUTES END';

    public static function generateRoutes(string $uri, string $className, string $type): string
    {
        $routes = "\$router->get('/{$uri}', '{$className}@index');\n";

        if ($type === 'form') {
            $routes .= "\$router->post('/{$uri}', '{$className}@store');\n";
        }

        return $routes;
    }

    public static function getRouteDescriptions(string $uri, string $type): array
    {
        $descriptions = ["GET /{$uri}"];

        if ($type === 'form') {
            $descriptions[] = "POST /{$uri}";
        }

        return $descriptions;
    }

    /**
     * إضافة الروتات بشكل آمن باستخدام Marker.
     * البحث عن START_MARKER وإضافة الروتات الجديدة بعدها وقبل END_MARKER.
     * إذا لم يتم العثور على الماركر، يتم إنشاؤه في نهاية الملف.
     */
    public static function insertRoutesSafely(string $content, string $newRoutes): string
    {
        if (strpos($content, self::START_MARKER) !== false && strpos($content, self::END_MARKER) !== false) {
            $pattern = '/' . preg_quote(self::START_MARKER, '/') . '(.*?)' . preg_quote(self::END_MARKER, '/') . '/s';

            return preg_replace_callback($pattern, function (array $matches) use ($newRoutes): string {
                $mergedLines = self::mergeRouteLines($matches[1], $newRoutes);

                return self::START_MARKER . "\n" . $mergedLines . self::END_MARKER . "\n";
            }, $content);
        }

        $mergedLines = self::mergeRouteLines('', $newRoutes);
        $markerBlock = "\n\n" . self::START_MARKER . "\n" . $mergedLines . self::END_MARKER . "\n";

        return $content . $markerBlock;
    }

    /**
     * يدمج الروتات الموجودة بالفعل بين الماركرز مع الروتات الجديدة،
     * مع تجاهل أي سطر مكرر (نفس الروت بالظبط) حفاظاً على الروتات القديمة.
     */
    private static function mergeRouteLines(string $existingBlock, string $newRoutes): string
    {
        $existingLines = array_values(array_filter(
            array_map('rtrim', explode("\n", $existingBlock)),
            static fn (string $line): bool => $line !== ''
        ));

        $newLines = array_values(array_filter(
            array_map('rtrim', explode("\n", $newRoutes)),
            static fn (string $line): bool => $line !== ''
        ));

        foreach ($newLines as $line) {
            if (!in_array($line, $existingLines, true)) {
                $existingLines[] = $line;
            }
        }

        if (empty($existingLines)) {
            return '';
        }

        return implode("\n", $existingLines) . "\n";
    }

    public static function appendToRoutesFile(string $routesFile, string $newRoutes): void
    {
        $content = file_get_contents($routesFile);
        $content = self::insertRoutesSafely($content, $newRoutes);
        file_put_contents($routesFile, $content);
    }
}
