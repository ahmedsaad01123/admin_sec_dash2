<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Controller;
use App\Core\Security;
use App\Core\Session;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('admin.dashboard', [
            'title'         => 'لوحة التحكم',
            'page_title'    => 'الرئيسية',
            'current_page'  => 'dashboard',
            'admin_name'    => Session::get('admin_username'),
            'csrf_token'    => Security::generateCsrfToken(),
        ], 'layouts.admin');
    }
}