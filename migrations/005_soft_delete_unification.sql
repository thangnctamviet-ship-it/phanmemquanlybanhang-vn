-- Migration 005: Soft delete chuẩn hoá
-- Thêm `deleted_at TIMESTAMP NULL` cho tất cả bảng entity (skip junction/log/pivot)
-- Idempotent: dùng procedure _qlbh_add_col đã có sẵn từ migration 002

SET NAMES utf8mb4;

-- Procedure (define lại để standalone, IF NOT EXISTS không hỗ trợ PROCEDURE pre-8.0)
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

-- Procedure add index có check
DROP PROCEDURE IF EXISTS _qlbh_add_index;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_index(IN tbl VARCHAR(64), IN idx VARCHAR(64), IN cols TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = DATABASE() AND table_name = tbl AND index_name = idx
  ) AND EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = tbl
  ) THEN
    SET @sql := CONCAT('CREATE INDEX `', idx, '` ON `', tbl, '`(', cols, ')');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- ===== Thêm deleted_at cho 29 bảng entity =====
CALL _qlbh_add_col('attribute_value',   'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('attributes',        'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('brands',            'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('cash_accounts',     'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('cash_payments',     'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('categories',        'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('commission_rules',  'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('company',           'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('customer_groups',   'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('customers',         'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('employee_shifts',   'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('groups',            'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('orders',            'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('products',          'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('product_batches',   'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('product_combos',    'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('product_prices',    'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('product_stock',     'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('product_units',     'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('promotions',        'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('purchase_returns',  'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('purchases',         'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('returns',           'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('stock_checks',      'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('stock_transfers',   'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('stores',            'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('suppliers',         'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('users',             'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('vouchers',          'deleted_at', 'TIMESTAMP NULL DEFAULT NULL');

-- ===== Index cho query "WHERE deleted_at IS NULL" =====
CALL _qlbh_add_index('products',  'idx_deleted_at', '`deleted_at`');
CALL _qlbh_add_index('orders',    'idx_deleted_at', '`deleted_at`');
CALL _qlbh_add_index('customers', 'idx_deleted_at', '`deleted_at`');
CALL _qlbh_add_index('suppliers', 'idx_deleted_at', '`deleted_at`');
CALL _qlbh_add_index('users',     'idx_deleted_at', '`deleted_at`');

-- ===== Cleanup procedure =====
DROP PROCEDURE _qlbh_add_col;
DROP PROCEDURE _qlbh_add_index;
