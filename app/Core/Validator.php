<?php

declare(strict_types=1);

namespace App\Core;

/**
 * كلاس تحقق بسيط من المدخلات (Validation)
 * بيدعم قواعد متعددة مفصولة بـ "|" زي:
 *   'email' => 'required|email'
 *   'amount' => 'required|numeric|min:1'
 *
 * الاستخدام المباشر:
 *   $validator = Validator::make($data, ['name' => 'required|min:3']);
 *   if ($validator->fails()) { ... }
 *
 * أو أسهل، من جوه أي كنترولر (شوف Controller::validate):
 *   $data = $this->validate(['name' => 'required|min:3']);
 */
final class Validator
{
    /** @var array<string, list<string>> */
    private array $errors = [];

    /** رسائل القواعد بالعربي (تقدر تضيف/تعدّل براحتك) */
    private const MESSAGES = [
        'required' => 'حقل :field مطلوب.',
        'email'    => 'حقل :field لازم يكون بريد إلكتروني صحيح.',
        'numeric'  => 'حقل :field لازم يكون رقم.',
        'integer'  => 'حقل :field لازم يكون رقم صحيح.',
        'string'   => 'حقل :field لازم يكون نص.',
        'min'      => 'حقل :field لازم يكون على الأقل :param.',
        'max'      => 'حقل :field أكبر من الحد المسموح (:param).',
        'in'       => 'قيمة حقل :field غير صحيحة.',
        'confirmed' => 'تأكيد حقل :field غير متطابق.',
        'alpha'    => 'حقل :field لازم يحتوي على حروف فقط.',
        'alpha_num' => 'حقل :field لازم يحتوي على حروف وأرقام فقط.',
        'date'     => 'حقل :field لازم يكون تاريخ صحيح.',
    ];

    /** أسماء الحقول بالعربي عشان تظهر في رسائل الخطأ (اختياري) */
    private array $fieldNames = [];

    private function __construct(
        private readonly array $data,
        private readonly array $rules,
    ) {
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data, $rules);
        $validator->run();

        return $validator;
    }

    /**
     * تحديد أسماء عربية للحقول تظهر في رسائل الخطأ
     * مثال: $validator->setFieldNames(['email' => 'البريد الإلكتروني']);
     */
    public function setFieldNames(array $names): self
    {
        $this->fieldNames = $names;

        return $this;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleLine) {
            $fieldRules = explode('|', $ruleLine);
            $value = $this->data[$field] ?? null;

            $isRequired = in_array('required', $fieldRules, true);
            $isEmpty = $value === null || $value === '';

            // لو الحقل مش مطلوب وقيمته فاضية، متبقاش تطبّق باقي القواعد عليه
            if (!$isRequired && $isEmpty) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule, $fieldRules);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, array $allRules): void
    {
        [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);

        $passed = match ($name) {
            'required'  => $value !== null && $value !== '',
            'email'     => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'numeric'   => is_numeric($value),
            'integer'   => filter_var($value, FILTER_VALIDATE_INT) !== false,
            'string'    => is_string($value),
            'alpha'     => is_string($value) && preg_match('/^\p{L}+$/u', $value) === 1,
            'alpha_num' => is_string($value) && preg_match('/^[\p{L}\d]+$/u', $value) === 1,
            'date'      => is_string($value) && strtotime($value) !== false,
            'min'       => $this->checkMin($value, (float) $param, $allRules),
            'max'       => $this->checkMax($value, (float) $param, $allRules),
            'in'        => in_array((string) $value, explode(',', (string) $param), true),
            'confirmed' => ($value ?? '') === ($this->data["{$field}_confirmation"] ?? null),
            default     => true, // قاعدة غير معروفة: نتجاهلها بدل ما نكسر التطبيق
        };

        if (!$passed) {
            $this->addError($field, $name, $param);
        }
    }

    /**
     * min بتشتغل بمعنى مختلف حسب نوع الحقل:
     * - لو رقم (numeric ضمن قواعد نفس الحقل): بيتحقق من القيمة نفسها
     * - غير كده: بيتحقق من طول النص (بالحروف، يدعم العربي بشكل صحيح)
     */
    private function checkMin(mixed $value, float $param, array $allRules): bool
    {
        if (in_array('numeric', $allRules, true) || in_array('integer', $allRules, true)) {
            return is_numeric($value) && (float) $value >= $param;
        }

        return mb_strlen((string) $value) >= $param;
    }

    private function checkMax(mixed $value, float $param, array $allRules): bool
    {
        if (in_array('numeric', $allRules, true) || in_array('integer', $allRules, true)) {
            return is_numeric($value) && (float) $value <= $param;
        }

        return mb_strlen((string) $value) <= $param;
    }

    private function addError(string $field, string $rule, ?string $param): void
    {
        $message = self::MESSAGES[$rule] ?? 'حقل :field غير صحيح.';
        $fieldLabel = $this->fieldNames[$field] ?? $field;

        $message = str_replace(
            [':field', ':param'],
            [$fieldLabel, (string) $param],
            $message
        );

        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string, list<string>> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
