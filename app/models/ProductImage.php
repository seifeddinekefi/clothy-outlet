<?php

/**
 * ============================================================
 * app/models/ProductImage.php
 * ============================================================
 * Represents the `product_images` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for product image gallery
 *  - Primary image management (only one per product)
 *  - Ordered retrieval
 * ============================================================
 */

class ProductImage extends Model
{
    protected string $table      = 'product_images';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find an image record by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all image records (not typically needed; use findByProduct).
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `product_id` ASC, `sort_order` ASC"
        );
    }

    /**
     * Return all images for a given product, ordered by sort_order.
     *
     * @return array<int, object>
     */
    public function findByProduct(int $productId): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}`
              WHERE `product_id` = :pid
              ORDER BY `is_primary` DESC, `sort_order` ASC",
            [':pid' => $productId]
        );
    }

    /**
     * Return the primary (thumbnail) image for a product.
     */
    public function findPrimary(int $productId): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}`
              WHERE `product_id` = :pid AND `is_primary` = 1
              LIMIT 1",
            [':pid' => $productId]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Add an image to a product.
     *
     * @param  int         $productId
     * @param  string      $imagePath   Relative path stored in DB
     * @param  string|null $altText
     * @param  bool        $isPrimary   If true, demotes all existing primaries first
     * @param  int         $sortOrder
     * @return string|false  New image id
     */
    public function create(int $productId, string $imagePath, ?string $altText = null, bool $isPrimary = false, int $sortOrder = 0): string|false
    {
        if ($isPrimary) {
            $this->clearPrimary($productId);
        }

        return $this->insert([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'alt_text'   => $altText,
            'is_primary' => (int) $isPrimary,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Update image metadata.
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateImage(int $id, array $data): bool
    {
        unset($data['id'], $data['product_id']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Set a specific image as the primary for its product.
     * Demotes all sibling images first.
     */
    public function setPrimary(int $imageId, int $productId): bool
    {
        $this->clearPrimary($productId);
        return $this->update(['is_primary' => 1], '`id` = :id', [':id' => $imageId]);
    }

    /**
     * Demote all primary images for a product (set is_primary = 0).
     */
    public function clearPrimary(int $productId): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}` SET `is_primary` = 0 WHERE `product_id` = :pid",
            [':pid' => $productId]
        );
    }

    /**
     * Delete an image record by id.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    /**
     * Delete all images for a product.
     */
    public function deleteByProduct(int $productId): bool
    {
        return $this->delete('`product_id` = :pid', [':pid' => $productId]);
    }
}
