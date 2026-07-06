<div class="max-w-7xl mx-auto">
    <!-- Welcome Card -->
    <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">مرحبًا بك، <?= htmlspecialchars($admin_name ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-gray-600">أهلاً بك في لوحة التحكم</p>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">المستخدمين</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">المشاريع</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-project-diagram text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">التقارير</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">الإشعارات</p>
                    <p class="text-3xl font-bold text-gray-800">0</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bell text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">إجراءات سريعة</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/profile" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <span class="font-semibold text-gray-800">تغيير كلمة المرور</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <span class="font-semibold text-gray-800">إضافة مستخدم جديد</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
                <span class="font-semibold text-gray-800">عرض التقارير</span>
            </a>
        </div>
    </div>
</div>