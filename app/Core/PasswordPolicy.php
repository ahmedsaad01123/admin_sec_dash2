<?php

declare(strict_types=1);

namespace App\Core;

/**
 * فحص قوة كلمة المرور والتأكد إنها متوافقة مع سياسة أمان محددة
 * (منقولة بتصرف من مشروع "تمكين" مع تصليح مشكلتين:
 *  1. المطابقة الجزئية كانت بتاخد بالعرض في الاتجاهين، فكانت بترفض
 *     باسوردات قوية فعلاً لمجرد إنها بتحتوي حروف موجودة في كلمة شائعة
 *     (مثال: "MyFootballTeam2024!" كانت بترفض لاحتوائها على "football")
 *  2. مفيش Instance/Singleton هنا لأن الكلاس مالوش حالة (State) أصلاً،
 *     كل الدوال Static عشان تتنادى مباشرة زي باقي كلاسات App\Core
 *
 * الاستخدام:
 *   $result = PasswordPolicy::validate($password, ['username' => $username]);
 *   if (!$result['valid']) { ...  $result['errors'] بيحتوي كل الأخطاء ... }
 *
 * أو لو محتاج بس true/false:
 *   if (!PasswordPolicy::passes($password)) { ... }
 */
final class PasswordPolicy
{
    /** كلمات مرور شائعة يُمنع استخدامها أو تضمينها جوه كلمة المرور */
    private const COMMON_PASSWORDS = [
        'password', '123456', '123456789', '12345678', '12345', '1234567',
        '1234567890', '1234', 'qwerty', 'abc123', 'password123', 'admin',
        'letmein', 'welcome', 'monkey', 'dragon', 'master', 'sunshine',
        'princess', 'football', 'shadow', 'superman', 'iloveyou', '111111',
        '123123', '654321', '000000', 'qwertyuiop', 'asdfghjkl', 'zxcvbnm',
        '1q2w3e4r', '123qwe', 'qwe123', 'password1', 'admin123', 'root',
        'toor', 'pass', 'test', 'guest', 'user', 'login', 'welcome123',
    ];

    /** تسلسلات شائعة يُمنع تضمينها جوه كلمة المرور */
    private const SEQUENCES = [
        '0123456789', 'abcdefghijklmnopqrstuvwxyz', 'qwertyuiop',
        'asdfghjkl', 'zxcvbnm', '12345678', '23456789', '34567890',
    ];

    /**
     * التحقق الكامل من كلمة المرور
     *
     * @param array $userInfo بيانات المستخدم (username, email, full_name) عشان نتأكد
     *                        إن كلمة المرور مش مبنية عليها
     * @return array{valid: bool, errors: list<string>, strength: string, score: int}
     */
    public static function validate(string $password, array $userInfo = []): array
    {
        $errors = [];

        $minLength = (int) Config::get('password_policy.min_length', 10);
        $maxLength = (int) Config::get('password_policy.max_length', 128);

        if (mb_strlen($password) < $minLength) {
            $errors[] = "كلمة المرور لازم تكون {$minLength} أحرف على الأقل.";
        }

        if (mb_strlen($password) > $maxLength) {
            $errors[] = "كلمة المرور أطول من الحد المسموح ({$maxLength} حرف).";
        }

        if (Config::get('password_policy.require_uppercase', true) && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'كلمة المرور لازم تحتوي على حرف كبير (A-Z) على الأقل.';
        }

        if (Config::get('password_policy.require_lowercase', true) && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'كلمة المرور لازم تحتوي على حرف صغير (a-z) على الأقل.';
        }

        if (Config::get('password_policy.require_numbers', true) && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'كلمة المرور لازم تحتوي على رقم واحد على الأقل.';
        }

        if (Config::get('password_policy.require_special', true) && !preg_match('/[!@#$%^&*(),.?":{}|<>_\-]/', $password)) {
            $errors[] = 'كلمة المرور لازم تحتوي على رمز خاص واحد على الأقل (!@#$%^&*...).';
        }

        if (Config::get('password_policy.prevent_common', true) && self::isCommonPassword($password)) {
            $errors[] = 'كلمة المرور شائعة جداً وسهل تخمينها، اختار كلمة مرور مختلفة.';
        }

        if (Config::get('password_policy.prevent_user_info', true) && self::containsUserInfo($password, $userInfo)) {
            $errors[] = 'كلمة المرور متنفعش تحتوي على اسم المستخدم أو الإيميل بتاعك.';
        }

        $maxRepeated = (int) Config::get('password_policy.max_repeated', 3);
        if (self::hasTooManyRepeatedChars($password, $maxRepeated)) {
            $errors[] = "كلمة المرور متنفعش تحتوي على أكتر من {$maxRepeated} حروف متكررة متتالية.";
        }

        if (Config::get('password_policy.prevent_sequences', true) && self::containsSequence($password)) {
            $errors[] = 'كلمة المرور متنفعش تحتوي على تسلسل واضح زي "123456" أو "qwerty".';
        }

        return [
            'valid'    => $errors === [],
            'errors'   => $errors,
            'strength' => self::strengthLabel($password),
            'score'    => self::strengthScore($password),
        ];
    }

