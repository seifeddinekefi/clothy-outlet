-- ============================================================
-- Migration: Guest Checkout Support
-- ============================================================
-- Run this migration on existing databases to add guest checkout support.
-- This adds the required columns to customers and orders tables.
--
-- Execute with: mysql -u root -p clothy_outlet < database/migrate_guest_checkout.sql
-- Or run in phpMyAdmin SQL tab.
-- ============================================================

-- Check if is_guest column exists before adding
SET @dbname = DATABASE();
SET @tablename = 'customers';
SET @columnname = 'is_guest';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `customers` ADD COLUMN `is_guest` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `notes`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add guest_token column if not exists
SET @columnname = 'guest_token';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `customers` ADD COLUMN `guest_token` VARCHAR(64) NULL AFTER `is_guest`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add password column if not exists (for guest to user conversion)
SET @columnname = 'password';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `customers` ADD COLUMN `password` VARCHAR(255) NULL AFTER `email`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add tracking_token to orders table if not exists
SET @tablename = 'orders';
SET @columnname = 'tracking_token';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1',
  'ALTER TABLE `orders` ADD COLUMN `tracking_token` VARCHAR(64) NULL AFTER `notes`'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add indexes (will silently fail if they already exist)
-- For MySQL 8.0+, we can use IF NOT EXISTS. For older versions, errors can be ignored.

-- Create index on is_guest if not exists
CREATE INDEX `idx_customers_is_guest` ON `customers` (`is_guest`);

-- Create index on guest_token if not exists  
CREATE INDEX `idx_customers_guest_token` ON `customers` (`guest_token`);

-- Create index on tracking_token if not exists
CREATE INDEX `idx_orders_tracking_token` ON `orders` (`tracking_token`);

-- Note: If indexes already exist, the above CREATE INDEX statements will show warnings/errors.
-- This is safe to ignore as long as the columns exist.

SELECT 'Migration completed successfully!' AS status;
