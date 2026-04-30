<?php

/**
 * app/models/ProductColor.php
 * Admin-defined color swatches for a product.
 * Colors apply to Standard and 180g quality tiers.
 * For 220g / 250g the only valid colors are White and Black (enforced elsewhere).
 */

class ProductColor extends Model
{
    protected string $table      = 'product_colors';
    protected string $primaryKey = 'id';

    /**
     * Return all colors for a product ordered by sort_order.
     *
     * @return array<int, object>
     */
    public function findByProduct(int $productId): array
    {
        return $this->db->select(
            "SELECT * FROM `product_colors`
              WHERE `product_id` = :pid
              ORDER BY `sort_order` ASC, `id` ASC",
            [':pid' => $productId]
        );
    }

    /**
     * Delete all colors for a product (used before re-saving).
     */
    public function deleteByProduct(int $productId): bool
    {
        return $this->delete('`product_id` = :pid', [':pid' => $productId]);
    }

    /**
     * Create a color entry.
     */
    public function create(int $productId, string $colorName, string $colorHex, int $sortOrder = 0): string|false
    {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colorHex)) {
            $colorHex = '#000000';
        }
        return $this->insert([
            'product_id' => $productId,
            'color_name' => mb_substr($colorName, 0, 50),
            'color_hex'  => strtolower($colorHex),
            'sort_order' => $sortOrder,
        ]);
    }
}
