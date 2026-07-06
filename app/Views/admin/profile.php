<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa fa-key fa-lg"></i>
                    </div>
                    <h3 class="card-title mb-0">تغيير كلمة المرور</h3>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="fa fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="fa fa-check-circle"></i>
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الحالية</label>
                        <input type="password" name="old_password" required autofocus class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" name="new_password" required minlength="8" class="form-control">
                        <small class="text-muted">يجب أن تكون 8 أحرف على الأقل</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" name="confirm_password" required class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-save"></i> تحديث كلمة المرور
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="/dashboard" class="text-muted text-decoration-none">
                        <i class="fa fa-arrow-right"></i> العودة للوحة التحكم
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
</style>