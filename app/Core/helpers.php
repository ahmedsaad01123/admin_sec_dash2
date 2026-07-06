<?php

declare(strict_types=1);

if (!function_exists('__')) {
    function __(string $key, array $replacements = []): string
    {
        return \App\Core\Language::get($key, $replacements);
    }
}

if (!function_exists('e')) {
    /**
     * دالة الـ Escaping الرسمية لأي بيانات بتتطبع في الـ View.
     * استخدمها دايمًا لما تطبع بيانات جاية من المستخدم أو من الداتابيز:
     *   <?= e($user['name']) ?>
     *
     * ملحوظة: البيانات الراجعة من input()/all() بقت "خام" (من غير Escaping)،
     * فالـ Escaping بقى مسؤولية الـ View وقت الطباعة بس، مش وقت الإدخال.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    /**
     * استرجاع قيمة قديمة اتبعتت في فورم فشل في الـ Validation
     * عشان تملأ بيها الحقل تاني بدل ما المستخدم يكتب كل حاجة من الأول:
     *   <input value="<?= e(old('email')) ?>">
     */
    function old(string $key, mixed $default = ''): mixed
    {
        static $old = null;

        // بنقرأ الـ Flash مرة واحدة بس في نفس الـ Request
        // عشان Session::flash() بتمسح القيمة أول ما تتقرا
        if ($old === null) {
            $old = \App\Core\Session::flash('old') ?? [];
        }

        return $old[$key] ?? $default;
    }
}

if (!function_exists('errors')) {
    /**
     * كل أخطاء الـ Validation الجاية من آخر Redirect (لو فيه)
     * @return array<string, list<string>>
     */
    function errors(): array
    {
        static $errors = null;

        if ($errors === null) {
            $errors = \App\Core\Session::flash('errors') ?? [];
        }

        return $errors;
    }
}

if (!function_exists('error')) {
    /**
     * أول رسالة خطأ لحقل معين، أو null لو مفيش خطأ:
     *   <?php if (error('email')): ?> <span><?= error('email') ?></span> <?php endif; ?>
     */
    function error(string $field): ?string
    {
        return errors()[$field][0] ?? null;
    }
}