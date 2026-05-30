-- Migration 007: created_at + updated_at + UNIQUE constraints
-- Idempotent: dùng procedure _qlbh_add_col + _qlbh_add_unique

SET NAMES utf8mb4;

DROP PROCEDURE IF EXISTS _qlbh_add_col;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_col(IN tbl VARCHAR(64), IN col VARCHAR(64), IN ddl TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = tbl AND column_name = col
  ) AND EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = tbl
  ) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', ddl);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS _qlbh_add_unique;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_unique(IN tbl VARCHAR(64), IN idx_name VARCHAR(64), IN cols TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx_name
  ) AND EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = tbl
  ) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD UNIQUE KEY `', idx_name, '`(', cols, ')');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- ===== A. created_at + updated_at cho tất cả bảng entity =====
-- created_at: tự động khi insert (DEFAULT CURRENT_TIMESTAMP)
-- updated_at: tự động khi update (ON UPDATE CURRENT_TIMESTAMP)
-- Lý do KHÔNG thêm vào: junction/pivot/log tables (audit_log đã có created_at)

CALL _qlbh_add_col('attribute_value', 'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('attribute_value', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('attributes',      'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('attributes',      'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('audit_log',       'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('brands',          'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('brands',          'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('cash_accounts',   'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('cash_payments',   'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('categories',      'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('categories',      'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('commission_rules','created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('commission_rules','updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('company',         'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('company',         'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('customer_groups', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('customers',       'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('employee_shifts', 'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('employee_shifts', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('groups',          'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('groups',          'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('orders',          'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('orders',          'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('orders_item',     'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_batches', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_combos',  'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_prices',  'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_prices',  'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_stock',   'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_units',   'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('product_units',   'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('products',        'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('products',        'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('promotion_conditions','created_at','TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('promotions',      'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('promotions',      'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('purchase_returns','updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('purchase_returns_item','created_at','TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('purchases',       'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('purchases_item',  'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('returns',         'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('returns_item',    'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('settings',        'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stock_checks',    'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stock_checks_item','created_at','TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stock_transfers', 'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stock_transfers_item','created_at','TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stores',          'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('stores',          'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('suppliers',       'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('tags',            'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('users',           'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('users',           'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL _qlbh_add_col('vouchers',        'created_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL _qlbh_add_col('vouchers',        'updated_at', 'TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

-- ===== B. UNIQUE constraints =====
-- (Quan trọng: chống trùng SKU, mã hoá đơn, email user, mã voucher, ...)

CALL _qlbh_add_unique('products',   'uq_products_sku',         '`sku`');
CALL _qlbh_add_unique('products',   'uq_products_barcode',     '`barcode`');     -- barcode nullable, MySQL bỏ qua NULL với UNIQUE
CALL _qlbh_add_unique('orders',     'uq_orders_bill_no',       '`bill_no`');
-- purchases không có bill_no (chỉ orders), skip
CALL _qlbh_add_unique('users',      'uq_users_username',       '`username`');
CALL _qlbh_add_unique('users',      'uq_users_email',          '`email`');
CALL _qlbh_add_unique('users',      'uq_users_employee_code',  '`employee_code`');
CALL _qlbh_add_unique('vouchers',   'uq_vouchers_code',        '`code`');
-- promotions chỉ có name (chưa có code), skip
CALL _qlbh_add_unique('customers',  'uq_customers_phone',      '`phone`');

-- product_stock: 1 product chỉ có 1 dòng/store
CALL _qlbh_add_unique('product_stock',       'uq_ps_product_store',     '`product_id`, `store_id`');
-- product_prices: 1 product chỉ có 1 price/store/customer_group/uom
CALL _qlbh_add_unique('product_prices',      'uq_pp_unique_key',        '`product_id`, `store_id`, `customer_group_id`');
-- product_units: 1 product chỉ có 1 unit cùng tên
CALL _qlbh_add_unique('product_units',       'uq_pu_product_unit',      '`product_id`, `unit_name`');
-- product_batches: 1 product chỉ có 1 batch cùng số lô
CALL _qlbh_add_unique('product_batches',     'uq_pba_product_lot',      '`product_id`, `lot_no`');
-- user_group: 1 user chỉ thuộc 1 group (đảm bảo unique)
CALL _qlbh_add_unique('user_group',          'uq_ug_user',              '`user_id`');
-- settings: `key` đã là PRIMARY KEY rồi, skip
-- tags: name unique
CALL _qlbh_add_unique('tags',                'uq_tags_name',            '`name`');
-- customer_groups: name unique
CALL _qlbh_add_unique('customer_groups',     'uq_cg_name',              '`name`');

-- ===== Cleanup =====
DROP PROCEDURE _qlbh_add_col;
DROP PROCEDURE _qlbh_add_unique;
