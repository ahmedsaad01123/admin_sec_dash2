<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Session;

/**
 * ميدل وير بيمنع المستخدمين المسجلين من الوصول لصفحات معينة
 * زي صفحات اللوجين والتسجيل - لو مسجل بالفعل يتحول للصفحة الرئيسية
 */
final class GuestOnlyMiddleware
{
    public function handle(): bool
    {
        if (Session::has('user_id')) {
            header('Location: /');
            exit;
        }

        return true;
    }
}
