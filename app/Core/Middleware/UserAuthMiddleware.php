<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Session;

/**
 * ميدل وير بيتأكد إن المتبرع مسجل دخول قبل ما يوصل لأي صفحة محمية
 * لو مش مسجل، بيوجهه لصفحة اللوجين تلقائيًا
 */
final class UserAuthMiddleware
{
    public function handle(): bool
    {
        if (Session::has('user_id')) {
            return true;
        }

        header('Location: /login');
        exit;
    }
}
