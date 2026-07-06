<!-- Welcome Card -->
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fa fa-user-tie fa-2x"></i>
            </div>
            <div>
                <h3 class="card-title mb-1"><?= __('welcome') ?>، <?= htmlspecialchars($admin_name ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="card-text text-muted"><?= __('welcome') ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-1">Users</p>
                <h3 class="mb-0">0</h3>
            </div>
            <div class="text-primary">
                <i class="fa fa-users fa-2x icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-1">Projects</p>
                <h3 class="mb-0">0</h3>
            </div>
            <div class="text-success">
                <i class="fa fa-project-diagram fa-2x icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-1">Reports</p>
                <h3 class="mb-0">0</h3>
            </div>
            <div class="text-info">
                <i class="fa fa-bar-chart fa-2x icon"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="stat-card d-flex justify-content-between align-items-center">
            <div>
                <p class="text-muted mb-1">Notifications</p>
                <h3 class="mb-0">0</h3>
            </div>
            <div class="text-warning">
                <i class="fa fa-bell fa-2x icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-3">Quick Actions</h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="/profile" class="btn btn-outline-primary w-100 text-start">
                    <i class="fa fa-cog"></i> <?= __('change_password') ?>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="#" class="btn btn-outline-success w-100 text-start">
                    <i class="fa fa-plus"></i> Add User
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="#" class="btn btn-outline-info w-100 text-start">
                    <i class="fa fa-file-alt"></i> View Reports
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card { padding: 20px; border-radius: 8px; background: #fff; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .stat-card .icon { font-size: 32px; opacity: 0.8; }
</style>