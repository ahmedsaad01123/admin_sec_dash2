<?php

declare(strict_types=1);

use App\Core\Env;

return [

    'app' => [
        'name'  => Env::get('APP_NAME', 'MyApp'),
        'env'   => Env::get('APP_ENV', 'production'),
        'debug' => (bool) Env::get('APP_DEBUG', false),
        'url'   => Env::get('APP_URL', 'http://localhost'),
        'admin_prefix' => Env::get('ADMIN_PREFIX', 'admin'),
    ],

    'database' => [
        'host'     => Env::get('DB_HOST', '127.0.0.1'),
        'port'     => Env::get('DB_PORT', '3306'),
        'database' => Env::get('DB_DATABASE', ''),
        'username' => Env::get('DB_USERNAME', ''),
        'password' => Env::get('DB_PASSWORD', ''),
        'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
    ],

    'session' => [
        'lifetime' => (int) Env::get('SESSION_LIFETIME', 120),
        'secure'   => (bool) Env::get('SESSION_SECURE', false),
    ],

    'password_policy' => [
        'min_length'         => (int) Env::get('PASSWORD_MIN_LENGTH', 10),
        'max_length'         => (int) Env::get('PASSWORD_MAX_LENGTH', 128),
        'require_uppercase'  => (bool) Env::get('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase'  => (bool) Env::get('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers'    => (bool) Env::get('PASSWORD_REQUIRE_NUMBERS', true),
        'require_special'    => (bool) Env::get('PASSWORD_REQUIRE_SPECIAL', true),
        'prevent_common'     => (bool) Env::get('PASSWORD_PREVENT_COMMON', true),
        'prevent_user_info'  => (bool) Env::get('PASSWORD_PREVENT_USER_INFO', true),
        'prevent_sequences'  => (bool) Env::get('PASSWORD_PREVENT_SEQUENCES', true),
        'max_repeated'       => (int) Env::get('PASSWORD_MAX_REPEATED', 3),
    ],

    'rate_limit' => [
        'max_attempts'    => (int) Env::get('RATE_LIMIT_MAX_ATTEMPTS', 5),
        'window_seconds'  => (int) Env::get('RATE_LIMIT_WINDOW_SECONDS', 900),
        'lockout_seconds' => (int) Env::get('RATE_LIMIT_LOCKOUT_SECONDS', 900),
    ],

];
