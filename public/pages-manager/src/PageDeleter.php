<?php

declare(strict_types=1);

namespace PagesManager;

class PageDeleter
{
    /**
     * حذف صفحة بالكامل (Controller, View, Routes, Model اختياري, Middleware اختياري)
     * @return array{success: bool, message: string, deleted_files: string[], errors: string[]}
     */
    public static function delete(string $pageName, bool $deleteModel = false, bool $deleteMiddleware = false): array
    {
        $deletedFiles = [];
        $errors = [];
        
        $className = ucfirst($pageName) . 'Controller';
        $viewName = strtolower($pageName);
        $routeUri = strtolower($pageName);
        
        // 1. حذف الكنترولر
        $controllerFile = BASE_PATH . "/app/Controllers/{$className}.php";
        if (file_exists($controllerFile)) {
            if (unlink($controllerFile)) {
                $deletedFiles[] = "app/Controllers/{$className}.php";
            } else {
                $errors[] = "app/Controllers/{$className}.php (فشل الحذف)";
            }
        }
        
        // 2. حذف الـ View
        $viewFile = BASE_PATH . "/app/Views/{$viewName}.php";
        if (file_exists($viewFile)) {
            if (unlink($viewFile)) {
                $deletedFiles[] = "app/Views/{$viewName}.php";
            } else {
                $errors[] = "app/Views/{$viewName}.php (فشل الحذف)";
            }
        }
        
        // 3. حذف الـ Model (اختياري)
        if ($deleteModel) {
            $modelFile = BASE_PATH . "/app/Models/{$pageName}Model.php";
            if (file_exists($modelFile)) {
                if (unlink($modelFile)) {
                    $deletedFiles[] = "app/Models/{$pageName}Model.php";
                } else {
                    $errors[] = "app/Models/{$pageName}Model.php (فشل الحذف)";
                }
            }
        }
        
        // 4. حذف الـ Middleware (اختياري)
        if ($deleteMiddleware) {
            $middlewareFile = BASE_PATH . "/app/Core/Middleware/{$className}Middleware.php";
            if (file_exists($middlewareFile)) {
                if (unlink($middlewareFile)) {
                    $deletedFiles[] = "app/Core/Middleware/{$className}Middleware.php";
                } else {
                    $errors[] = "app/Core/Middleware/{$className}Middleware.php (فشل الحذف)";
                }
            }
        }
        
        // 5. حذف الـ Routes
        $routesFile = BASE_PATH . '/routes/web.php';
        if (file_exists($routesFile)) {
            $content = file_get_contents($routesFile);
            $newContent = self::removeRoutes($content, $routeUri);
            if ($content !== $newContent) {
                if (file_put_contents($routesFile, $newContent) !== false) {
                    $deletedFiles[] = "routes/web.php (تم حذف الروتات الخاصة بـ {$pageName})";
                } else {
                    $errors[] = "routes/web.php (فشل تحديث الملف)";
                }
            }
        }
        
        // بناء رسالة النتيجة
        $success = empty($errors) && !empty($deletedFiles);
        $message = $success ? "تم الحذف بنجاح" : "تم الحذف جزئياً (بعض الملفات لم تُحذف)";
        
        return [
            'success' => $success,
            'message' => $message,
            'deleted_files' => $deletedFiles,
            'errors' => $errors,
        ];
    }
    
    /**
     * إزالة الروتات الخاصة بصفحة معينة من ملف routes/web.php
     * باستخدام الـ Marker الموجود في devtools
     */
    private static function removeRoutes(string $content, string $routeUri): string
    {
        $startMarker = '// AUTO-GENERATED ROUTES START';
        $endMarker = '// AUTO-GENERATED ROUTES END';
        
        // البحث عن الماركر
        if (strpos($content, $startMarker) === false || strpos($content, $endMarker) === false) {
            // إذا لم يوجد ماركر، نبحث عن الروتات في كل الملف (أقل أماناً)
            $patterns = [
                "/\\\$router->get\s*\(\s*'\/{$routeUri}(?:\/|$)[^;]*;/",
                "/\\\$router->post\s*\(\s*'\/{$routeUri}(?:\/|$)[^;]*;/",
                "/\\\$router->put\s*\(\s*'\/{$routeUri}(?:\/|$)[^;]*;/",
                "/\\\$router->delete\s*\(\s*'\/{$routeUri}(?:\/|$)[^;]*;/",
            ];
            foreach ($patterns as $pattern) {
                $content = preg_replace($pattern, '', $content);
            }
            return $content;
        }
        
        // استخراج المحتوى بين الماركرين
        $pattern = "/" . preg_quote($startMarker, '/') . "\s*(.*?)\s*" . preg_quote($endMarker, '/') . "/s";
        preg_match($pattern, $content, $matches);
        
        if (!isset($matches[1])) {
            return $content;
        }
        
        $blockContent = $matches[1];
        
        // حذف الأسطر التي تحتوي على الروت المطلوب
        $lines = explode("\n", $blockContent);
        $newLines = [];
        foreach ($lines as $line) {
            // التحقق من وجود الروت في السطر
            if (strpos($line, "'/{$routeUri}'") !== false || strpos($line, "'/{$routeUri}/'") !== false) {
                continue; // تخطي هذا السطر (حذفه)
            }
            $newLines[] = $line;
        }
        
        $newBlock = implode("\n", $newLines);
        // إزالة الأسطر الفارغة الزائدة
        $newBlock = trim($newBlock);
        
        // إعادة بناء المحتوى
        $replacement = $startMarker . "\n" . $newBlock . "\n" . $endMarker;
        return preg_replace($pattern, $replacement, $content);
    }
}