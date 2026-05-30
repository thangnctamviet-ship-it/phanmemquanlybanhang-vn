-- Migration 006: Reset transactional data + Foreign Key constraints
-- ⚠️  PHẢI CHỈ CHẠY KHI DATA CHƯA QUAN TRỌNG (user xác nhận: phần mềm chưa phát hành)
-- Giữ lại: users, user_group, groups, stores, company, settings (auth + config)
-- Wipe:     orders, products, customers, suppliers, purchases, transfers, ...

SET NAMES utf8mb4;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', '');
SET FOREIGN_KEY_CHECKS = 0;

-- ===== A. TRUNCATE bảng transactional (giữ master data / auth) =====
TRUNCATE TABLE orders_item;
TRUNCATE TABLE orders;
TRUNCATE TABLE product_attribute_values;
TRUNCATE TABLE product_brands;
TRUNCATE TABLE product_categories;
TRUNCATE TABLE product_units;
TRUNCATE TABLE product_prices;
TRUNCATE TABLE product_batches;
TRUNCATE TABLE product_combos_item;
TRUNCATE TABLE product_combos;
TRUNCATE TABLE product_stock;
TRUNCATE TABLE products;
TRUNCATE TABLE customers;
TRUNCATE TABLE suppliers;
TRUNCATE TABLE purchases_item;
TRUNCATE TABLE purchases;
TRUNCATE TABLE returns_item;
TRUNCATE TABLE returns;
TRUNCATE TABLE purchase_returns_item;
TRUNCATE TABLE purchase_returns;
TRUNCATE TABLE stock_checks_item;
TRUNCATE TABLE stock_checks;
TRUNCATE TABLE stock_transfers_item;
TRUNCATE TABLE stock_transfers;
TRUNCATE TABLE cash_payments;
TRUNCATE TABLE cash_accounts;
TRUNCATE TABLE promotion_conditions;
TRUNCATE TABLE promotions;
TRUNCATE TABLE vouchers;
TRUNCATE TABLE employee_shifts;
TRUNCATE TABLE commission_rules;
TRUNCATE TABLE taggables;
TRUNCATE TABLE tags;
TRUNCATE TABLE audit_log;
TRUNCATE TABLE brands;
TRUNCATE TABLE categories;
TRUNCATE TABLE attribute_value;
TRUNCATE TABLE attributes;
TRUNCATE TABLE customer_groups;

-- ===== B. Cho phép NULL cho các FK SET NULL =====
-- (MySQL yêu cầu cột nullable để on delete SET NULL hoạt động)
ALTER TABLE `customers`        MODIFY `customer_group_id` INT NULL DEFAULT NULL;
ALTER TABLE `orders`           MODIFY `customer_id`       INT NULL DEFAULT NULL;
ALTER TABLE `orders`           MODIFY `cash_account_id`   INT NULL DEFAULT NULL;
ALTER TABLE `returns`          MODIFY `order_id`          INT NULL DEFAULT NULL;
ALTER TABLE `returns`          MODIFY `customer_id`       INT NULL DEFAULT NULL;
ALTER TABLE `purchase_returns` MODIFY `purchase_id`       INT NULL DEFAULT NULL;
ALTER TABLE `vouchers`         MODIFY `customer_id`       INT NULL DEFAULT NULL;
ALTER TABLE `vouchers`         MODIFY `used_order_id`     INT NULL DEFAULT NULL;
ALTER TABLE `products`         MODIFY `default_supplier_id` INT NULL DEFAULT NULL;

