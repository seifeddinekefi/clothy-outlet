-- ============================================================
-- database/clothy_outlet.sql
-- Clothy Outlet — Full Relational Database Schema
-- Engine  : MySQL 8.x / MariaDB 10.5+  (InnoDB)
-- Charset : utf8mb4 / utf8mb4_unicode_ci
--
-- ERD Relationships
-- ─────────────────
-- roles ──────────────── admins        (1 : N)
-- categories ─────────── products      (1 : N)
-- products ───────────── product_images (1 : N)
-- products ───────────── product_sizes  (1 : N)
-- customers ──────────── orders         (1 : N)
-- orders ─────────────── order_items    (1 : N)
-- products ───────────── order_items    (1 : N)
--
-- Creation order respects FK dependencies.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ── Database ──────────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS `clothy_outlet`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `clothy_outlet`;


-- ==============================================================
-- TABLE: roles
-- Stores admin role definitions with a JSON permissions matrix.
-- Supports future multi-role, fine-grained access control.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(50)         NOT NULL,
    `permissions` JSON                NOT NULL COMMENT 'e.g. {"orders":true,"products":true}',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin role definitions with permission sets';


-- ==============================================================
-- TABLE: admins
-- Staff / admin user accounts.
-- FK: role_id → roles.id  (RESTRICT — cannot delete a role in use)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED         NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)         NOT NULL,
    `email`      VARCHAR(180)         NOT NULL,
    `password`   VARCHAR(255)         NOT NULL COMMENT 'bcrypt / argon2id hash',
    `role_id`    TINYINT UNSIGNED     NOT NULL DEFAULT 1,
    `is_active`  TINYINT(1)           NOT NULL DEFAULT 1,
    `last_login` TIMESTAMP            NULL     DEFAULT NULL,
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY  `uq_admins_email`   (`email`),
    KEY         `idx_admins_role`   (`role_id`),

    CONSTRAINT `fk_admins_role`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin panel user accounts';


