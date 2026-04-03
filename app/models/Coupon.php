<?php

/**
 * app/models/Coupon.php
 * Coupon lookup and validation helpers.
 */
class Coupon extends Model
{
    protected string $table = 'coupons';
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
            "CREATE TABLE IF NOT EXISTS `coupons` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(50) NOT NULL,
                `discount_type` ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
                `discount_value` DECIMAL(10,2) NOT NULL,
                `min_order_amount` DECIMAL(10,2) NULL,
                `max_discount_amount` DECIMAL(10,2) NULL,
                `starts_at` DATETIME NULL,
                `expires_at` DATETIME NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_coupons_code` (`code`),
                KEY `idx_coupons_active` (`is_active`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::$tableEnsured = true;
    }

    public function findValidByCode(string $code): mixed
    {
        return $this->db->selectOne(
            "SELECT *
               FROM `coupons`
              WHERE `code` = :code
                AND `is_active` = 1
                AND (`starts_at` IS NULL OR `starts_at` <= NOW())
                AND (`expires_at` IS NULL OR `expires_at` >= NOW())
              LIMIT 1",
            [':code' => strtoupper(trim($code))]
        );
    }

    public function calculateDiscount(object $coupon, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if (!empty($coupon->min_order_amount) && $subtotal < (float) $coupon->min_order_amount) {
            return 0.0;
        }

        $discount = 0.0;
        if (($coupon->discount_type ?? 'fixed') === 'percent') {
            $discount = $subtotal * ((float) $coupon->discount_value / 100);
        } else {
            $discount = (float) $coupon->discount_value;
        }

        if (!empty($coupon->max_discount_amount) && $discount > (float) $coupon->max_discount_amount) {
            $discount = (float) $coupon->max_discount_amount;
        }

        return max(0.0, min($discount, $subtotal));
    }

    // ── CRUD Methods ──────────────────────────────────────────

    public function findById(int $id): mixed
    {
        return $this->db->selectOne(
            "SELECT * FROM `{$this->table}` WHERE `id` = :id LIMIT 1",
            [':id' => $id]
        );
    }

    public function findAll(): array
    {
        return $this->db->select(
            "SELECT * FROM `{$this->table}` ORDER BY `created_at` DESC"
        );
    }

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $code = strtoupper(trim($code));
        $sql = "SELECT COUNT(*) AS cnt FROM `{$this->table}` WHERE `code` = :code";
        $params = [':code' => $code];

        if ($excludeId !== null) {
            $sql .= " AND `id` != :id";
            $params[':id'] = $excludeId;
        }

        $row = $this->db->selectOne($sql, $params);
        return ($row->cnt ?? 0) > 0;
    }

    public function createCoupon(array $data): string|false
    {
        $data['code'] = strtoupper(trim($data['code'] ?? ''));
        return $this->insert($data);
    }

    public function updateCoupon(int $id, array $data): bool
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper(trim($data['code']));
        }
        return $this->update($data, '`id` = :id', [':id' => $id]);
    }

    public function deleteCoupon(int $id): bool
    {
        return $this->delete('`id` = :id', [':id' => $id]);
    }
}
