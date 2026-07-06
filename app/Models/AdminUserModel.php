<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class AdminUserModel extends Model
{
    protected string $table = 'admin_users';
    protected array $fillable = ['username', 'email', 'password', 'remember_token', 'last_login_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public function findByUsername(string $username): array|false
    {
        $rows = $this->where('username', $username);
        return $rows[0] ?? false;
    }
}