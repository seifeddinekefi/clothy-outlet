<?php

/**
 * ============================================================
 * core/Model.php
 * ============================================================
 * Base Model — abstract parent for all application models.
 *
 * Responsibilities:
 *  - Holds a reference to the Database singleton
 *  - Provides generic CRUD helper methods
 *  - Enforces table-name declaration in child models
 *  - No HTML, no request handling, no session logic
 * ============================================================
 */

abstract class Model
{
    protected Database $db;

    /**
     * Each child model MUST declare its table name.
     * Example: protected string $table = 'products';
     */
    protected string $table = '';

    /**
     * Primary key column (override if different in a model).
     */
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Generic Finders ──────────────────────────────────────

    /**
     * Return all rows from this model's table.
     *
     * @return array<int, object>
     */
    public function all(string $orderBy = '', string $direction = 'ASC'): array
    {
        $order = $orderBy
            ? ' ORDER BY ' . $this->escapeIdentifier($orderBy) . ' ' . $direction
            : '';
        return $this->db->select("SELECT * FROM {$this->table}{$order}");
    }

    /**
     * Find a single row by primary key.
     */
    public function find(int|string $id): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1",
            [':id' => $id]
        );
    }

    /**
     * Find rows matching a simple key = value condition.
     *
     * @return array<int, object>
     */
    public function findBy(string $column, mixed $value): array
    {
        $col = $this->escapeIdentifier($column);
        return $this->db->select(
            "SELECT * FROM {$this->table} WHERE {$col} = :val",
            [':val' => $value]
        );
    }

    /**
     * Find ONE row matching key = value.
     */
    public function findOneBy(string $column, mixed $value): mixed
    {
        $col = $this->escapeIdentifier($column);
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE {$col} = :val LIMIT 1",
            [':val' => $value]
        );
    }

    // ── Generic Write Operations ─────────────────────────────

    /**
     * Insert a row and return its new primary key.
     *
     * @param  array<string, mixed> $data  column => value pairs
     * @return string|false
     */
    public function insert(array $data): string|false
    {
        $columns     = implode(', ', array_map([$this, 'escapeIdentifier'], array_keys($data)));
        $placeholders = ':' . implode(', :', array_keys($data));
        $params      = array_combine(
            array_map(fn($k) => ':' . $k, array_keys($data)),
            array_values($data)
        );

        $this->db->statement(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $params
        );

        return $this->db->lastInsertId();
    }

    /**
     * Update rows matching a condition.
     *
     * @param  array<string, mixed> $data       Fields to update
     * @param  string               $where      SQL condition string (e.g. 'id = :wid')
     * @param  array<string, mixed> $whereParams Bound values for the WHERE clause
     * @return bool
     */
    public function update(array $data, string $where, array $whereParams = []): bool
    {
        $setParts = [];
        $params   = [];

        foreach ($data as $col => $val) {
            $placeholder         = ':set_' . $col;
            $setParts[]          = $this->escapeIdentifier($col) . ' = ' . $placeholder;
            $params[$placeholder] = $val;
        }

        $params = array_merge($params, $whereParams);

        return $this->db->statement(
            "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE {$where}",
            $params
        );
    }

    /**
     * Delete rows matching a condition.
     *
     * @param  string               $where
     * @param  array<string, mixed> $params
     * @return bool
     */
    public function delete(string $where, array $params = []): bool
    {
        return $this->db->statement(
            "DELETE FROM {$this->table} WHERE {$where}",
            $params
        );
    }

    /**
     * Delete a row by primary key.
     */
    public function deleteById(int|string $id): bool
    {
        return $this->delete("{$this->primaryKey} = :id", [':id' => $id]);
    }

    // ── Pagination ───────────────────────────────────────────

    /**
     * Return a paginated result set.
     *
     * @return array{data: array<int,object>, total: int, page: int, perPage: int, pages: int}
     */
    public function paginate(int $page = 1, int $perPage = PER_PAGE, string $where = '', array $params = []): array
    {
        $where    = $where ? "WHERE {$where}" : '';
        $offset   = ($page - 1) * $perPage;

        $total = (int) ($this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM {$this->table} {$where}",
            $params
        )->cnt ?? 0);

        $data = $this->db->select(
            "SELECT * FROM {$this->table} {$where} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int) ceil($total / $perPage),
        ];
    }

    // ── Utility ──────────────────────────────────────────────

    /**
     * Count all rows (optional WHERE).
     */
    public function count(string $where = '', array $params = []): int
    {
        $clause = $where ? "WHERE {$where}" : '';
        $row    = $this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM {$this->table} {$clause}",
            $params
        );
        return (int) ($row->cnt ?? 0);
    }

    /**
     * Check whether a row matching a condition exists.
     */
    public function exists(string $where, array $params = []): bool
    {
        return $this->count($where, $params) > 0;
    }

    /**
     * Expose the Database instance for custom queries in child models.
     */
    protected function getDb(): Database
    {
        return $this->db;
    }

    // ── Internal ─────────────────────────────────────────────

    /**
     * Wrap an identifier in backticks to prevent SQL injection via column/table names.
     * NOTE: This is NOT a substitute for parameterised queries on values.
     */
    private function escapeIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
