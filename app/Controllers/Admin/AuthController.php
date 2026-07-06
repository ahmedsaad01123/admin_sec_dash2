<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Security;
use App\Core\Session;
use App\Models\AdminUserModel;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Session::has('admin_id')) {
            $this->redirect($this->dashboardUrl());
        }

        $this->view('admin.login', [
            'title'      => 'تسجيل دخول الأدمن',
            'csrf_token' => Security::generateCsrfToken(),
            'error'      => Session::flash('login_error'),
        ]);
    }

    public function login(): void
    {
        if (!$this->verifyCsrf()) {
            Session::flash('login_error', 'انتهت صلاحية الجلسة، حاول تاني.');
            $this->redirect($this->loginUrl());
        }

        $username = (string) $this->input('username', '');
        $password = (string) ($_POST['password'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // فحص عدد المحاولات الفاشلة قبل ما نتحقق من الباسورد أصلاً
        $limit = RateLimiter::attempt($username, $ip);

        if (!$limit['allowed']) {
            $minutes = (int) ceil($limit['retry_after'] / 60);
            Session::flash('login_error', "محاولات كتير غلط. حاول تاني بعد {$minutes} دقيقة تقريباً.");
            $this->redirect($this->loginUrl());
        }

        $model = new AdminUserModel();
        $admin = $model->findByUsername($username);

        // لو اليوزر مش موجود، لازم نعمل password_verify() على Hash وهمي برضه
        // عشان وقت الاستجابة يفضل ثابت، ومحدش يقدر يعرف "اليوزرنيم ده موجود ولا لأ"
        // من فرق التوقيت بين الحالتين (Timing Attack)
        $hashToCheck = $admin['password'] ?? '$2y$12$usZqLwLwOe0LwWjF4dK9wOeXHqjX3F5xJqXk8vQhF6mUwYQwZ8P2S';
        $passwordValid = Security::verifyPassword($password, $hashToCheck);

        // رسالة الخطأ واحدة سواء اليوزر غلط أو الباسورد غلط
        // عشان محدش يقدر يعرف إن اليوزرنيم ده موجود أصلاً ولا لأ
        if (!$admin || !$passwordValid) {
            Session::flash('login_error', 'اسم المستخدم أو كلمة المرور غير صحيحة.');
            $this->redirect($this->loginUrl());
        }

        // نجح تسجيل الدخول: نصفّر عداد المحاولات الفاشلة بتاع اليوزر ده من الـ IP ده
        RateLimiter::clear($username, $ip);

        // تجديد الـ Session ID بعد تسجيل الدخول يمنع هجمات Session Fixation
        session_regenerate_id(true);

        Session::set('admin_id', $admin['id']);
        Session::set('admin_username', $admin['username']);

        $model->update((int) $admin['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        $this->redirect($this->dashboardUrl());
    }

    public function logout(): void
    {
        Session::remove('admin_id');
        Session::remove('admin_username');
        session_regenerate_id(true);

        $this->redirect($this->loginUrl());
    }

    private function loginUrl(): string
    {
        return '/';
    }

    private function dashboardUrl(): string
    {
        return '/dashboard';
    }
}