<?php

/**
 * ============================================================
 * app/models/Role.php
 * ============================================================
 * Represents the `roles` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD operations on roles
 *  - Decode/encode JSON permissions
 *  - Provide role lookup helpers for the auth system
 * ============================================================
 */

class Role extends Model
{
    protected string $table      = 'roles';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find a role by its primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Retrieve all roles ordered by id.
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `id` ASC"
        );
    }

    /**
     * Find a role by name (case-insensitive).
     */
    public function findByName(string $name): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE LOWER(`name`) = LOWER(:name) LIMIT 1",
            [':name' => $name]
        );
    }

    // ── Permissions Helpers ───────────────────────────────────

    /**
     * Return the permissions array for a given role id.
     *
     * @return array<string, bool>
     */
    public function getPermissions(int $roleId): array
    {
        $role = $this->find($roleId);
        if (!$role) {
            return [];
        }
        $decoded = json_decode($role->permissions, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Check whether a role has a specific permission key.
     */
    public function hasPermission(int $roleId, string $permission): bool
    {
        $permissions = $this->getPermissions($roleId);
        return !empty($permissions[$permission]);
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new role.
     *
     * @param  string               $name
     * @param  array<string, bool>  $permissions  e.g. ['products' => true, 'orders' => false]
     * @return string|false  New role id on success
     */
    public function create(string $name, array $permissions = []): string|false
    {
        return $this->insert([
            'name'        => $name,
            'permissions' => json_encode($permissions, JSON_THROW_ON_ERROR),
        ]);
    }

    /**
     * Update role name and/or permissions.
     *
     * @param  int                  $id
     * @param  string|null          $name
     * @param  array<string, bool>|null $permissions
     * @return bool
     */
    public function updateRole(int $id, ?string $name = null, ?array $permissions = null): bool
    {
        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($permissions !== null) {
            $data['permissions'] = json_encode($permissions, JSON_THROW_ON_ERROR);
        }
        if (empty($data)) {
            return false;
        }

        return $this->update($data, '`id` = :wid', [':wid' => $id]);
    }

    /**
     * Delete a role by id.
     * Will fail (FK RESTRICT) if admins are still assigned to this role.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }
}