-- ===== C. Tạo helper procedure add FK idempotent =====
DROP PROCEDURE IF EXISTS _qlbh_add_fk;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_fk(
  IN tbl VARCHAR(64), IN fk_name VARCHAR(64),
  IN col VARCHAR(64),
  IN ref_tbl VARCHAR(64), IN ref_col VARCHAR(64),
  IN on_delete VARCHAR(20), IN on_update VARCHAR(20)
)
BEGIN
  -- Skip nếu FK đã tồn tại
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.table_constraints
    WHERE constraint_schema = DATABASE() AND table_name = tbl
      AND constraint_name = fk_name AND constraint_type = 'FOREIGN KEY'
  ) AND EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = tbl
  ) AND EXISTS (
    SELECT 1 FROM information_schema.tables
    WHERE table_schema = DATABASE() AND table_name = ref_tbl
  ) AND EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = tbl AND column_name = col
  ) THEN
    SET @sql := CONCAT(
      'ALTER TABLE `', tbl, '` ADD CONSTRAINT `', fk_name,
      '` FOREIGN KEY (`', col, '`) REFERENCES `', ref_tbl, '`(`', ref_col, '`) ',
      'ON DELETE ', on_delete, ' ON UPDATE ', on_update
    );
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- ===== D. Foreign keys =====
-- Composition rules:
--   CASCADE  : child không có nghĩa nếu parent biến mất (line item, pivot)
--   RESTRICT : không cho xoá parent nếu còn child (bảo vệ data lịch sử)
--   SET NULL : optional reference (nullable column required)

-- ----- Pivot tables: CASCADE -----
CALL _qlbh_add_fk('product_brands',            'fk_pb_product',  'product_id',         'products', 'id', 'CASCADE', 'CASCADE');
CALL _qlbh_add_fk('product_brands',            'fk_pb_brand',    'brand_id',           'brands',   'id', 'CASCADE', 'CASCADE');
CALL _qlbh_add_fk('product_categories',        'fk_pc_product',  'product_id',         'products', 'id', 'CASCADE', 'CASCADE');
CALL _qlbh_add_fk('product_categories',        'fk_pc_category', 'category_id',        'categories','id','CASCADE', 'CASCADE');
CALL _qlbh_add_fk('product_attribute_values',  'fk_pav_product', 'product_id',         'products', 'id', 'CASCADE', 'CASCADE');
CALL _qlbh_add_fk('product_attribute_values',  'fk_pav_value',   'attribute_value_id', 'attribute_value','id','CASCADE','CASCADE');

