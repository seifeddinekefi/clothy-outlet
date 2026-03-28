<?php

/**
 * ============================================================
 * core/Database.php
 * ============================================================
 * PDO database abstraction layer.
 *
 * Features:
 *  - Singleton connection (one connection per request)
 *  - Prepared statements with named / positional binding
 *  - Transaction support (begin, commit, rollBack)
 *  - Fluent method chaining where sensible
 *  - Centralized PDO exception handling
 * ============================================================
 */

class Database
{
    // ── Singleton Instance ───────────────────────────────────
    private static ?Database $instance = null;

    private \PDO   $pdo;
    private \PDOStatement $stmt;

    // ── Constructor (private — use getInstance()) ────────────
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::ATTR_PERSISTENT         => false,
            \PDO::MYSQL_ATTR_FOUND_ROWS   => true,
        ];

        try {
            $this->pdo = new \PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Return (or create) the singleton Database instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Prevent cloning / unserialization
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new \RuntimeException('Database instance cannot be unserialized.');
    }

    // ── Query Preparation ────────────────────────────────────

    /**
     * Prepare a SQL statement.
     *
     * @param  string $sql  SQL with :named or ? placeholders
     * @return static
     */
    public function query(string $sql): static
    {
        try {
            $this->stmt = $this->pdo->prepare($sql);
        } catch (\PDOException $e) {
            $this->handleException($e);
        }
        return $this;
    }

    // ── Parameter Binding ────────────────────────────────────

    /**
     * Bind a single parameter to the prepared statement.
     *
     * @param  string|int $param   Named placeholder (:name) or positional index (1-based)
     * @param  mixed      $value
     * @param  int|null   $type    PDO::PARAM_* constant; auto-detected if null
     * @return static
     */
    public function bind(string|int $param, mixed $value, ?int $type = null): static
    {
        if ($type === null) {
            $type = match (true) {
                is_int($value)  => \PDO::PARAM_INT,
                is_bool($value) => \PDO::PARAM_BOOL,
                is_null($value) => \PDO::PARAM_NULL,
                default         => \PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Bind an associative array of parameters at once.
     *
     * @param  array<string, mixed> $params
     * @return static
     */
    public function bindAll(array $params): static
    {
        foreach ($params as $param => $value) {
            $this->bind($param, $value);
        }
        return $this;
    }

    // ── Execution ────────────────────────────────────────────

    /**
     * Execute the prepared statement.
     *
     * @return static
     */
    public function execute(): static
    {
        try {
            $this->stmt->execute();
        } catch (\PDOException $e) {
            $this->handleException($e);
        }
        return $this;
    }

    // ── Result Fetching ──────────────────────────────────────

    /**
     * Fetch all rows as an array of objects (or specified fetch mode).
     *
     * @return array<int, object>
     */
    public function resultSet(int $fetchMode = \PDO::FETCH_OBJ): array
    {
        return $this->stmt->fetchAll($fetchMode);
    }

    /**
     * Fetch a single row as an object.
     */
    public function single(int $fetchMode = \PDO::FETCH_OBJ): mixed
    {
        return $this->stmt->fetch($fetchMode);
    }

    /**
     * Return the number of affected rows.
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Return the last inserted auto-increment ID.
     */
    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    // ── Convenience Shortcuts ────────────────────────────────

    /**
     * Prepare + bind + execute in one call, then return all rows.
     *
     * @param  string               $sql
     * @param  array<string, mixed> $params
     * @return array<int, object>
     */
    public function select(string $sql, array $params = []): array
    {
        return $this->query($sql)->bindAll($params)->execute()->resultSet();
    }

    /**
     * Prepare + bind + execute, return single row.
     *
     * @param  string               $sql
     * @param  array<string, mixed> $params
     */
    public function selectOne(string $sql, array $params = []): mixed
    {
        return $this->query($sql)->bindAll($params)->execute()->single();
    }

    /**
     * Prepare + bind + execute for INSERT / UPDATE / DELETE.
     *
     * @param  string               $sql
     * @param  array<string, mixed> $params
     * @return bool
     */
    public function statement(string $sql, array $params = []): bool
    {
        try {
            $this->query($sql)->bindAll($params)->execute();
            return true;
        } catch (\PDOException $e) {
            $this->handleException($e);
            return false;
        }
    }

    // ── Transactions ─────────────────────────────────────────

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Wrap a callable in a transaction; auto-rollback on exception.
     *
     * @param  callable $callback
     * @return mixed  The value returned by $callback
     * @throws \Throwable
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    // ── Raw PDO Access ───────────────────────────────────────

    /**
     * Provide direct PDO access when needed for edge-cases.
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    // ── Error Handling ───────────────────────────────────────

    private function handleException(\PDOException $e): never
    {
        if (APP_DEBUG) {
            throw new \RuntimeException(
                'Database Error: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        // Production: log, do not expose internals
        error_log('DB Error [' . date('Y-m-d H:i:s') . ']: ' . $e->getMessage());
        http_response_code(500);
        exit('A database error occurred. Please try again later.');
    }
}
