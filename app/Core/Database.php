<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * إدارة الاتصال بقاعدة البيانات باستخدام PDO
 * بنستخدم نمط Singleton عشان مايتفتحش أكتر من اتصال في نفس الطلب
 */
final class Database
{
    private static ?PDO $instance = null;

    public static function connection(array $config): PDO
    {
        if (self::$instance instanceof PDO) {
            return self::$instance;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // تعطيل الـ Emulated Prepares يخلي الـ Prepared Statements حقيقية
                // وده أهم سطر في حماية الكويريز من SQL Injection
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // ما بنسيبش رسالة الخطأ الأصلية (اللي ممكن تحتوي بيانات اتصال) توصل للمستخدم
            throw new RuntimeException('تعذر الاتصال بقاعدة البيانات، برجاء المحاولة لاحقًا.', previous: $e);
        }

        return self::$instance;
    }
}
