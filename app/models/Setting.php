<?php

/**
 * ============================================================
 * app/models/Setting.php
 * ============================================================
 * Key-value application settings backed by the `settings` table.
 *
 * All reads/writes use the `key` column as identifier.
 * ============================================================
 */

class Setting extends Model
{
    protected string $table      = 'settings';
    protected string $primaryKey = 'id';

    /**
     * Get a single setting value by key.
     *
     * @param  string $key
     * @param  mixed  $default  Returned when the key does not exist.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $row = $this->db->selectOne(
            "SELECT `value` FROM `{$this->table}` WHERE `key` = :key LIMIT 1",
            [':key' => $key]
        );
        return $row ? $row->value : $default;
    }

    /**
     * Set (upsert) a single key/value pair.
     */
    public function set(string $key, string|null $value): bool
    {
        $existing = $this->db->selectOne(
            "SELECT `id` FROM `{$this->table}` WHERE `key` = :key LIMIT 1",
            [':key' => $key]
        );

        if ($existing) {
            return $this->update(
                ['value' => $value],
                '`key` = :key',
                [':key' => $key]
            );
        }

        return (bool) $this->insert(['key' => $key, 'value' => $value]);
    }

    /**
     * Upsert multiple key/value pairs at once.
     *
     * @param array<string, string|null> $data
     */
    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set((string) $key, $value !== null ? (string) $value : null);
        }
    }

    /**
     * Return all settings as a flat associative array: key => value.
     *
     * @return array<string, string|null>
     */
    public function allKeyed(): array
    {
        $rows = $this->all();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->key] = $row->value;
        }
        return $result;
    }
}
