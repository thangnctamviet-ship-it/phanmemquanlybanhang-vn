-- Migration 003: Type fix + Index + Charset unify
-- Fix nợ kỹ thuật từ codebase 2018: VARCHAR(255) cho số/timestamp, thiếu index, utf8 vs utf8mb4
-- Idempotent: check trước khi alter

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
-- Cho phép truncate '' → 0 khi convert string → DECIMAL/INT (data cũ có thể NULL/empty)
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', '');

-- Sanitize trước khi convert: NULL/'' → 0 cho các cột số sẽ chuyển kiểu
UPDATE `products`    SET `price` = '0'          WHERE `price` IS NULL OR `price` = '' OR `price` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `products`    SET `qty` = '0'            WHERE `qty` IS NULL OR `qty` = '' OR `qty` NOT REGEXP '^-?[0-9]+$';
UPDATE `orders`      SET `date_time` = '0'      WHERE `date_time` IS NULL OR `date_time` = '' OR `date_time` NOT REGEXP '^[0-9]+$';
UPDATE `orders`      SET `gross_amount` = '0'        WHERE `gross_amount` IS NULL OR `gross_amount` = '' OR `gross_amount` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `service_charge_rate` = '0' WHERE `service_charge_rate` IS NULL OR `service_charge_rate` = '' OR `service_charge_rate` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `service_charge` = '0'      WHERE `service_charge` IS NULL OR `service_charge` = '' OR `service_charge` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `vat_charge_rate` = '0'     WHERE `vat_charge_rate` IS NULL OR `vat_charge_rate` = '' OR `vat_charge_rate` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `vat_charge` = '0'          WHERE `vat_charge` IS NULL OR `vat_charge` = '' OR `vat_charge` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `net_amount` = '0'          WHERE `net_amount` IS NULL OR `net_amount` = '' OR `net_amount` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders`      SET `discount` = '0'            WHERE `discount` IS NULL OR `discount` = '' OR `discount` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders_item` SET `qty` = '0'                 WHERE `qty` IS NULL OR `qty` = '' OR `qty` NOT REGEXP '^-?[0-9]+$';
UPDATE `orders_item` SET `rate` = '0'                WHERE `rate` IS NULL OR `rate` = '' OR `rate` NOT REGEXP '^[0-9]+\\.?[0-9]*$';
UPDATE `orders_item` SET `amount` = '0'              WHERE `amount` IS NULL OR `amount` = '' OR `amount` NOT REGEXP '^[0-9]+\\.?[0-9]*$';

-- ===== Helper: kiểm tra column type trước khi alter =====
DROP PROCEDURE IF EXISTS _qlbh_modify_col;
DELIMITER $$
CREATE PROCEDURE _qlbh_modify_col(IN tbl VARCHAR(64), IN col VARCHAR(64), IN new_type VARCHAR(200))
BEGIN
  DECLARE current_type VARCHAR(200);
  SELECT COLUMN_TYPE INTO current_type
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col;
  -- So sánh không phân biệt hoa thường + bỏ khoảng trắng
  IF current_type IS NOT NULL
     AND LOWER(REPLACE(current_type,' ','')) <> LOWER(REPLACE(new_type,' ',''))
  THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` MODIFY COLUMN `', col, '` ', new_type);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS _qlbh_add_index;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_index(IN tbl VARCHAR(64), IN idx_name VARCHAR(64), IN idx_cols VARCHAR(200))
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND INDEX_NAME = idx_name
  ) THEN
    SET @sql := CONCAT('CREATE INDEX `', idx_name, '` ON `', tbl, '` (', idx_cols, ')');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- ===== A. CHARSET UNIFY: utf8 → utf8mb4 cho bảng cũ =====
-- An toàn: utf8mb4 backward compatible với utf8, mọi data hiện có vẫn đọc được.
-- LƯU Ý: VARCHAR(255) utf8mb4 = 1020 bytes — vẫn dưới row size limit InnoDB.

ALTER TABLE `products`         CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `orders`           CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `orders_item`      CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `brands`           CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `categories`       CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `stores`           CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `attributes`       CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `attribute_value`  CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users`            CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_group`       CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `groups`           CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `company`          CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ===== B. TYPE FIX: VARCHAR(255) → kiểu chuẩn =====
-- MySQL tự convert string số → DECIMAL/INT/BIGINT khi MODIFY. Không cần script copy.

-- products: price, qty (sku/name/image/description giữ varchar/text)
CALL _qlbh_modify_col('products', 'price', 'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('products', 'qty',   'INT NOT NULL DEFAULT 0');

-- orders: tất cả số + timestamp
CALL _qlbh_modify_col('orders', 'date_time',           'BIGINT NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'gross_amount',        'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'service_charge_rate', 'DECIMAL(8,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'service_charge',      'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'vat_charge_rate',     'DECIMAL(8,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'vat_charge',          'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'net_amount',          'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders', 'discount',            'DECIMAL(15,2) NOT NULL DEFAULT 0');

-- orders_item: qty/rate/amount
CALL _qlbh_modify_col('orders_item', 'qty',    'INT NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders_item', 'rate',   'DECIMAL(15,2) NOT NULL DEFAULT 0');
CALL _qlbh_modify_col('orders_item', 'amount', 'DECIMAL(15,2) NOT NULL DEFAULT 0');

-- ===== C. INDEX cho query nóng =====

-- orders: filter theo ngày + paid_status + store + customer + bill_no lookup
CALL _qlbh_add_index('orders', 'idx_date_time',   '`date_time`');
CALL _qlbh_add_index('orders', 'idx_paid_status', '`paid_status`');
CALL _qlbh_add_index('orders', 'idx_user',        '`user_id`');
CALL _qlbh_add_index('orders', 'idx_bill_no',     '`bill_no`');
CALL _qlbh_add_index('orders', 'idx_date_paid',   '`date_time`, `paid_status`');
-- store_id/customer_id đã có sau migration 001 (default 0)
CALL _qlbh_add_index('orders', 'idx_store',       '`store_id`');
CALL _qlbh_add_index('orders', 'idx_customer',    '`customer_id`');

-- orders_item: lookup theo order + product
CALL _qlbh_add_index('orders_item', 'idx_order',         '`order_id`');
CALL _qlbh_add_index('orders_item', 'idx_product',       '`product_id`');
CALL _qlbh_add_index('orders_item', 'idx_order_product', '`order_id`, `product_id`');

-- products: availability + sku/barcode lookup
CALL _qlbh_add_index('products', 'idx_avail', '`availability`');
CALL _qlbh_add_index('products', 'idx_sku',   '`sku`');

-- attribute_value (parent lookup)
CALL _qlbh_add_index('attribute_value', 'idx_parent', '`attribute_parent_id`');

-- user_group
CALL _qlbh_add_index('user_group', 'idx_user',  '`user_id`');
CALL _qlbh_add_index('user_group', 'idx_group', '`group_id`');

-- Indexes cho bảng mới (đã có sẵn 1 số từ 001/002, thêm cho an toàn)
CALL _qlbh_add_index('customers', 'idx_phone',   '`phone`');
CALL _qlbh_add_index('suppliers', 'idx_active',  '`active`');

-- ===== D. Cleanup procedures =====
DROP PROCEDURE _qlbh_modify_col;
DROP PROCEDURE _qlbh_add_index;

SET FOREIGN_KEY_CHECKS = 1;
