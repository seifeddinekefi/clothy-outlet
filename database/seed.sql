-- ============================================================
-- database/seed.sql
-- Clothy Outlet — Sample Seed Data
--
-- Run AFTER clothy_outlet.sql has been executed.
-- Import order respects FK constraints.
--
-- Default admin credentials:
--   Email    : admin@clothyoutlet.com
--   Password : Admin@1234
--   (Hash generated with password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost'=>12]))
-- ============================================================

USE `clothy_outlet`;

SET FOREIGN_KEY_CHECKS = 0;

-- ── Delete in reverse FK order, then reset AUTO_INCREMENT ────
-- Using DELETE instead of TRUNCATE: TRUNCATE can fail in some
-- MySQL/MariaDB builds when FK checks are toggled per-statement
-- (e.g. phpMyAdmin running statements in isolated sessions).
DELETE FROM `order_items`;
DELETE FROM `orders`;
DELETE FROM `wishlists`;
DELETE FROM `password_resets`;
DELETE FROM `customers`;
DELETE FROM `coupons`;
DELETE FROM `product_sizes`;
DELETE FROM `product_images`;
DELETE FROM `products`;
DELETE FROM `categories`;
DELETE FROM `admins`;
DELETE FROM `roles`;
DELETE FROM `settings`;

-- ── Reset AUTO_INCREMENT counters ────────────────────────────
ALTER TABLE `order_items`    AUTO_INCREMENT = 1;
ALTER TABLE `orders`         AUTO_INCREMENT = 1;
ALTER TABLE `wishlists`      AUTO_INCREMENT = 1;
ALTER TABLE `password_resets` AUTO_INCREMENT = 1;
ALTER TABLE `customers`      AUTO_INCREMENT = 1;
ALTER TABLE `coupons`        AUTO_INCREMENT = 1;
ALTER TABLE `product_sizes`  AUTO_INCREMENT = 1;
ALTER TABLE `product_images` AUTO_INCREMENT = 1;
ALTER TABLE `products`       AUTO_INCREMENT = 1;
ALTER TABLE `categories`     AUTO_INCREMENT = 1;
ALTER TABLE `admins`         AUTO_INCREMENT = 1;
ALTER TABLE `roles`          AUTO_INCREMENT = 1;
ALTER TABLE `settings`       AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;


-- ==============================================================
-- roles
-- ==============================================================
INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'super_admin', '{"dashboard":true,"products":true,"categories":true,"orders":true,"customers":true,"admins":true,"settings":true}'),
(2, 'manager',     '{"dashboard":true,"products":true,"categories":true,"orders":true,"customers":true,"admins":false,"settings":false}'),
(3, 'staff',       '{"dashboard":true,"products":false,"categories":false,"orders":true,"customers":true,"admins":false,"settings":false}');


