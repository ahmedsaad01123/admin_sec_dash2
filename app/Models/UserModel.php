<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class UserModel extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password', 'phone', 'remember_token', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public function findByEmail(string $email): array|false
    {
        $rows = $this->where('email', $email);
        return $rows[0] ?? false;
    }

    public function findByPhone(string $phone): array|false
    {
        $rows = $this->where('phone', $phone);
        return $rows[0] ?? false;
    }
}
