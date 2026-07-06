<?php

declare(strict_types=1);

namespace PagesManager;

class PageScanner
{
    /**
     * مسح جميع الصفحات الموجودة في المشروع
     * @return array<int, array{
     *     name: string,
     *     controller: string,
     *     view: string,
     *     model: string|null,
     *     middleware: string|null,
     *     routes: string[]
     * }>
     */
    public static function scan(): array
    {
        $pages = [];
        $controllersDir = BASE_PATH . '/app/Controllers';
        
        if (!is_dir($controllersDir)) {
            return [];
        }
        
        // الحصول على جميع ملفات الكنترولر
        $controllerFiles = glob($controllersDir . '/*Controller.php');
        
        foreach ($controllerFiles as $controllerFile) {
            $fileName = basename($controllerFile, '.php');
            
            // استخراج اسم الصفحة (مثل AboutController -> About)
            if (preg_match('/^(.+)Controller$/', $fileName, $matches)) {
                $pageName = $matches[1];
                
                // مسار الـ View
                $viewFile = BASE_PATH . '/app/Views/' . strtolower($pageName) . '.php';
                $viewExists = file_exists($viewFile);
                
                // مسار الـ Model
                $modelFile = BASE_PATH . '/app/Models/' . $pageName . 'Model.php';
                $modelExists = file_exists($modelFile);
                
                // مسار الـ Middleware
                $middlewareFile = BASE_PATH . '/app/Core/Middleware/' . $pageName . 'ControllerMiddleware.php';
                $middlewareExists = file_exists($middlewareFile);
                
                // الحصول على الـ Routes من ملف web.php
                $routes = self::getRoutesForPage(strtolower($pageName));
                
                $pages[] = [
                    'name' => $pageName,
                    'controller' => $controllerFile,
                    'view' => $viewExists ? $viewFile : null,
                    'model' => $modelExists ? $modelFile : null,
                    'middleware' => $middlewareExists ? $middlewareFile : null,
                    'routes' => $routes,
                ];
            }
        }
        
        // ترتيب الصفحات أبجدياً
        usort($pages, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        return $pages;
    }
    
    /**
     * الحصول على الـ Routes المرتبطة بصفحة معينة من ملف web.php
     */
    private static function getRoutesForPage(string $pageName): array
    {
        $routes = [];
        $routesFile = BASE_PATH . '/routes/web.php';
        
        if (!file_exists($routesFile)) {
            return $routes;
        }
        
        $content = file_get_contents($routesFile);
        
        // البحث عن الروتات التي تحتوي على اسم الصفحة
        $pattern = "/\\\$router->(get|post|put|delete)\s*\(\s*'\/{$pageName}(?:\/|$)/";
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach ($matches[0] as $match) {
            // استخراج السطر كاملاً
            $start = $match[1];
            $end = strpos($content, "\n", $start);
            if ($end === false) {
                $end = strlen($content);
            }
            $line = substr($content, $start, $end - $start);
            $routes[] = trim($line);
        }
        
        return $routes;
    }
}