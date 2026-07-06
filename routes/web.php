<?php

declare(strict_types=1);

use App\Core\Middleware\AdminAuthMiddleware;

/**
 * تعريف مسارات التطبيق - لوحة تحكم الأدمن فقط
 * @var \App\Core\Router $router متاح تلقائيًا من App.php
 */

// روتات مفتوحة (من غير تسجيل دخول)
$router->get('/', 'Admin\AuthController@showLogin');
$router->post('/login', 'Admin\AuthController@login');
$router->post('/logout', 'Admin\AuthController@logout');

// روتات محمية - تتطلب تسجيل دخول أدمن
$router->get('/dashboard', 'Admin\DashboardController@index', ['middleware' => [AdminAuthMiddleware::class]]);
$router->get('/profile', 'Admin\ProfileController@show', ['middleware' => [AdminAuthMiddleware::class]]);
$router->post('/profile', 'Admin\ProfileController@updatePassword', ['middleware' => [AdminAuthMiddleware::class]]);


