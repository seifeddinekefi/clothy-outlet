<?php

/**
 * ============================================================
 * app/models/OrderItem.php
 * ============================================================
 * Represents the `order_items` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for order line items
 *  - Retrieve items with product snapshot data
 *  - Bulk insert (multi-item checkout)
 *  - Best-seller analytics
 * ============================================================
 */

class OrderItem extends Model
{
    protected string $table      = 'order_items';
    protected string $primaryKey = 'id';

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find an order item by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all order items (admin — rarely needed directly).
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT oi.*, p.name AS product_name, p.slug AS product_slug
               FROM `order_items` oi
               JOIN `products`    p  ON p.id = oi.product_id
              ORDER BY oi.order_id ASC, oi.id ASC"
        );
    }

    /**
     * Return all items for a given order with product details joined.
     *
     * @return array<int, object>
     */
    public function findByOrder(int $orderId): array
    {
        return $this->db->select(
            "SELECT oi.*,
                    p.name        AS product_name,
                    p.slug        AS product_slug,
                    pi.image_path AS product_image
               FROM `order_items` oi
               JOIN `products`    p  ON p.id         = oi.product_id
               LEFT JOIN `product_images` pi
                      ON pi.product_id = oi.product_id AND pi.is_primary = 1
              WHERE oi.order_id = :oid
              ORDER BY oi.id ASC",
            [':oid' => $orderId]
        );
    }

    /**
     * Return all order items for a specific product (purchase history).
     *
     * @return array<int, object>
     */
    public function findByProduct(int $productId): array
    {
        return $this->db->select(
            "SELECT oi.*, o.created_at AS order_date, o.status AS order_status
               FROM `order_items` oi
               JOIN `orders`      o  ON o.id = oi.order_id
              WHERE oi.product_id = :pid
              ORDER BY o.created_at DESC",
            [':pid' => $productId]
        );
    }

    // ── Analytics ────────────────────────────────────────────

    /**
     * Return the top N best-selling products by quantity sold.
     *
     * @param  int $limit
     * @return array<int, object>  Each: {product_id, product_name, total_qty, total_revenue}
     */
    public function bestSellers(int $limit = 10): array
    {
        return $this->db->select(
            "SELECT oi.product_id,
                    p.name                  AS product_name,
                    p.slug                  AS product_slug,
                    pi.image_path           AS product_image,
                    SUM(oi.quantity)        AS total_qty,
                    SUM(oi.subtotal)        AS total_revenue
               FROM `order_items` oi
               JOIN `products`    p   ON p.id         = oi.product_id
               LEFT JOIN `product_images` pi
                      ON pi.product_id = oi.product_id AND pi.is_primary = 1
               JOIN `orders`      o   ON o.id         = oi.order_id
              WHERE o.payment_status = 'paid'
              GROUP BY oi.product_id, p.name, p.slug, pi.image_path
              ORDER BY total_qty DESC
              LIMIT :lim",
            [':lim' => $limit]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a single order item.
     *
     * @param  int         $orderId
     * @param  int         $productId
     * @param  int         $quantity
     * @param  float       $price      Unit price at time of order
     * @param  string|null $size
     * @param  string|null $color      Color chosen at purchase
     * @param  string|null $quality    Quality tier chosen at purchase
     * @return string|false  New item id
     */
    public function create(
        int $orderId,
        int $productId,
        int $quantity,
        float $price,
        ?string $size = null,
        ?string $color = null,
        ?string $quality = null
    ): string|false {
        return $this->insert([
            'order_id'        => $orderId,
            'product_id'      => $productId,
            'size'            => $size,
            'selected_color'  => $color,
            'selected_quality' => $quality,
            'quantity'        => max(1, $quantity),
            'price'           => $price,
        ]);
    }

    /**
     * Bulk-insert all items for a new order within a single transaction.
     *
     * @param  int   $orderId
     * @param  array<int, array{product_id:int, quantity:int, price:float, size?:string|null, color?:string|null, quality?:string|null}> $items
     * @return bool
     */
    public function bulkCreate(int $orderId, array $items): bool
    {
        if (empty($items)) {
            return false;
        }

        return (bool) $this->db->transaction(function () use ($orderId, $items): void {
            foreach ($items as $item) {
                $this->create(
                    $orderId,
                    (int)   $item['product_id'],
                    (int)   $item['quantity'],
                    (float) $item['price'],
                    $item['size']    ?? null,
                    $item['color']   ?? null,
                    $item['quality'] ?? null
                );
            }
        });
    }

    /**
     * Update an order item (quantity / price correction).
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateItem(int $id, array $data): bool
    {
        unset($data['id'], $data['order_id'], $data['product_id'], $data['subtotal']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Delete a single order item.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    /**
     * Delete all items for a given order.
     */
    public function deleteByOrder(int $orderId): bool
    {
        return $this->delete('`order_id` = :oid', [':oid' => $orderId]);
    }
}
