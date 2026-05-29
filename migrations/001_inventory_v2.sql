-- Migration 001: Inventory v2 — tồn theo cửa hàng, nhà cung cấp, phiếu nhập, công nợ, KH thân thiết
-- Idempotent: chạy nhiều lần không lỗi (dùng CREATE TABLE IF NOT EXISTS + thủ tục ALTER có check)
-- Áp dụng cho mỗi tenant DB.

SET NAMES utf8mb4;

-- ===== 1. Thêm cột vào bảng cũ (idempotent qua INFORMATION_SCHEMA) =====

-- products: min_stock, cost_price
DROP PROCEDURE IF EXISTS _qlbh_add_col;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_col(IN tbl VARCHAR(64), IN col VARCHAR(64), IN ddl TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
  ) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN ', ddl);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

CALL _qlbh_add_col('products', 'min_stock', '`min_stock` INT(11) NOT NULL DEFAULT 5');
CALL _qlbh_add_col('products', 'cost_price', '`cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0');

-- orders: store_id, customer_id, paid_amount, debt_amount
CALL _qlbh_add_col('orders', 'store_id', '`store_id` INT(11) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('orders', 'customer_id', '`customer_id` INT(11) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('orders', 'paid_amount', '`paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('orders', 'debt_amount', '`debt_amount` DECIMAL(15,2) NOT NULL DEFAULT 0');

DROP PROCEDURE _qlbh_add_col;

-- ===== 2. Bảng mới =====

-- Tồn kho theo cửa hàng
CREATE TABLE IF NOT EXISTS `product_stock` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `store_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_store` (`product_id`, `store_id`),
  KEY `idx_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Khách hàng
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(32) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(500) DEFAULT NULL,
  `birthday` DATE DEFAULT NULL,
  `loyalty_points` INT(11) NOT NULL DEFAULT 0,
  `debt` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nhà cung cấp
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(32) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address` VARCHAR(500) DEFAULT NULL,
  `debt` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Phiếu nhập
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL,
  `supplier_id` INT(11) NOT NULL,
  `store_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `debt_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `user_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Item phiếu nhập
CREATE TABLE IF NOT EXISTS `purchases_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  `cost_price` DECIMAL(15,2) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_purchase` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Phiếu thu/chi (công nợ)
CREATE TABLE IF NOT EXISTS `cash_payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `kind` ENUM('receive_customer','pay_supplier','other_in','other_out') NOT NULL,
  `party_type` ENUM('customer','supplier','other') NOT NULL,
  `party_id` INT(11) NOT NULL DEFAULT 0,
  `amount` DECIMAL(15,2) NOT NULL,
  `reference` VARCHAR(64) DEFAULT NULL,
  `note` TEXT,
  `user_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_party` (`party_type`, `party_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chuyển kho giữa cửa hàng
CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL,
  `from_store_id` INT(11) NOT NULL,
  `to_store_id` INT(11) NOT NULL,
  `status` ENUM('draft','completed','cancelled') NOT NULL DEFAULT 'completed',
  `note` TEXT,
  `user_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stock_transfers_item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `transfer_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_transfer` (`transfer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== 3. Backfill: copy products.qty + products.store_id sang product_stock =====
-- Chỉ chèn nếu chưa có row tương ứng.
INSERT INTO `product_stock` (`product_id`, `store_id`, `qty`)
SELECT p.id, p.store_id, CAST(p.qty AS SIGNED)
FROM `products` p
LEFT JOIN `product_stock` ps ON ps.product_id = p.id AND ps.store_id = p.store_id
WHERE p.store_id > 0 AND ps.id IS NULL;