-- ==============================================================
-- admins
-- password: Admin@1234
-- ==============================================================
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role_id`, `is_active`) VALUES
(1, 'Super Admin',   'admin@clothyoutlet.com',   '$2y$12$FD9WkYWFevXSZ5TVRgre5uRBUpiXfXp3FN6iIsa8TkT1WrQYIFCai', 1, 1),
(2, 'Store Manager', 'manager@clothyoutlet.com', '$2y$12$FD9WkYWFevXSZ5TVRgre5uRBUpiXfXp3FN6iIsa8TkT1WrQYIFCai', 2, 1);


-- ==============================================================
-- categories
-- ==============================================================
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
(1, 'T-Shirts',   't-shirts',   'Casual and graphic tees for every style.',      1, 1),
(2, 'Shirts',     'shirts',     'Formal and semi-formal shirts.',                 2, 1),
(3, 'Jeans',      'jeans',      'Denim jeans in all fits – slim, regular, wide.', 3, 1),
(4, 'Dresses',    'dresses',    'Day dresses, maxi dresses and evening wear.',    4, 1),
(5, 'Outerwear',  'outerwear',  'Jackets, coats and hoodies.',                    5, 1),
(6, 'Activewear', 'activewear', 'Gym wear and outdoor performance clothing.',     6, 1);


-- ==============================================================
-- products
-- ==============================================================
INSERT INTO `products`
    (`id`, `name`, `slug`, `description`, `price`, `compare_price`, `stock`, `sku`, `category_id`, `is_featured`, `is_active`)
VALUES
-- T-Shirts
(1,  'Essential White Tee',
     'essential-white-tee',
     'A wardrobe staple — 100% organic cotton, relaxed unisex fit.',
     19.99, 29.99, 150, 'TSH-001-WHT', 1, 1, 1),

(2,  'Graphic Black Tee',
     'graphic-black-tee',
     'Bold print on heavyweight 200 gsm cotton.',
     24.99, NULL,  80,  'TSH-002-BLK', 1, 1, 1),

(3,  'Striped Nautical Tee',
     'striped-nautical-tee',
     'Classic French sailor stripe, navy and white.',
     22.99, 27.99, 60,  'TSH-003-NVY', 1, 0, 1),

-- Shirts
(4,  'Oxford Button-Down Shirt',
     'oxford-button-down-shirt',
     'Crisp Oxford cloth, slim fit, button-down collar.',
     49.99, 65.00, 45,  'SHT-001-BLU', 2, 1, 1),

(5,  'Linen Summer Shirt',
     'linen-summer-shirt',
     'Breathable pure linen, relaxed fit, camp collar.',
     44.99, NULL,  55,  'SHT-002-WHT', 2, 0, 1),

-- Jeans
(6,  'Slim Fit Dark Wash Jeans',
     'slim-fit-dark-wash-jeans',
     '98% cotton 2% elastane, dark indigo wash, slim through the leg.',
     69.99, 89.99, 70,  'JNS-001-DRK', 3, 1, 1),

(7,  'Straight Leg Raw Denim',
     'straight-leg-raw-denim',
     'Selvedge raw denim, straight fit, fades beautifully.',
     89.99, NULL,  35,  'JNS-002-RAW', 3, 0, 1),

-- Dresses
(8,  'Floral Wrap Dress',
     'floral-wrap-dress',
     'Lightweight viscose, adjustable wrap silhouette, midi length.',
     54.99, 69.99, 40,  'DRS-001-FLO', 4, 1, 1),

(9,  'Black Mini Dress',
     'black-mini-dress',
     'Structured bodycon mini in scuba fabric — the perfect LBD.',
     59.99, NULL,  30,  'DRS-002-BLK', 4, 1, 1),

-- Outerwear
(10, 'Classic Trench Coat',
     'classic-trench-coat',
     'Water-resistant cotton-poly blend, double-breasted, belt tie.',
     129.99, 159.99, 25, 'OTW-001-BGE', 5, 1, 1),

-- Activewear
(11, 'Performance Running Tee',
     'performance-running-tee',
     'Moisture-wicking recycled polyester, 4-way stretch.',
     34.99, NULL,  90,  'ACT-001-BLK', 6, 0, 1),

(12, 'Compression Leggings',
     'compression-leggings',
     '78% nylon 22% spandex, high-waist, squat-proof.',
     54.99, 64.99, 65,  'ACT-002-NVY', 6, 1, 1);


-- ==============================================================
-- coupons
-- ==============================================================
INSERT INTO `coupons`
    (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `starts_at`, `expires_at`, `is_active`)
VALUES
(1, 'WELCOME10', 'percent', 10.00, 50.00, 30.00, NULL, NULL, 1),
(2, 'SAVE15',    'fixed',   15.00, 100.00, NULL,  NULL, NULL, 1);


-- ==============================================================
-- product_images
-- ==============================================================
INSERT INTO `product_images` (`product_id`, `image_path`, `alt_text`, `is_primary`, `sort_order`) VALUES
(1,  'products/tsh-001-wht-1.jpg', 'Essential White Tee front',     1, 1),
(1,  'products/tsh-001-wht-2.jpg', 'Essential White Tee back',      0, 2),
(2,  'products/tsh-002-blk-1.jpg', 'Graphic Black Tee front',       1, 1),
(3,  'products/tsh-003-nvy-1.jpg', 'Striped Nautical Tee',          1, 1),
(4,  'products/sht-001-blu-1.jpg', 'Oxford Shirt front',            1, 1),
(4,  'products/sht-001-blu-2.jpg', 'Oxford Shirt detail',           0, 2),
(5,  'products/sht-002-wht-1.jpg', 'Linen Summer Shirt',            1, 1),
(6,  'products/jns-001-drk-1.jpg', 'Slim Fit Jeans front',          1, 1),
(6,  'products/jns-001-drk-2.jpg', 'Slim Fit Jeans back',           0, 2),
(7,  'products/jns-002-raw-1.jpg', 'Raw Denim Jeans',               1, 1),
(8,  'products/drs-001-flo-1.jpg', 'Floral Wrap Dress',             1, 1),
(9,  'products/drs-002-blk-1.jpg', 'Black Mini Dress',              1, 1),
(10, 'products/otw-001-bge-1.jpg', 'Classic Trench Coat front',     1, 1),
(10, 'products/otw-001-bge-2.jpg', 'Classic Trench Coat side',      0, 2),
(11, 'products/act-001-blk-1.jpg', 'Performance Running Tee',       1, 1),
(12, 'products/act-002-nvy-1.jpg', 'Compression Leggings',          1, 1);


-- ==============================================================
-- product_sizes
-- ==============================================================
INSERT INTO `product_sizes` (`product_id`, `size`, `stock`) VALUES
-- T-Shirts (product 1)
(1, 'XS', 15), (1, 'S', 30), (1, 'M', 40), (1, 'L', 35), (1, 'XL', 20), (1, 'XXL', 10),
-- T-Shirts (product 2)
(2, 'S', 15), (2, 'M', 25), (2, 'L', 25), (2, 'XL', 15),
-- Striped Tee (product 3)
(3, 'XS', 8), (3, 'S', 12), (3, 'M', 18), (3, 'L', 14), (3, 'XL', 8),
-- Shirts (product 4)
(4, 'S', 8), (4, 'M', 15), (4, 'L', 12), (4, 'XL', 10),
-- Linen Shirt (product 5)
(5, 'S', 10), (5, 'M', 18), (5, 'L', 15), (5, 'XL', 12),
-- Jeans (product 6) — numeric sizes
(6, '28', 8), (6, '30', 15), (6, '32', 20), (6, '34', 15), (6, '36', 12),
-- Raw Denim (product 7)
(7, '28', 5), (7, '30', 10), (7, '32', 12), (7, '34', 8),
-- Dresses (product 8)
(8, 'XS', 5), (8, 'S', 10), (8, 'M', 12), (8, 'L', 8), (8, 'XL', 5),
-- Mini Dress (product 9)
(9, 'XS', 4), (9, 'S', 8), (9, 'M', 10), (9, 'L', 8),
-- Trench (product 10)
(10, 'XS', 3), (10, 'S', 5), (10, 'M', 8), (10, 'L', 6), (10, 'XL', 3),
-- Activewear (products 11-12)
(11, 'XS', 10), (11, 'S', 20), (11, 'M', 30), (11, 'L', 20), (11, 'XL', 10),
(12, 'XS', 8), (12, 'S', 15), (12, 'M', 20), (12, 'L', 15), (12, 'XL', 7);


-- ==============================================================
-- customers
-- ==============================================================
INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `city`, `notes`) VALUES
(1, 'Alice Johnson',   'alice@example.com', '+1 555-0101', '12 Maple Street',  'New York',  NULL),
(2, 'Bob Smith',       'bob@example.com',   '+1 555-0102', '40 Oak Avenue',    'Los Angeles', NULL),
(3, 'Clara Williams',  'clara@example.com', '+1 555-0103', '7 Pine Lane',      'Chicago',   'Leave at front door'),
(4, 'Guest Customer',  NULL,                '+1 555-0199', '88 Cedar Road',    'Houston',   NULL);


-- ==============================================================
-- wishlists
-- ==============================================================
INSERT INTO `wishlists` (`customer_id`, `product_id`) VALUES
(1, 10),
(1, 8),
(2, 6);


-- ==============================================================
-- orders
-- ==============================================================
INSERT INTO `orders`
    (`id`, `customer_id`, `total_price`, `subtotal`, `discount`, `shipping_fee`, `status`, `payment_method`, `payment_status`)
VALUES
(1, 1, 109.97, 104.97, 0.00,  5.00, 'delivered', 'card',            'paid'),
(2, 2, 69.99,  64.99,  0.00,  5.00, 'shipped',   'cash_on_delivery','unpaid'),
(3, 3, 134.98, 129.98, 10.00, 15.00,'confirmed', 'paypal',          'paid'),
(4, 4, 44.99,  44.99,  0.00,  0.00, 'pending',   'cash_on_delivery','unpaid');


-- ==============================================================
-- order_items
-- ==============================================================
INSERT INTO `order_items` (`order_id`, `product_id`, `size`, `quantity`, `price`) VALUES
-- Order 1: Alice bought White Tee + Oxford Shirt
(1, 1, 'M',  2, 19.99),
(1, 4, 'L',  1, 49.99),
-- Order 2: Bob bought Slim Fit Jeans
(2, 6, '32', 1, 69.99),
-- Order 3: Clara bought Floral Wrap Dress + Compression Leggings
(3, 8, 'M',  1, 54.99),
(3, 12,'M',  1, 54.99),
-- Order 4: Guest bought Linen Shirt
(4, 5, 'L',  1, 44.99);


-- ==============================================================
-- settings
-- ==============================================================
INSERT INTO `settings` (`key`, `value`) VALUES
('store_name',          'Clothy Outlet'),
('store_tagline',       'Fashion for Everyone'),
('store_email',         'contact@clothyoutlet.com'),
('store_phone',         ''),
('store_address',       ''),
('currency_symbol',     '$'),
('products_per_page',   '12'),
('low_stock_threshold', '10');
