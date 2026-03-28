<?php

/**
 * ============================================================
 * app/models/Admin.php
 * ============================================================
 * Represents the `admins` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD operations on admin accounts
 *  - Credential verification (password_verify — no hashing here)
 *  - Role join helpers
 * ============================================================
 */

class Admin extends Model
{
    protected string $table      = 'admins';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find an admin by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Retrieve all admins with their role name (JOIN).
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT a.*, r.name AS role_name
               FROM `admins` a
               JOIN `roles`  r ON r.id = a.role_id
              ORDER BY a.id ASC"
        );
    }

    /**
     * Find an admin by email address.
     */
    public function findByEmail(string $email): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `email` = :email LIMIT 1",
            [':email' => $email]
        );
    }

    /**
     * Find admin with role data joined.
     */
    public function findWithRole(int $id): mixed
    {
        return $this->db->selectOne(
            "SELECT a.*, r.name AS role_name, r.permissions AS role_permissions
               FROM `admins` a
               JOIN `roles`  r ON r.id = a.role_id
              WHERE a.id = :id
              LIMIT 1",
            [':id' => $id]
        );
    }

    // ── Authentication Helpers ────────────────────────────────

    /**
     * Verify plain-text password against stored hash.
     * This does NOT hash — hashing is the controller's job.
     */
    public function verifyPassword(string $plainPassword, string $storedHash): bool
    {
        return password_verify($plainPassword, $storedHash);
    }

    /**
     * Check whether password needs to be rehashed.
     * Call after successful login; if true, rehash and update.
     */
    public function passwordNeedsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Update the last_login timestamp for an admin.
     */
    public function touchLastLogin(int $id): bool
    {
        return $this->update(
            ['last_login' => date('Y-m-d H:i:s')],
            '`id` = :id',
            [':id' => $id]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new admin account.
     * The password passed in MUST already be hashed by the caller.
     *
     * @param  array{name: string, email: string, password: string, role_id?: int} $data
     * @return string|false  New admin id
     */
    public function create(array $data): string|false
    {
        return $this->insert([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'],   // pre-hashed
            'role_id'   => $data['role_id'] ?? 2,
            'is_active' => $data['is_active'] ?? 1,
        ]);
    }

    /**
     * Update an admin's profile fields.
     * Pass 'password' only if updating the password (must be pre-hashed).
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateAdmin(int $id, array $data): bool
    {
        // Prevent accidental plain-text password storage
        if (isset($data['password']) && strlen($data['password']) < 20) {
            throw new \InvalidArgumentException('Admin password must be a valid hash.');
        }

        // Strip fields that must not be updated this way
        unset($data['id'], $data['created_at']);

        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Soft-deactivate an admin (set is_active = 0).
     */
    public function deactivate(int $id): bool
    {
        return $this->update(['is_active' => 0], '`id` = :id', [':id' => $id]);
    }

    /**
     * Delete an admin by id.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    // ── Existence Checks ─────────────────────────────────────

    /**
     * Check if an email is already registered.
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `email` = :email";
        $params = [':email' => $email];

        if ($excludeId !== null) {
            $sql   .= ' AND `id` != :xid';
            $params[':xid'] = $excludeId;
        }

        $row = $this->db->selectOne($sql, $params);
        return (int) ($row->cnt ?? 0) > 0;
    }
}
