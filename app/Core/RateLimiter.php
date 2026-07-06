<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * حماية من محاولات تسجيل الدخول المتكررة (Brute Force) باستخدام الداتابيز
 * (منقول بتصرف من مشروع "تمكين" مع تصليح مشكلتين:
 *  1. كود التنظيف الدوري كان فيه Bug فعلي (`mt_rand() < 0.1`) بيخلي التنظيف
 *     عمره ما يشتغل، لأن mt_rand() بترجع رقم صحيح كبير مش نسبة من 0 إلى 1
 *  2. الـ Window (نطاق الوقت) كان بيفضل عالق على نفس الصف القديم من غير Reset
 *     واضح، فبنينا هنا منطق أوضح: لو الوقت عدّى، نعمل الصف "يبدأ من جديد" فعلاً
 *
 * الفكرة: كل محاولة دخول بتتسجل على مفتاح (IP + المعرّف زي اليوزرنيم)،
 * ولو المحاولات الفاشلة زادت عن الحد المسموح جوه فترة زمنية معينة،
 * بيتقفل الدخول لحد ما وقت القفل يخلص.
 *
 * الاستخدام في أي كنترولر (قبل التحقق من كلمة المرور):
 *
 *   $limit = RateLimiter::attempt($username, $_SERVER['REMOTE_ADDR']);
 *   if (!$limit['allowed']) {
 *       // $limit['retry_after'] بالثواني
 *       throw new \RuntimeException('محاولات كتير، حاول تاني بعد شوية.');
 *   }
 *   // ... تحقق من الباسورد ...
 *   if ($passwordCorrect) {
 *       RateLimiter::clear($username, $_SERVER['REMOTE_ADDR']);
 *   }
 */
final class RateLimiter
{
    /**
     * تسجيل محاولة جديدة والتحقق هل مسموح بيها ولا لأ
     *
     * @return array{allowed: bool, locked: bool, remaining: int, retry_after: int}
     */
    public static function attempt(string $identifier, string $ipAddress, string $type = 'login'): array
    {
        $db = Database::connection(Config::get('database'));
        $identifier = mb_strtolower(trim($identifier));

        $maxAttempts    = (int) Config::get('rate_limit.max_attempts', 5);
        $windowSeconds  = (int) Config::get('rate_limit.window_seconds', 900);
        $lockoutSeconds = (int) Config::get('rate_limit.lockout_seconds', 900);

        // احتمال 10% نعمل تنظيف للسجلات القديمة (بديل صحيح عن mt_rand() < 0.1)
        if (random_int(1, 100) <= 10) {
            self::cleanup($db);
        }

        $row = self::findRow($db, $ipAddress, $identifier);

        // لو مقفول فعلاً ولسه في وقت القفل
        if ($row && (int) $row['is_locked'] === 1 && $row['locked_until'] !== null
            && strtotime((string) $row['locked_until']) > time()
        ) {
            return [
                'allowed'     => false,
                'locked'      => true,
                'remaining'   => 0,
                'retry_after' => strtotime((string) $row['locked_until']) - time(),
            ];
        }

        // لو مفيش صف أصلاً، أو الصف قديم وخرج بره نطاق الوقت المسموح، نبدأ عداد جديد
        $windowExpired = $row !== false && strtotime((string) $row['window_start']) < (time() - $windowSeconds);

        if ($row === false || $windowExpired) {
            self::resetRow($db, $ipAddress, $identifier);
            $currentAttempts = 0;
        } else {
            $currentAttempts = (int) $row['attempt_count'];
        }

        if ($currentAttempts >= $maxAttempts) {
            self::lockRow($db, $ipAddress, $identifier, $lockoutSeconds);
            self::logViolation($db, $ipAddress, $identifier, $currentAttempts, $type);

            return [
                'allowed'     => false,
                'locked'      => true,
                'remaining'   => 0,
                'retry_after' => $lockoutSeconds,
            ];
        }

        self::recordAttempt($db, $ipAddress, $identifier);

        return [
            'allowed'     => true,
            'locked'      => false,
            'remaining'   => $maxAttempts - $currentAttempts - 1,
            'retry_after' => 0,
        ];
    }

    /**
     * تصفير المحاولات بعد تسجيل دخول ناجح
     * لازم تتنادى فور ما الباسورد يتحقق صح
     */
    public static function clear(string $identifier, string $ipAddress): void
    {
        $db = Database::connection(Config::get('database'));

        $stmt = $db->prepare(
            'DELETE FROM rate_limits WHERE ip_address = :ip AND identifier = :identifier'
        );
        $stmt->execute(['ip' => $ipAddress, 'identifier' => mb_strtolower(trim($identifier))]);
    }

    private static function findRow(PDO $db, string $ip, string $identifier): array|false
    {
        $stmt = $db->prepare(
            'SELECT * FROM rate_limits WHERE ip_address = :ip AND identifier = :identifier LIMIT 1'
        );
        $stmt->execute(['ip' => $ip, 'identifier' => $identifier]);

        return $stmt->fetch();
    }

    private static function resetRow(PDO $db, string $ip, string $identifier): void
    {
        $stmt = $db->prepare(
            'INSERT INTO rate_limits (ip_address, identifier, attempt_count, window_start, last_attempt, is_locked, locked_until)
             VALUES (:ip, :identifier, 0, NOW(), NOW(), 0, NULL)
             ON DUPLICATE KEY UPDATE
                attempt_count = 0,
                window_start  = NOW(),
                last_attempt  = NOW(),
                is_locked     = 0,
                locked_until  = NULL'
        );
        $stmt->execute(['ip' => $ip, 'identifier' => $identifier]);
    }

    private static function recordAttempt(PDO $db, string $ip, string $identifier): void
    {
        $stmt = $db->prepare(
            'UPDATE rate_limits
             SET attempt_count = attempt_count + 1, last_attempt = NOW()
             WHERE ip_address = :ip AND identifier = :identifier'
        );
        $stmt->execute(['ip' => $ip, 'identifier' => $identifier]);
    }

    private static function lockRow(PDO $db, string $ip, string $identifier, int $lockoutSeconds): void
    {
        $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);

        $stmt = $db->prepare(
            'UPDATE rate_limits
             SET is_locked = 1, locked_until = :locked_until
             WHERE ip_address = :ip AND identifier = :identifier'
        );
        $stmt->execute(['locked_until' => $lockedUntil, 'ip' => $ip, 'identifier' => $identifier]);
    }

    private static function logViolation(PDO $db, string $ip, string $identifier, int $attempts, string $type): void
    {
        $stmt = $db->prepare(
            'INSERT INTO rate_limit_violations (ip_address, identifier, attempt_count, violation_type, user_agent, violation_time)
             VALUES (:ip, :identifier, :attempts, :type, :ua, NOW())'
        );
        $stmt->execute([
            'ip'        => $ip,
            'identifier' => $identifier,
            'attempts'  => $attempts,
            'type'      => $type,
            'ua'        => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    }

    private static function cleanup(PDO $db): void
    {
        // سجلات قديمة جداً وغير مقفولة حالياً
        $db->exec(
            "DELETE FROM rate_limits
             WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 DAY)
             AND (is_locked = 0 OR locked_until < NOW())"
        );

        // سجلات مخالفات أقدم من شهر
        $db->exec(
            "DELETE FROM rate_limit_violations
             WHERE violation_time < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
    }
}
