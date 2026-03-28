<?php

/**
 * ============================================================
 * app/models/ProductSize.php
 * ============================================================
 * Represents the `product_sizes` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for per-size stock breakdown
 *  - Stock adjustment helpers
 *  - Size availability checks
 * ============================================================
 */

class ProductSize extends Model
{
    protected string $table      = 'product_sizes';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find a size record by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all size records (admin use only).
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `product_id` ASC, `id` ASC"
        );
    }

    /**
     * Return all sizes for a product, ordered naturally.
     *
     * @return array<int, object>
     */
    public function findByProduct(int $productId): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}`
              WHERE `product_id` = :pid
              ORDER BY FIELD(`size`, 'XS','S','M','L','XL','XXL'), `size` ASC",
            [':pid' => $productId]
        );
    }

    /**
     * Return only sizes that have stock > 0 for a product.
     *
     * @return array<int, object>
     */
    public function findAvailable(int $productId): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}`
              WHERE `product_id` = :pid AND `stock` > 0
              ORDER BY FIELD(`size`, 'XS','S','M','L','XL','XXL'), `size` ASC",
            [':pid' => $productId]
        );
    }

    /**
     * Find a specific product + size combination.
     */
    public function findByProductAndSize(int $productId, string $size): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}`
              WHERE `product_id` = :pid AND `size` = :size
              LIMIT 1",
            [':pid' => $productId, ':size' => $size]
        );
    }

    // ── Stock Management ──────────────────────────────────────

    /**
     * Check if a requested quantity is available for a size.
     */
    public function isAvailable(int $productId, string $size, int $qty = 1): bool
    {
        $row = $this->findByProductAndSize($productId, $size);
        return $row !== false && (int) $row->stock >= $qty;
    }

    /**
     * Decrement stock for a size.
     * Uses GREATEST(0,…) to prevent negative stock.
     */
    public function decrementStock(int $productId, string $size, int $qty): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}`
                SET `stock` = GREATEST(0, `stock` - :qty)
              WHERE `product_id` = :pid AND `size` = :size",
            [':qty' => $qty, ':pid' => $productId, ':size' => $size]
        );
    }

    /**
     * Increment stock for a size (used on order cancellation / restocking).
     */
    public function incrementStock(int $productId, string $size, int $qty): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}`
                SET `stock` = `stock` + :qty
              WHERE `product_id` = :pid AND `size` = :size",
            [':qty' => $qty, ':pid' => $productId, ':size' => $size]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a size entry for a product.
     *
     * @param  int    $productId
     * @param  string $size
     * @param  int    $stock
     * @return string|false  New id
     */
    public function create(int $productId, string $size, int $stock = 0): string|false
    {
        return $this->insert([
            'product_id' => $productId,
            'size'       => strtoupper(trim($size)),
            'stock'      => max(0, $stock),
        ]);
    }

    /**
     * Upsert (insert or update) a size entry.
     * Useful when syncing size stock from admin form.
     *
     * @return string|false  Inserted id (or false on update path)
     */
    public function upsert(int $productId, string $size, int $stock): string|false
    {
        $existing = $this->findByProductAndSize($productId, $size);

        if ($existing) {
            $this->update(
                ['stock' => max(0, $stock)],
                '`id` = :id',
                [':id' => $existing->id]
            );
            return (string) $existing->id;
        }

        return $this->create($productId, $size, $stock);
    }

    /**
     * Set the stock for a specific size.
     */
    public function setStock(int $productId, string $size, int $stock): bool
    {
        return $this->db->statement(
            "UPDATE `{$this->table}`
                SET `stock` = :stock
              WHERE `product_id` = :pid AND `size` = :size",
            [':stock' => max(0, $stock), ':pid' => $productId, ':size' => $size]
        );
    }

    /**
     * Delete a size record by id.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    /**
     * Delete all sizes for a product.
     */
    public function deleteByProduct(int $productId): bool
    {
        return $this->delete('`product_id` = :pid', [':pid' => $productId]);
    }
}
