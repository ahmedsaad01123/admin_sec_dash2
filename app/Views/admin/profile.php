<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                <i class="fas fa-key text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">تغيير كلمة المرور</h1>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور الحالية</label>
                <input type="password" name="old_password" required autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">كلمة المرور الجديدة</label>
                <input type="password" name="new_password" required minlength="8"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition">
                <p class="text-xs text-gray-500 mt-1">يجب أن تكون 8 أحرف على الأقل</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">تأكيد كلمة المرور الجديدة</label>
                <input type="password" name="confirm_password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition">
            </div>

            <button type="submit" 
                class="w-full bg-accent text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition font-semibold">
                <i class="fas fa-save ml-2"></i>
                تحديث كلمة المرور
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/dashboard" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-right ml-1"></i>
                العودة للوحة التحكم
            </a>
        </div>
    </div>
</div>