<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * الكلاس الأساسي لكل الموديلز
 * بيوفر عمليات CRUD أساسية باستخدام Prepared Statements
 * لحماية كاملة من هجمات SQL Injection
 *
 * ملحوظة الأمان: لو عايز تمنع Mass Assignment (تمرير أعمدة غير مقصودة من اليوزر)،
 * عرّف $fillable في الموديل بتاعك بالأعمدة المسموح بيها فقط.
 * مثال: protected array $fillable = ['name', 'email', 'password'];
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * الأعمدة المسموح بتحديثها/إنشائها من خلال create() و update().
     * لو فاضية، update/create هيرفضوا العمل عشان يجبرونك تعرّفها صراحة.
     */
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::connection(Config::get('database'));
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function all(): array
    {
        return $this->db->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    /**
     * فلترة البيانات: بترجع بس الأعمدة الموجودة في $fillable
     * لو $fillable فاضية بترجع مصفوفة فاضية (رفض تلقائي)
     */
    private function filterFillable(array $data): array
    {
        if ($this->fillable === []) {
            return [];
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * التحقق من اسم العمود: حروف إنجليزية، أرقام، و underscore بس
     */
    private function validateColumn(string $column): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }
    }

    public function where(string $column, mixed $value): array
    {
        $this->validateColumn($column);

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);

        return $stmt->fetchAll();
    }

    public function create(array $data): string|false
    {
        $filtered = $this->filterFillable($data);
        foreach (array_keys($filtered) as $column) {
            $this->validateColumn($column);
        }        

        if ($filtered === []) {
            throw new \RuntimeException(
                'No fillable columns defined or no allowed data provided in: ' . static::class
            );
        }

        $columns = implode(', ', array_keys($filtered));
        $placeholders = ':' . implode(', :', array_keys($filtered));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($filtered);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);

        if ($filtered === []) {
            throw new \RuntimeException(
                'No fillable columns defined or no allowed data provided in: ' . static::class
            );
        }

        // نتحقق من كل اسم عمود في SET عشان نمنع SQL Injection
        foreach (array_keys($filtered) as $column) {
            $this->validateColumn($column);
        }

        $set = implode(', ', array_map(
            static fn(string $col): string => "{$col} = :{$col}",
            array_keys($filtered)
        ));

        $filtered[$this->primaryKey] = $id;

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :{$this->primaryKey}"
        );

        return $stmt->execute($filtered);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }
}