<?php

/**
 * ============================================================
 * app/models/Category.php
 * ============================================================
 * Represents the `categories` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for product categories
 *  - Active category listing (storefront)
 *  - Slug-based lookup
 *  - Product count per category
 * ============================================================
 */

class Category extends Model
{
    protected string $table      = 'categories';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find a category by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all categories ordered by sort_order.
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `sort_order` ASC, `name` ASC"
        );
    }

    /**
     * Return only active categories (used by storefront navigation).
     *
     * @return array<int, object>
     */
    public function findActive(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` WHERE `is_active` = 1 ORDER BY `sort_order` ASC, `name` ASC"
        );
    }

    /**
     * Find a category by its unique slug.
     */
    public function findBySlug(string $slug): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `slug` = :slug LIMIT 1",
            [':slug' => $slug]
        );
    }

    /**
     * Return all active categories with a product count attached.
     *
     * @return array<int, object>
     */
    public function findAllWithProductCount(): array
    {
        return $this->db->select(
            "SELECT c.*,
                    COUNT(p.id) AS product_count
               FROM `categories` c
               LEFT JOIN `products` p
                      ON p.category_id = c.id
                     AND p.is_active   = 1
              WHERE c.is_active = 1
              GROUP BY c.id
              ORDER BY c.sort_order ASC, c.name ASC"
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new category.
     *
     * @param  array{name: string, slug: string, description?: string, image_path?: string, sort_order?: int} $data
     * @return string|false  New category id
     */
    public function create(array $data): string|false
    {
        return $this->insert([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?? null,
            'image_path'  => $data['image_path']  ?? null,
            'sort_order'  => $data['sort_order']  ?? 0,
            'is_active'   => $data['is_active']   ?? 1,
        ]);
    }

    /**
     * Update a category.
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateCategory(int $id, array $data): bool
    {
        unset($data['id'], $data['created_at']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Delete a category.
     * Will fail if products are still assigned (FK RESTRICT).
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    // ── Existence Checks ─────────────────────────────────────

    /**
     * Check slug uniqueness (exclude a given id for edit scenarios).
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `slug` = :slug";
        $params = [':slug' => $slug];
        if ($excludeId !== null) {
            $sql   .= ' AND `id` != :xid';
            $params[':xid'] = $excludeId;
        }
        $row = $this->db->selectOne($sql, $params);
        return (int) ($row->cnt ?? 0) > 0;
    }
}
