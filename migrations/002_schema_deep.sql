-- Migration 002: Schema sâu — đối chiếu KiotViet, đặt nền tảng cho mọi feature tương lai
-- Idempotent qua procedure helper _qlbh_add_col + CREATE TABLE IF NOT EXISTS
-- UI có thể ẩn, DB sẵn sàng.

SET NAMES utf8mb4;

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

-- ===== A. ALTER bảng cũ =====

-- products: mã vạch, đơn vị, giá sỉ, tồn max, NCC mặc định, flag batch/variant, weight
CALL _qlbh_add_col('products', 'barcode',              '`barcode` VARCHAR(64) DEFAULT NULL');
CALL _qlbh_add_col('products', 'unit',                 '`unit` VARCHAR(32) DEFAULT NULL');
CALL _qlbh_add_col('products', 'wholesale_price',      '`wholesale_price` DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('products', 'max_stock',            '`max_stock` INT NOT NULL DEFAULT 0');
CALL _qlbh_add_col('products', 'default_supplier_id',  '`default_supplier_id` INT NOT NULL DEFAULT 0');
CALL _qlbh_add_col('products', 'has_batches',          '`has_batches` TINYINT(1) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('products', 'has_variants',         '`has_variants` TINYINT(1) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('products', 'weight',               '`weight` DECIMAL(10,3) NOT NULL DEFAULT 0');

-- customers
CALL _qlbh_add_col('customers', 'customer_group_id', '`customer_group_id` INT NOT NULL DEFAULT 0');
CALL _qlbh_add_col('customers', 'credit_limit',      '`credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('customers', 'gender',            "`gender` ENUM('M','F','O') DEFAULT NULL");
CALL _qlbh_add_col('customers', 'tax_code',          '`tax_code` VARCHAR(20) DEFAULT NULL');

-- suppliers
CALL _qlbh_add_col('suppliers', 'credit_limit', '`credit_limit` DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('suppliers', 'tax_code',     '`tax_code` VARCHAR(20) DEFAULT NULL');

-- orders
CALL _qlbh_add_col('orders', 'due_date',        '`due_date` DATE DEFAULT NULL');
CALL _qlbh_add_col('orders', 'cash_account_id', '`cash_account_id` INT NOT NULL DEFAULT 0');
CALL _qlbh_add_col('orders', 'channel',         "`channel` ENUM('pos','online','phone','other') NOT NULL DEFAULT 'other'");

-- purchases
CALL _qlbh_add_col('purchases', 'due_date',        '`due_date` DATE DEFAULT NULL');
CALL _qlbh_add_col('purchases', 'cash_account_id', '`cash_account_id` INT NOT NULL DEFAULT 0');

-- stores
CALL _qlbh_add_col('stores', 'address',   '`address` VARCHAR(500) DEFAULT NULL');
CALL _qlbh_add_col('stores', 'phone',     '`phone` VARCHAR(32) DEFAULT NULL');
CALL _qlbh_add_col('stores', 'latitude',  '`latitude` DECIMAL(10,7) DEFAULT NULL');
CALL _qlbh_add_col('stores', 'longitude', '`longitude` DECIMAL(10,7) DEFAULT NULL');

-- users (CodeIgniter user table)
CALL _qlbh_add_col('users', 'employee_code', '`employee_code` VARCHAR(32) DEFAULT NULL');
CALL _qlbh_add_col('users', 'base_salary',   '`base_salary` DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_add_col('users', 'hired_at',      '`hired_at` DATE DEFAULT NULL');

DROP PROCEDURE _qlbh_add_col;

-- ===== B. Bảng mới =====

-- 1. Settings key-value cho tenant (industry preset, feature flags)
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(64) NOT NULL PRIMARY KEY,
  `value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Nhóm KH
CREATE TABLE IF NOT EXISTS `customer_groups` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Đơn vị quy đổi (1 thùng = 24 lon)
CREATE TABLE IF NOT EXISTS `product_units` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `unit_name` VARCHAR(32) NOT NULL,
  `factor` DECIMAL(15,4) NOT NULL DEFAULT 1,
  `barcode` VARCHAR(64) DEFAULT NULL,
  `price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `is_base` TINYINT(1) NOT NULL DEFAULT 0,
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Bảng giá nâng cao (theo store / customer_group / type)
CREATE TABLE IF NOT EXISTS `product_prices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `price_type` ENUM('retail','wholesale','store','group','custom') NOT NULL DEFAULT 'retail',
  `store_id` INT NOT NULL DEFAULT 0,
  `customer_group_id` INT NOT NULL DEFAULT 0,
  `price` DECIMAL(15,2) NOT NULL,
  `valid_from` DATE DEFAULT NULL,
  `valid_to` DATE DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_product` (`product_id`),
  KEY `idx_type` (`price_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Lô hàng + HSD
CREATE TABLE IF NOT EXISTS `product_batches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `store_id` INT NOT NULL DEFAULT 0,
  `lot_no` VARCHAR(64) NOT NULL,
  `manufacture_date` DATE DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `qty` INT NOT NULL DEFAULT 0,
  `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_product_store` (`product_id`, `store_id`),
  KEY `idx_expiry` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Combo / Quà tặng
CREATE TABLE IF NOT EXISTS `product_combos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `combo_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_combos_item` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `combo_id` INT NOT NULL,
  `child_product_id` INT NOT NULL,
  `qty` INT NOT NULL DEFAULT 1,
  KEY `idx_combo` (`combo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Trả hàng từ KH
CREATE TABLE IF NOT EXISTS `returns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `order_id` INT DEFAULT NULL,
  `customer_id` INT NOT NULL DEFAULT 0,
  `store_id` INT NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `refund_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `reason` VARCHAR(255) DEFAULT NULL,
  `note` TEXT,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_order` (`order_id`),
  KEY `idx_customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `returns_item` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `return_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `qty` INT NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  KEY `idx_return` (`return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Trả hàng cho NCC
CREATE TABLE IF NOT EXISTS `purchase_returns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `purchase_id` INT DEFAULT NULL,
  `supplier_id` INT NOT NULL DEFAULT 0,
  `store_id` INT NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `refund_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `reason` VARCHAR(255) DEFAULT NULL,
  `note` TEXT,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_purchase` (`purchase_id`),
  KEY `idx_supplier` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `purchase_returns_item` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_return_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `qty` INT NOT NULL,
  `cost_price` DECIMAL(15,2) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  KEY `idx_pret` (`purchase_return_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Kiểm kho
CREATE TABLE IF NOT EXISTS `stock_checks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(32) NOT NULL UNIQUE,
  `store_id` INT NOT NULL,
  `status` ENUM('draft','balanced','completed','cancelled') NOT NULL DEFAULT 'draft',
  `total_diff_qty` INT NOT NULL DEFAULT 0,
  `total_diff_value` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note` TEXT,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL,
  KEY `idx_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `stock_checks_item` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `check_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `system_qty` INT NOT NULL,
  `actual_qty` INT NOT NULL,
  `diff_qty` INT NOT NULL,
  `cost_price` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `diff_value` DECIMAL(15,2) NOT NULL DEFAULT 0,
  KEY `idx_check` (`check_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Tài khoản tiền (quỹ)
CREATE TABLE IF NOT EXISTS `cash_accounts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('cash','bank','ewallet','other') NOT NULL DEFAULT 'cash',
  `bank_name` VARCHAR(100) DEFAULT NULL,
  `bank_account_no` VARCHAR(64) DEFAULT NULL,
  `balance` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Ca làm việc NV
CREATE TABLE IF NOT EXISTS `employee_shifts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `store_id` INT NOT NULL,
  `check_in` DATETIME NOT NULL,
  `check_out` DATETIME DEFAULT NULL,
  `opening_cash` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `closing_cash` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_sales` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `order_count` INT NOT NULL DEFAULT 0,
  `note` TEXT,
  KEY `idx_user_store` (`user_id`, `store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Hoa hồng NV
CREATE TABLE IF NOT EXISTS `commission_rules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `scope` ENUM('all','user','product','category') NOT NULL DEFAULT 'all',
  `scope_id` INT NOT NULL DEFAULT 0,
  `commission_type` ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `valid_from` DATE DEFAULT NULL,
  `valid_to` DATE DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `note` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Khuyến mãi
CREATE TABLE IF NOT EXISTS `promotions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `kind` ENUM('discount_percent','discount_amount','buy_x_get_y','combo') NOT NULL,
  `value` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `valid_from` DATETIME DEFAULT NULL,
  `valid_to` DATETIME DEFAULT NULL,
  `store_id` INT NOT NULL DEFAULT 0,
  `customer_group_id` INT NOT NULL DEFAULT 0,
  `usage_limit` INT NOT NULL DEFAULT 0,
  `used_count` INT NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `note` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `promotion_conditions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `promotion_id` INT NOT NULL,
  `condition_type` ENUM('min_total','min_qty','product','category','customer_group') NOT NULL,
  `ref_id` INT NOT NULL DEFAULT 0,
  `ref_value` DECIMAL(15,2) NOT NULL DEFAULT 0,
  KEY `idx_promo` (`promotion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Voucher
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(64) NOT NULL UNIQUE,
  `kind` ENUM('amount','percent') NOT NULL DEFAULT 'amount',
  `value` DECIMAL(15,2) NOT NULL,
  `valid_from` DATETIME DEFAULT NULL,
  `valid_to` DATETIME DEFAULT NULL,
  `customer_id` INT NOT NULL DEFAULT 0,
  `used_at` DATETIME DEFAULT NULL,
  `used_order_id` INT NOT NULL DEFAULT 0,
  `note` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Tags + taggables (polymorphic)
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(64) NOT NULL,
  `color` VARCHAR(16) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `taggables` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tag_id` INT NOT NULL,
  `entity_type` VARCHAR(32) NOT NULL,
  `entity_id` INT NOT NULL,
  KEY `idx_tag` (`tag_id`),
  KEY `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Audit log
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL DEFAULT 0,
  `action` VARCHAR(32) NOT NULL,
  `entity_type` VARCHAR(32) NOT NULL,
  `entity_id` INT NOT NULL DEFAULT 0,
  `old_data` MEDIUMTEXT,
  `new_data` MEDIUMTEXT,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_entity` (`entity_type`, `entity_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== C. Seed defaults =====

INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('industry_preset', 'general'),
  ('enable_batches', '0'),
  ('enable_combos', '0'),
  ('enable_variants', '1'),
  ('enable_wholesale', '0'),
  ('enable_returns', '1'),
  ('enable_loyalty', '1'),
  ('enable_multi_unit', '0'),
  ('enable_promotions', '0'),
  ('enable_employee_shift', '0'),
  ('loyalty_points_per_1000', '1'),
  ('low_stock_threshold', '5');

INSERT IGNORE INTO `customer_groups` (`id`, `name`, `discount_percent`) VALUES
  (1, 'Khách lẻ', 0),
  (2, 'Khách thân thiết', 3),
  (3, 'VIP', 5);

INSERT IGNORE INTO `cash_accounts` (`id`, `name`, `type`, `balance`) VALUES
  (1, 'Tiền mặt', 'cash', 0);
