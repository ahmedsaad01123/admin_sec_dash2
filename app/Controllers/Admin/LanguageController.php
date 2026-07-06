<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Language;
use App\Core\Security;
use App\Core\Session;

final class LanguageController extends Controller
{
    public function switch(): void
    {
        // التحقق من CSRF
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'انتهت صلاحية الجلسة، حاول مجدداً.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $lang = $this->input('lang', 'ar');
        
        // التحقق من صحة اللغة
        if (!in_array($lang, ['ar', 'en'])) {
            $lang = 'ar';
        }

        // تبديل اللغة
        Language::switch($lang);

        // Redirect للصفحة السابقة
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
    }
}
