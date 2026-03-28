<?php

/**
 * app/models/Wishlist.php
 * Customer wishlist model.
 */
class Wishlist extends Model
{
    protected string $table = 'wishlists';
    protected string $primaryKey = 'id';

    private static bool $tableEnsured = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(): void
    {
        if (self::$tableEnsured) {
            return;
        }

        $this->db->statement(
            "CREATE TABLE IF NOT EXISTS `wishlists` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `customer_id` INT UNSIGNED NOT NULL,
                `product_id` INT UNSIGNED NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_wishlist_customer_product` (`customer_id`, `product_id`),
                KEY `idx_wishlist_customer` (`customer_id`),
                KEY `idx_wishlist_product` (`product_id`),
                CONSTRAINT `fk_wishlist_customer`
                    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk_wishlist_product`
                    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::$tableEnsured = true;
    }

    /**
     * Return wishlist products for a customer.
     *
     * @return array<int, object>
     */
    public function findByCustomer(int $customerId): array
    {
        return $this->db->select(
            "SELECT w.id AS wishlist_id,
                    w.customer_id,
                    w.product_id,
                    w.created_at AS wished_at,
                    p.name,
                    p.slug,
                    p.price,
                    p.compare_price,
                    p.is_featured,
                    pi.image_path AS primary_image
               FROM `wishlists` w
               JOIN `products` p ON p.id = w.product_id
               LEFT JOIN `product_images` pi
                      ON pi.product_id = p.id AND pi.is_primary = 1
              WHERE w.customer_id = :cid
                AND p.is_active = 1
              ORDER BY w.created_at DESC",
            [':cid' => $customerId]
        );
    }

    public function has(int $customerId, int $productId): bool
    {
        return $this->exists('`customer_id` = :cid AND `product_id` = :pid', [
            ':cid' => $customerId,
            ':pid' => $productId,
        ]);
    }

    public function add(int $customerId, int $productId): bool
    {
        if ($this->has($customerId, $productId)) {
            return true;
        }

        return (bool) $this->insert([
            'customer_id' => $customerId,
            'product_id'  => $productId,
        ]);
    }

    public function remove(int $customerId, int $productId): bool
    {
        return $this->delete('`customer_id` = :cid AND `product_id` = :pid', [
            ':cid' => $customerId,
            ':pid' => $productId,
        ]);
    }
}
