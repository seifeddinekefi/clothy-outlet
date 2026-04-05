<?php

/**
 * ============================================================
 * app/models/Order.php
 * ============================================================
 * Represents the `orders` table.
 *
 * Responsibilities (DB layer only):
 *  - CRUD for order headers
 *  - Status transitions (pending → confirmed → shipped …)
 *  - Revenue / analytics queries
 *  - Customer order history
 *  - Paginated admin order listing with JOIN data
 * ============================================================
 */

class Order extends Model
{
    protected string $table      = 'orders';
    protected string $primaryKey = 'id';

    // ── Valid Status / Payment Values ─────────────────────────

    public const STATUSES = [
        'pending',
        'confirmed',
        'shipped',
        'delivered',
        'cancelled',
    ];

    public const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];

    public const PAYMENT_METHODS = [
        'cash_on_delivery',
        'card',
        'paypal',
        'stripe',
        'bank_transfer',
    ];

    // ── Finders ──────────────────────────────────────────────

    /**
     * Find an order by primary key.
     */
    public function findById(int $id): mixed
    {
        return $this->find($id);
    }

    /**
     * Return all orders with customer name joined.
     *
     * @return array<int, object>
     */
    public function findAll(): array
    {
        return $this->db->select(
            "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone
               FROM `orders`    o
               JOIN `customers` c ON c.id = o.customer_id
              ORDER BY o.created_at DESC"
        );
    }

    /**
     * Return a single order with customer details joined.
     */
    public function findWithCustomer(int $orderId): mixed
    {
        return $this->db->selectOne(
            "SELECT o.*,
                    c.name    AS customer_name,
                    c.email   AS customer_email,
                    c.phone   AS customer_phone,
                    c.address AS customer_address,
                    c.city    AS customer_city,
                    c.notes   AS customer_notes
               FROM `orders`    o
               JOIN `customers` c ON c.id = o.customer_id
              WHERE o.id = :id
              LIMIT 1",
            [':id' => $orderId]
        );
    }

    /**
     * Return all orders placed by a specific customer.
     *
     * @return array<int, object>
     */
    public function findByCustomer(int $customerId): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}`
              WHERE `customer_id` = :cid
              ORDER BY `created_at` DESC",
            [':cid' => $customerId]
        );
    }

    /**
     * Find one order by customer and order id.
     */
    public function findByCustomerAndId(int $customerId, int $orderId): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}`
              WHERE `customer_id` = :cid AND `id` = :oid
              LIMIT 1",
            [':cid' => $customerId, ':oid' => $orderId]
        );
    }

    /**
     * Return orders filtered by status.
     *
     * @return array<int, object>
     */
    public function findByStatus(string $status): array
    {
        return $this->db->select(
            "SELECT o.*, c.name AS customer_name
               FROM `orders`    o
               JOIN `customers` c ON c.id = o.customer_id
              WHERE o.status = :status
              ORDER BY o.created_at DESC",
            [':status' => $status]
        );
    }

    /**
     * Paginated order list with optional status filter.
     *
     * @param  int         $page
     * @param  int         $perPage
     * @param  string|null $status
     * @return array{data: array<int,object>, total: int, page: int, perPage: int, pages: int}
     */
    public function paginateOrders(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where  = '1=1';
        $params = [];

        if ($status !== null) {
            $where          .= ' AND o.status = :status';
            $params[':status'] = $status;
        }

        $total = (int) ($this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `orders` o WHERE {$where}",
            $params
        )->cnt ?? 0);

        $offset = ($page - 1) * $perPage;

        $data = $this->db->select(
            "SELECT o.*, c.name AS customer_name, c.phone AS customer_phone
               FROM `orders`    o
               JOIN `customers` c ON c.id = o.customer_id
              WHERE {$where}
              ORDER BY o.created_at DESC
              LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int) ceil($total / $perPage),
        ];
    }

    // ── Analytics / Dashboard ─────────────────────────────────

    /**
     * Count orders that need attention:
     * - status is 'pending' or 'confirmed', OR
     * - payment is 'unpaid' on a non-cancelled order.
     * Badge disappears automatically when there is nothing to act on.
     */
    public function pendingCount(): int
    {
        $row = $this->db->selectOne(
            "SELECT COUNT(*) AS cnt FROM `{$this->table}`
              WHERE (`status` IN ('pending', 'confirmed'))
                 OR (`payment_status` = 'unpaid' AND `status` != 'cancelled')"
        );
        return (int) ($row->cnt ?? 0);
    }

    /**
     * Revenue from paid orders placed in the current calendar month.
     */
    public function revenueThisMonth(): float
    {
        $row = $this->db->selectOne(
            "SELECT COALESCE(SUM(`total_price`), 0) AS revenue
               FROM `{$this->table}`
              WHERE `payment_status` = 'paid'
                AND MONTH(`created_at`) = MONTH(NOW())
                AND YEAR(`created_at`)  = YEAR(NOW())"
        );
        return (float) ($row->revenue ?? 0);
    }

    /**
     * Return total revenue from paid orders.
     */
    public function totalRevenue(): float
    {
        $row = $this->db->selectOne(
            "SELECT COALESCE(SUM(`total_price`), 0) AS revenue
               FROM `{$this->table}`
              WHERE `payment_status` = 'paid'"
        );
        return (float) ($row->revenue ?? 0);
    }

    /**
     * Count orders grouped by status.
     *
     * @return array<int, object>  Each object: {status, count}
     */
    public function countByStatus(): array
    {
        return $this->db->select(
            "SELECT `status`, COUNT(*) AS `count`
               FROM `{$this->table}`
              GROUP BY `status`"
        );
    }

    /**
     * Return daily order revenue for the last N days.
     *
     * @param  int $days
     * @return array<int, object>  Each object: {date, order_count, revenue}
     */
    public function revenueByDay(int $days = 30): array
    {
        return $this->db->select(
            "SELECT DATE(`created_at`)           AS `date`,
                    COUNT(*)                     AS order_count,
                    COALESCE(SUM(`total_price`), 0) AS revenue
               FROM `{$this->table}`
              WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL :days DAY)
                AND `payment_status` = 'paid'
              GROUP BY DATE(`created_at`)
              ORDER BY `date` ASC",
            [':days' => $days]
        );
    }

    // ── Status Transitions ────────────────────────────────────

    /**
     * Update the status of an order.
     * Sets shipped_at / delivered_at timestamps automatically.
     *
     * @throws \InvalidArgumentException
     */
    public function updateStatus(int $orderId, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException("Invalid order status: [{$status}]");
        }

        $data = ['status' => $status];

        if ($status === 'shipped') {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'delivered') {
            $data['delivered_at']   = date('Y-m-d H:i:s');
            $data['payment_status'] = 'paid';  // auto-mark COD as paid on delivery
        }

        return $this->update($data, '`id` = :id', [':id' => $orderId]);
    }

    /**
     * Update payment status.
     *
     * @throws \InvalidArgumentException
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        if (!in_array($paymentStatus, self::PAYMENT_STATUSES, true)) {
            throw new \InvalidArgumentException("Invalid payment status: [{$paymentStatus}]");
        }
        return $this->update(
            ['payment_status' => $paymentStatus],
            '`id` = :id',
            [':id' => $orderId]
        );
    }

    // ── Write Operations ──────────────────────────────────────

    /**
     * Create a new order header.
     *
     * @param  array{
     *     customer_id:     int,
     *     subtotal:        float,
     *     discount?:       float,
     *     shipping_fee?:   float,
     *     total_price:     float,
     *     payment_method?: string,
     *     notes?:          string|null,
     *     tracking_token?: string|null
     * } $data
     * @return string|false  New order id
     */
    public function create(array $data): string|false
    {
        return $this->insert([
            'customer_id'    => $data['customer_id'],
            'subtotal'       => $data['subtotal'],
            'discount'       => $data['discount']       ?? 0.00,
            'shipping_fee'   => $data['shipping_fee']   ?? 0.00,
            'total_price'    => $data['total_price'],
            'status'         => 'pending',
            'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
            'payment_status' => 'unpaid',
            'notes'          => $data['notes']          ?? null,
            'tracking_token' => $data['tracking_token'] ?? null,
        ]);
    }

    /**
     * Find an order by its tracking token (for guest order tracking).
     */
    public function findByTrackingToken(string $token): mixed
    {
        return $this->db->selectOne(
            "SELECT o.*,
                    c.name    AS customer_name,
                    c.email   AS customer_email,
                    c.phone   AS customer_phone,
                    c.address AS customer_address,
                    c.city    AS customer_city,
                    c.notes   AS customer_notes,
                    c.is_guest AS customer_is_guest
               FROM `orders`    o
               JOIN `customers` c ON c.id = o.customer_id
              WHERE o.tracking_token = :token
              LIMIT 1",
            [':token' => $token]
        );
    }

    /**
     * Update order fields.
     *
     * @param  int                  $id
     * @param  array<string, mixed> $data
     * @return bool
     */
    public function updateOrder(int $id, array $data): bool
    {
        unset($data['id'], $data['created_at'], $data['customer_id']);
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    /**
     * Delete an order.
     * Cascades to order_items.
     */
    public function deleteById(int|string $id): bool
    {
        return parent::deleteById($id);
    }

    /**
     * Cancel an order only if it belongs to the customer and is still pending.
     */
    public function cancelByCustomer(int $customerId, int $orderId): bool
    {
        return $this->update(
            ['status' => 'cancelled'],
            '`id` = :oid AND `customer_id` = :cid AND `status` = :status',
            [':oid' => $orderId, ':cid' => $customerId, ':status' => 'pending']
        );
    }
}
