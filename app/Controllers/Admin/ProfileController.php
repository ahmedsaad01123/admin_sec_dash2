<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\PasswordPolicy;
use App\Core\Security;
use App\Core\Session;
use App\Models\AdminUserModel;

final class ProfileController extends Controller
{
    public function show(): void
    {
        $this->view('admin.profile', [
            'title'         => 'تغيير كلمة المرور',
            'page_title'    => 'الإعدادات',
            'current_page'  => 'profile',
            'admin_name'    => Session::get('admin_username'),
            'csrf_token'   => Security::generateCsrfToken(),
            'error'         => Session::flash('profile_error'),
            'success'       => Session::flash('profile_success'),
        ], 'layouts.admin');
    }

    public function updatePassword(): void
    {
        // التحقق من CSRF
        if (!$this->verifyCsrf()) {
            Session::flash('profile_error', 'انتهت صلاحية الجلسة، حاول مجدداً.');
            $this->redirect($this->profileUrl());
        }

        // قراءة المدخلات
        $oldPassword     = (string) ($_POST['old_password'] ?? '');
        $newPassword     = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        // التحقق من صحة المدخلات الأساسية
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::flash('profile_error', 'جميع الحقول مطلوبة.');
            $this->redirect($this->profileUrl());
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('profile_error', 'كلمة المرور الجديدة وتأكيدها غير متطابقين.');
            $this->redirect($this->profileUrl());
        }

        // جلب بيانات الأدمن الحالي من قاعدة البيانات
        $adminId = (int) Session::get('admin_id');
        $model   = new AdminUserModel();
        $admin   = $model->find($adminId); // نفترض وجود find في Model الأساسي

        if (!$admin) {
            Session::flash('profile_error', 'المستخدم غير موجود.');
            $this->redirect($this->profileUrl());
        }

        // التحقق من صحة كلمة المرور القديمة
        if (!Security::verifyPassword($oldPassword, $admin['password'])) {
            Session::flash('profile_error', 'كلمة المرور الحالية غير صحيحة.');
            $this->redirect($this->profileUrl());
        }

        // التحقق من قوة كلمة المرور الجديدة حسب سياسة الأمان
        // (بنمرر بيانات الأدمن عشان نتأكد إن الباسورد الجديد مش مبني على اسمه/إيميله)
        $policy = PasswordPolicy::validate($newPassword, [
            'username' => $admin['username'] ?? '',
            'email'    => $admin['email'] ?? '',
        ]);

        if (!$policy['valid']) {
            Session::flash('profile_error', implode(' ', $policy['errors']));
            $this->redirect($this->profileUrl());
        }

        // تحديث كلمة المرور
        $hashedNew = Security::hashPassword($newPassword); // نفترض وجود دالة مساعدة
        $updated = $model->update($adminId, ['password' => $hashedNew]);

        if (!$updated) {
            Session::flash('profile_error', 'حدث خطأ أثناء تحديث كلمة المرور، حاول مجدداً.');
        } else {
            Session::flash('profile_success', 'تم تغيير كلمة المرور بنجاح.');
        }

        $this->redirect($this->profileUrl());
    }

    private function profileUrl(): string
    {
        return '/profile';
    }
}