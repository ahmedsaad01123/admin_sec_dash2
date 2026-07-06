<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Config;
use App\Core\Session;

/**
 * ميدل وير بيتأكد إن الأدمن مسجل دخول قبل ما يوصل لأي صفحة محمية
 * لو مش مسجل، بيوجهه لصفحة اللوجين تلقائيًا
 */
final class AdminAuthMiddleware
{
    public function handle(): bool
    {
        if (Session::has('admin_id')) {
            return true;
        }

        header('Location: /');
        exit;
    }
}