-- ----- Master entity extensions -----
CALL _qlbh_add_fk('product_stock',    'fk_ps_product', 'product_id', 'products', 'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_stock',    'fk_ps_store',   'store_id',   'stores',   'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_units',    'fk_pu_product', 'product_id', 'products', 'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_batches',  'fk_pba_product','product_id', 'products', 'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_batches',  'fk_pba_store',  'store_id',   'stores',   'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_combos',         'fk_pco_product',  'product_id',       'products',        'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_combos_item',    'fk_pcoi_combo',   'combo_id',         'product_combos',  'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('product_combos_item',    'fk_pcoi_child',   'child_product_id', 'products',        'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('product_prices',         'fk_pp_product',   'product_id',       'products',        'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('attribute_value',        'fk_av_parent',    'attribute_parent_id','attributes',    'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('customers',              'fk_cust_group',   'customer_group_id','customer_groups', 'id', 'SET NULL', 'CASCADE');

-- ----- Orders + items -----
CALL _qlbh_add_fk('orders',         'fk_o_store',    'store_id',    'stores',    'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('orders',         'fk_o_user',     'user_id',     'users',     'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('orders',         'fk_o_customer', 'customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
CALL _qlbh_add_fk('orders_item',    'fk_oi_order',   'order_id',    'orders',    'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('orders_item',    'fk_oi_product', 'product_id',  'products',  'id', 'RESTRICT', 'CASCADE');

-- ----- Purchases + items -----
CALL _qlbh_add_fk('purchases',      'fk_pur_store',    'store_id',    'stores',    'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchases',      'fk_pur_user',     'user_id',     'users',     'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchases',      'fk_pur_supplier', 'supplier_id', 'suppliers', 'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchases_item', 'fk_puri_purchase','purchase_id', 'purchases', 'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('purchases_item', 'fk_puri_product', 'product_id',  'products',  'id', 'RESTRICT', 'CASCADE');

-- ----- Returns -----
CALL _qlbh_add_fk('returns',      'fk_ret_order',     'order_id',    'orders',    'id', 'SET NULL', 'CASCADE');
CALL _qlbh_add_fk('returns',      'fk_ret_customer',  'customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
CALL _qlbh_add_fk('returns',      'fk_ret_store',     'store_id',    'stores',    'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('returns',      'fk_ret_user',      'user_id',     'users',     'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('returns_item', 'fk_reti_return',   'return_id',   'returns',   'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('returns_item', 'fk_reti_product',  'product_id',  'products',  'id', 'RESTRICT', 'CASCADE');

CALL _qlbh_add_fk('purchase_returns',      'fk_pret_purchase',  'purchase_id', 'purchases', 'id', 'SET NULL', 'CASCADE');
CALL _qlbh_add_fk('purchase_returns',      'fk_pret_supplier',  'supplier_id', 'suppliers', 'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchase_returns',      'fk_pret_store',     'store_id',    'stores',    'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchase_returns',      'fk_pret_user',      'user_id',     'users',     'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('purchase_returns_item', 'fk_preti_return',   'purchase_return_id', 'purchase_returns','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('purchase_returns_item', 'fk_preti_product',  'product_id',  'products',  'id', 'RESTRICT', 'CASCADE');

-- ----- Stock movement -----
CALL _qlbh_add_fk('stock_transfers',      'fk_st_from',  'from_store_id', 'stores',  'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('stock_transfers',      'fk_st_to',    'to_store_id',   'stores',  'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('stock_transfers',      'fk_st_user',  'user_id',       'users',   'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('stock_transfers_item', 'fk_sti_xfer', 'transfer_id',   'stock_transfers','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('stock_transfers_item', 'fk_sti_product','product_id',  'products','id', 'RESTRICT', 'CASCADE');

CALL _qlbh_add_fk('stock_checks',      'fk_sc_store',  'store_id', 'stores', 'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('stock_checks',      'fk_sc_user',   'user_id',  'users',  'id', 'RESTRICT', 'CASCADE');
CALL _qlbh_add_fk('stock_checks_item', 'fk_sci_check', 'check_id', 'stock_checks','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('stock_checks_item', 'fk_sci_product','product_id','products','id','RESTRICT','CASCADE');

-- ----- Promotions / Vouchers -----
CALL _qlbh_add_fk('promotions',           'fk_promo_store', 'store_id',          'stores',          'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('promotions',           'fk_promo_group', 'customer_group_id', 'customer_groups', 'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('promotion_conditions', 'fk_pc_promo',    'promotion_id',      'promotions',      'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('vouchers',             'fk_vou_customer','customer_id',       'customers',       'id', 'SET NULL', 'CASCADE');
CALL _qlbh_add_fk('vouchers',             'fk_vou_order',   'used_order_id',     'orders',          'id', 'SET NULL', 'CASCADE');

-- ----- HR / shifts -----
CALL _qlbh_add_fk('employee_shifts', 'fk_es_user',  'user_id',  'users',  'id', 'CASCADE',  'CASCADE');
CALL _qlbh_add_fk('employee_shifts', 'fk_es_store', 'store_id', 'stores', 'id', 'CASCADE',  'CASCADE');

-- ----- Auth -----
CALL _qlbh_add_fk('user_group', 'fk_ug_user',  'user_id',  'users',  'id', 'CASCADE', 'CASCADE');
CALL _qlbh_add_fk('user_group', 'fk_ug_group', 'group_id', 'groups', 'id', 'CASCADE', 'CASCADE');

-- ----- Tags -----
CALL _qlbh_add_fk('taggables', 'fk_tg_tag', 'tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');

-- ===== E. Cleanup =====
DROP PROCEDURE _qlbh_add_fk;
SET FOREIGN_KEY_CHECKS = 1;