-- ==============================================================
-- TABLE: categories
-- Product categories (flat list — expandable to tree via
-- parent_id in a future step).
-- ==============================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)        NOT NULL,
    `slug`        VARCHAR(110)        NOT NULL,
    `description` TEXT                NULL,
    `image_path`  VARCHAR(300)        NULL,
    `sort_order`  SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)          NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`),
    KEY        `idx_categories_active` (`is_active`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Top-level product categories';


-- ==============================================================
-- TABLE: products
-- Core product catalogue.
-- FK: category_id → categories.id (RESTRICT)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `products` (
    `id`          INT UNSIGNED         NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(200)         NOT NULL,
    `slug`        VARCHAR(220)         NOT NULL,
    `description` TEXT                 NULL,
    `price`       DECIMAL(10, 2)       NOT NULL DEFAULT 0.00,
    `compare_price` DECIMAL(10, 2)     NULL     DEFAULT NULL COMMENT 'Original price before discount',
    `stock`       INT UNSIGNED         NOT NULL DEFAULT 0,
    `sku`         VARCHAR(80)          NULL     UNIQUE COMMENT 'Stock Keeping Unit',
    `category_id` SMALLINT UNSIGNED    NOT NULL,
    `is_featured` TINYINT(1)           NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)           NOT NULL DEFAULT 1,
    `meta_title`  VARCHAR(160)         NULL     COMMENT 'SEO title',
    `meta_desc`   VARCHAR(320)         NULL     COMMENT 'SEO description',
    `created_at`  TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_slug`     (`slug`),
    KEY        `idx_products_category` (`category_id`),
    KEY        `idx_products_featured` (`is_featured`),
    KEY        `idx_products_active`   (`is_active`),
    KEY        `idx_products_price`    (`price`),
    FULLTEXT   KEY `ft_products_search` (`name`, `description`),

    CONSTRAINT `fk_products_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Main product catalogue';


-- ==============================================================
-- TABLE: product_images
-- Multiple images per product; one marked as primary.
-- FK: product_id → products.id (CASCADE — delete product = delete images)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `product_images` (
    `id`         INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED        NOT NULL,
    `image_path` VARCHAR(300)        NOT NULL,
    `alt_text`   VARCHAR(200)        NULL,
    `is_primary` TINYINT(1)          NOT NULL DEFAULT 0,
    `sort_order` SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_product_images_product`  (`product_id`),
    KEY `idx_product_images_primary`  (`is_primary`),

    CONSTRAINT `fk_product_images_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Product image gallery; is_primary marks the thumbnail';


-- ==============================================================
-- TABLE: product_sizes
-- Per-product size/stock breakdown (XS, S, M, L, XL, etc.)
-- FK: product_id → products.id (CASCADE)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `product_sizes` (
    `id`         INT UNSIGNED       NOT NULL AUTO_INCREMENT,
    `product_id` INT UNSIGNED       NOT NULL,
    `size`       VARCHAR(20)        NOT NULL COMMENT 'XS | S | M | L | XL | XXL | numeric',
    `stock`      INT UNSIGNED       NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_product_size`         (`product_id`, `size`),
    KEY        `idx_product_sizes_product` (`product_id`),

    CONSTRAINT `fk_product_sizes_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Per-size stock levels for each product';


-- ==============================================================
-- TABLE: customers
-- Guest or registered shopper profiles.
-- No user/auth table yet — auth added in later step.
-- email is nullable to support guest checkout.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `customers` (
    `id`          INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150)        NOT NULL,
    `email`       VARCHAR(180)        NULL COMMENT 'Null for guest orders',
    `password`    VARCHAR(255)        NULL COMMENT 'bcrypt hash, NULL for guests',
    `phone`       VARCHAR(30)         NULL,
    `address`     VARCHAR(300)        NULL,
    `city`        VARCHAR(100)        NULL,
    `notes`       TEXT                NULL COMMENT 'Delivery notes / instructions',
    `is_guest`    TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1 = guest checkout, 0 = registered',
    `guest_token` VARCHAR(64)         NULL COMMENT 'Token for guest session tracking',
    `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_customers_email`       (`email`),
    KEY `idx_customers_phone`       (`phone`),
    KEY `idx_customers_is_guest`    (`is_guest`),
    KEY `idx_customers_guest_token` (`guest_token`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Shopper profiles (registered + guest)';


-- ==============================================================
-- TABLE: orders
-- A single customer order (checkout session result).
-- FK: customer_id → customers.id (RESTRICT)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `orders` (
    `id`             INT UNSIGNED         NOT NULL AUTO_INCREMENT,
    `customer_id`    INT UNSIGNED         NOT NULL,
    `total_price`    DECIMAL(10, 2)       NOT NULL DEFAULT 0.00,
    `subtotal`       DECIMAL(10, 2)       NOT NULL DEFAULT 0.00,
    `discount`       DECIMAL(10, 2)       NOT NULL DEFAULT 0.00 COMMENT 'Coupon / promo reduction',
    `shipping_fee`   DECIMAL(10, 2)       NOT NULL DEFAULT 0.00,
    `status`         ENUM(
                         'pending',
                         'confirmed',
                         'shipped',
                         'delivered',
                         'cancelled'
                     )                    NOT NULL DEFAULT 'pending',
    `payment_method` VARCHAR(50)          NOT NULL DEFAULT 'cash_on_delivery'
                     COMMENT 'cash_on_delivery | card | paypal | stripe | …',
    `payment_status` ENUM(
                         'unpaid',
                         'paid',
                         'refunded'
                     )                    NOT NULL DEFAULT 'unpaid',
    `notes`          TEXT                 NULL     COMMENT 'Admin / customer order notes',
    `tracking_token` VARCHAR(64)          NULL     COMMENT 'Token for guest order tracking',
    `shipped_at`     TIMESTAMP            NULL     DEFAULT NULL,
    `delivered_at`   TIMESTAMP            NULL     DEFAULT NULL,
    `created_at`     TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_orders_customer`        (`customer_id`),
    KEY `idx_orders_status`          (`status`),
    KEY `idx_orders_payment_status`  (`payment_status`),
    KEY `idx_orders_created`         (`created_at`),
    KEY `idx_orders_tracking_token`  (`tracking_token`),

    CONSTRAINT `fk_orders_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Customer order headers';


-- ==============================================================
-- TABLE: order_items
-- Individual line items inside an order (snapshot of product at
-- time of purchase — price is stored, not referenced live).
-- FK: order_id   → orders.id   (CASCADE)
-- FK: product_id → products.id (RESTRICT — keep history even if
--     product is deactivated)
-- ==============================================================
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`         INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `order_id`   INT UNSIGNED        NOT NULL,
    `product_id` INT UNSIGNED        NOT NULL,
    `size`       VARCHAR(20)         NULL     COMMENT 'Snapshot of ordered size',
    `quantity`   SMALLINT UNSIGNED   NOT NULL DEFAULT 1,
    `price`      DECIMAL(10, 2)      NOT NULL COMMENT 'Unit price at time of order',
    `subtotal`   DECIMAL(10, 2)      GENERATED ALWAYS AS (`quantity` * `price`) STORED
                 COMMENT 'Computed: quantity × price',

    PRIMARY KEY (`id`),
    KEY `idx_order_items_order`   (`order_id`),
    KEY `idx_order_items_product` (`product_id`),

    CONSTRAINT `fk_order_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_order_items_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Per-line-item detail for each order';


-- ==============================================================
-- TABLE: coupons
-- Promo codes applied at checkout.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `coupons` (
    `id`                  INT UNSIGNED         NOT NULL AUTO_INCREMENT,
    `code`                VARCHAR(50)          NOT NULL,
    `discount_type`       ENUM('fixed','percent') NOT NULL DEFAULT 'fixed',
    `discount_value`      DECIMAL(10, 2)       NOT NULL,
    `min_order_amount`    DECIMAL(10, 2)       NULL,
    `max_discount_amount` DECIMAL(10, 2)       NULL,
    `starts_at`           DATETIME             NULL,
    `expires_at`          DATETIME             NULL,
    `is_active`           TINYINT(1)           NOT NULL DEFAULT 1,
    `created_at`          TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_coupons_code` (`code`),
    KEY `idx_coupons_active` (`is_active`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Checkout coupons';


-- ==============================================================
-- TABLE: wishlists
-- Customer saved products.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `wishlists` (
    `id`          INT UNSIGNED       NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED       NOT NULL,
    `product_id`  INT UNSIGNED       NOT NULL,
    `created_at`  TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_wishlist_customer_product` (`customer_id`, `product_id`),
    KEY `idx_wishlist_customer` (`customer_id`),
    KEY `idx_wishlist_product` (`product_id`),

    CONSTRAINT `fk_wishlist_customer`
        FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT `fk_wishlist_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Customer wishlist items';


-- ==============================================================
-- TABLE: password_resets
-- One-time password reset tokens.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(180)      NOT NULL,
    `token_hash` CHAR(64)          NOT NULL,
    `expires_at` DATETIME          NOT NULL,
    `created_at` DATETIME          NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_password_resets_token_hash` (`token_hash`),
    KEY `idx_password_resets_email` (`email`),
    KEY `idx_password_resets_expires` (`expires_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Password reset tokens';


-- ==============================================================
-- TABLE: settings
-- Application-wide key-value configuration store.
-- ==============================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`        VARCHAR(100) NOT NULL,
    `value`      TEXT         NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`key`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Application key-value settings store';


-- ── Re-enable FK checks ───────────────────────────────────────
SET FOREIGN_KEY_CHECKS = 1;
