-- ============================================================
-- database/migrate_quality_prices.sql
-- Adds per-quality price override to product_qualities.
-- If price IS NULL the product's base price is used instead.
-- ============================================================

ALTER TABLE `product_qualities`
    ADD COLUMN `price` DECIMAL(10, 2) NULL DEFAULT NULL
        COMMENT 'Price for this quality tier; NULL = use base product price'
        AFTER `quality_type`;