    /** نسخة مختصرة بترجع true/false بس */
    public static function passes(string $password, array $userInfo = []): bool
    {
        return self::validate($password, $userInfo)['valid'];
    }

    /**
     * هل كلمة المرور شائعة أو بتحتوي كلمة شائعة كاملة؟
     * ملحوظة: بنتحقق في اتجاه واحد بس (هل الكلمة الشائعة موجودة جوه كلمة المرور)
     * مش العكس، عشان منرفضش كلمات مرور طويلة قوية لمجرد التشابه الجزئي العرضي
     */
    private static function isCommonPassword(string $password): bool
    {
        $lower = mb_strtolower($password);

        foreach (self::COMMON_PASSWORDS as $common) {
            if ($lower === $common || str_contains($lower, $common)) {
                return true;
            }
        }

        return false;
    }

    private static function containsUserInfo(string $password, array $userInfo): bool
    {
        if ($userInfo === []) {
            return false;
        }

        $lower = mb_strtolower($password);

        if (!empty($userInfo['username']) && mb_strlen((string) $userInfo['username']) >= 3) {
            if (str_contains($lower, mb_strtolower((string) $userInfo['username']))) {
                return true;
            }
        }

        if (!empty($userInfo['email'])) {
            $localPart = explode('@', mb_strtolower((string) $userInfo['email']))[0];
            if (mb_strlen($localPart) >= 3 && str_contains($lower, $localPart)) {
                return true;
            }
        }

        if (!empty($userInfo['full_name'])) {
            foreach (explode(' ', mb_strtolower((string) $userInfo['full_name'])) as $part) {
                if (mb_strlen($part) >= 3 && str_contains($lower, $part)) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function hasTooManyRepeatedChars(string $password, int $maxRepeated): bool
    {
        $length = mb_strlen($password);
        $repeated = 1;

        for ($i = 1; $i < $length; $i++) {
            if (mb_substr($password, $i, 1) === mb_substr($password, $i - 1, 1)) {
                $repeated++;
                if ($repeated > $maxRepeated) {
                    return true;
                }
            } else {
                $repeated = 1;
            }
        }

        return false;
    }

    private static function containsSequence(string $password): bool
    {
        $lower = mb_strtolower($password);

        foreach (self::SEQUENCES as $sequence) {
            if (str_contains($lower, $sequence) || str_contains($lower, strrev($sequence))) {
                return true;
            }
        }

        return false;
    }

    private static function strengthScore(string $password): int
    {
        $score = 0;
        $length = mb_strlen($password);

        if ($length >= 8) {
            $score++;
        }
        if ($length >= 12) {
            $score++;
        }
        if ($length >= 16) {
            $score++;
        }
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        }
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        if (preg_match('/[0-9]/', $password)) {
            $score++;
        }
        if (preg_match('/[!@#$%^&*(),.?":{}|<>_\-]/', $password)) {
            $score++;
        }

        return $score;
    }

    private static function strengthLabel(string $password): string
    {
        $score = self::strengthScore($password);

        return match (true) {
            $score >= 6 => 'قوية جداً',
            $score >= 5 => 'قوية',
            $score >= 3 => 'متوسطة',
            $score >= 1 => 'ضعيفة',
            default     => 'ضعيفة جداً',
        };
    }
}
