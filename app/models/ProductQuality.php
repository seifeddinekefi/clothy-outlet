<?php

/**
 * app/models/ProductQuality.php
 * Quality tiers enabled per product by the admin.
 * Business rule: for 220g and 250g tiers, only White and Black colors
 * are available (enforced in CartController and the product show view).
 */

class ProductQuality extends Model
{
    protected string $table      = 'product_qualities';
    protected string $primaryKey = 'id';

    const TYPES = ['standard', '180g', '220g', '250g'];

    /** Quality types that restrict colors to White and Black only. */
    const HIGH_QUALITY_TYPES = ['220g', '250g'];

    /**
     * Return all quality tiers for a product, ordered by canonical position.
     *
     * @return array<int, object>
     */
    public function findByProduct(int $productId): array
    {
        return $this->db->select(
            "SELECT * FROM `product_qualities`
              WHERE `product_id` = :pid
              ORDER BY FIELD(`quality_type`, 'standard', '180g', '220g', '250g'), `sort_order` ASC",
            [':pid' => $productId]
        );
    }

    /**
     * Delete all quality tiers for a product (used before re-saving).
     */
    public function deleteByProduct(int $productId): bool
    {
        return $this->delete('`product_id` = :pid', [':pid' => $productId]);
    }

    /**
     * Create a quality tier entry.
     *
     * @return string|false  New row id, or false on failure / unknown type.
     */
    public function create(int $productId, string $qualityType, int $sortOrder = 0): string|false
    {
        if (!in_array($qualityType, self::TYPES, true)) {
            return false;
        }
        return $this->insert([
            'product_id'   => $productId,
            'quality_type' => $qualityType,
            'sort_order'   => $sortOrder,
        ]);
    }

    /**
     * Return true when the given quality restricts colors to White/Black.
     */
    public static function isHighQuality(string $qualityType): bool
    {
        return in_array($qualityType, self::HIGH_QUALITY_TYPES, true);
    }
}
