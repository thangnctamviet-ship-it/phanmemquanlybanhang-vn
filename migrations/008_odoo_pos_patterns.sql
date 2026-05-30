-- Migration 008: Áp dụng patterns từ Odoo POS (best practice enterprise)
-- Thêm 11 bảng + 5 cột để đạt chuẩn SaaS POS chuyên nghiệp:
--   1. POS terminals (multi-device support)
--   2. POS sessions (ca làm việc - đối soát tiền)
--   3. Inventory movements (audit trail kho)
--   4. Order payments (multi-method per order)
--   5. Payment methods config (không hardcode)
--   6. Store floors + tables (F&B/nhà hàng)
--   7. Loyalty cards + transactions (chuẩn hoá thay vì loyalty_points string)
--   8. Offline sync support
--   9. Kitchen Display System (KDS) status cho orders_item

SET NAMES utf8mb4;

DROP PROCEDURE IF EXISTS _qlbh_add_col;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_col(IN tbl VARCHAR(64), IN col VARCHAR(64), IN ddl TEXT)
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=tbl AND column_name=col)
     AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=tbl) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', ddl);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS _qlbh_add_fk;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_fk(
  IN tbl VARCHAR(64), IN fk_name VARCHAR(64), IN col VARCHAR(64),
  IN ref_tbl VARCHAR(64), IN ref_col VARCHAR(64),
  IN on_delete VARCHAR(20), IN on_update VARCHAR(20)
)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.table_constraints
    WHERE constraint_schema=DATABASE() AND table_name=tbl AND constraint_name=fk_name AND constraint_type='FOREIGN KEY'
  ) AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=tbl)
    AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=ref_tbl) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD CONSTRAINT `', fk_name,
      '` FOREIGN KEY (`', col, '`) REFERENCES `', ref_tbl, '`(`', ref_col, '`) ',
      'ON DELETE ', on_delete, ' ON UPDATE ', on_update);
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- ===== 1. POS TERMINALS (quầy thu ngân vật lý) =====
CREATE TABLE IF NOT EXISTS `pos_terminals` (
  `id`          INT NOT NULL AUTO_INCREMENT,
  `store_id`    INT NOT NULL,
  `name`        VARCHAR(100) NOT NULL COMMENT 'VD: Quầy 1, Quầy Bar',
  `device_id`   VARCHAR(255) DEFAULT NULL COMMENT 'ID thiết bị (tablet/PC)',
  `settings`    JSON DEFAULT NULL COMMENT 'Cài đặt riêng (máy in, ngăn kéo tiền,...)',
  `active`      TINYINT NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_store` (`store_id`),
  KEY `idx_device` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('pos_terminals','fk_pt_store','store_id','stores','id','CASCADE','CASCADE');

-- ===== 2. POS SESSIONS (ca làm việc) =====
CREATE TABLE IF NOT EXISTS `pos_sessions` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `terminal_id`     INT NOT NULL,
  `opened_by`       INT NOT NULL COMMENT 'user mở ca',
  `closed_by`       INT DEFAULT NULL,
  `session_code`    VARCHAR(50) NOT NULL COMMENT 'SS-20260530-001',
  `state`           ENUM('open','closing','closed') NOT NULL DEFAULT 'open',
  `opening_cash`    DECIMAL(15,2) NOT NULL DEFAULT 0,
  `closing_cash`    DECIMAL(15,2) DEFAULT NULL COMMENT 'Tiền thực kiểm cuối ca',
  `expected_cash`   DECIMAL(15,2) DEFAULT NULL COMMENT 'Tiền lý thuyết',
  `cash_difference` DECIMAL(15,2) DEFAULT 0 COMMENT 'Chênh lệch (thiếu/thừa)',
  `total_orders`    INT NOT NULL DEFAULT 0,
  `total_revenue`   DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_discount`  DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_refunds`   DECIMAL(15,2) NOT NULL DEFAULT 0,
  `note`            TEXT,
  `opened_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at`       TIMESTAMP NULL DEFAULT NULL,
  `created_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_session_code` (`session_code`),
  KEY `idx_terminal` (`terminal_id`),
  KEY `idx_state` (`state`),
  KEY `idx_opened_at` (`opened_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('pos_sessions','fk_ps_terminal','terminal_id','pos_terminals','id','RESTRICT','CASCADE');
CALL _qlbh_add_fk('pos_sessions','fk_ps_opened','opened_by','users','id','RESTRICT','CASCADE');

-- Link orders với session
CALL _qlbh_add_col('orders', 'session_id', 'INT NULL DEFAULT NULL COMMENT "POS session"');
CALL _qlbh_add_col('orders', 'terminal_id', 'INT NULL DEFAULT NULL');
CALL _qlbh_add_col('orders', 'offline_id', 'VARCHAR(100) NULL DEFAULT NULL COMMENT "UUID tạo offline, dùng dedup khi sync"');
CALL _qlbh_add_col('orders', 'synced_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_fk('orders','fk_o_session','session_id','pos_sessions','id','SET NULL','CASCADE');
CALL _qlbh_add_fk('orders','fk_o_terminal','terminal_id','pos_terminals','id','SET NULL','CASCADE');

-- ===== 3. INVENTORY MOVEMENTS (audit trail kho) =====
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id`               BIGINT NOT NULL AUTO_INCREMENT,
  `product_id`       INT NOT NULL,
  `source_store_id`  INT DEFAULT NULL COMMENT 'NULL nếu nhập từ NCC',
  `dest_store_id`    INT DEFAULT NULL COMMENT 'NULL nếu bán cho khách',
  `movement_type`    ENUM(
    'purchase_in','sale_out','transfer_in','transfer_out',
    'adjustment_in','adjustment_out','return_in','return_out','damage'
  ) NOT NULL,
  `quantity`         DECIMAL(15,3) NOT NULL COMMENT 'Số lượng (+/-)',
  `cost_price`       DECIMAL(15,2) NOT NULL DEFAULT 0,
  `reference_type`   VARCHAR(50) DEFAULT NULL COMMENT 'order|purchase|transfer|stock_check|return',
  `reference_id`     INT DEFAULT NULL,
  `lot_no`           VARCHAR(64) DEFAULT NULL,
  `serial_no`        VARCHAR(100) DEFAULT NULL,
  `expiry_date`      DATE DEFAULT NULL,
  `note`             TEXT,
  `created_by`       INT DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_source` (`source_store_id`),
  KEY `idx_dest` (`dest_store_id`),
  KEY `idx_type` (`movement_type`),
  KEY `idx_ref` (`reference_type`, `reference_id`),
  KEY `idx_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('inventory_movements','fk_im_product','product_id','products','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('inventory_movements','fk_im_user','created_by','users','id','SET NULL','CASCADE');

-- ===== 4. PAYMENT METHODS (config thay vì hardcode) =====
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id`         INT NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL COMMENT 'Tiền mặt, Chuyển khoản, MoMo,...',
  `type`       ENUM('cash','bank_transfer','card','ewallet','qr_code','other') NOT NULL DEFAULT 'cash',
  `provider`   VARCHAR(50) DEFAULT NULL COMMENT 'momo|vnpay|zalopay|vietqr|null',
  `icon`       VARCHAR(100) DEFAULT NULL,
  `config`     JSON DEFAULT NULL COMMENT 'API keys, merchant ID,...',
  `is_default` TINYINT NOT NULL DEFAULT 0,
  `active`     TINYINT NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pm_name` (`name`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: 6 payment method phổ biến VN
INSERT IGNORE INTO `payment_methods` (`id`,`name`,`type`,`provider`,`is_default`,`sort_order`) VALUES
  (1, 'Tiền mặt',       'cash',         NULL,      1, 1),
  (2, 'Chuyển khoản',   'bank_transfer',NULL,      0, 2),
  (3, 'Thẻ ngân hàng',  'card',         NULL,      0, 3),
  (4, 'MoMo',           'ewallet',      'momo',    0, 4),
  (5, 'VNPay',          'ewallet',      'vnpay',   0, 5),
  (6, 'VietQR',         'qr_code',      'vietqr',  0, 6);

-- ===== 5. ORDER PAYMENTS (1 đơn nhiều phương thức) =====
CREATE TABLE IF NOT EXISTS `order_payments` (
  `id`                INT NOT NULL AUTO_INCREMENT,
  `order_id`          INT NOT NULL,
  `payment_method_id` INT NOT NULL,
  `amount`            DECIMAL(15,2) NOT NULL,
  `status`            ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'completed',
  `transaction_ref`   VARCHAR(255) DEFAULT NULL COMMENT 'Mã GD từ MoMo/VNPay',
  `provider_data`     JSON DEFAULT NULL,
  `payment_date`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `refunded_at`       TIMESTAMP NULL DEFAULT NULL,
  `refund_amount`     DECIMAL(15,2) NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_method` (`payment_method_id`),
  KEY `idx_date` (`payment_date`),
  KEY `idx_ref` (`transaction_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('order_payments','fk_op_order','order_id','orders','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('order_payments','fk_op_method','payment_method_id','payment_methods','id','RESTRICT','CASCADE');

-- ===== 6. STORE FLOORS + TABLES (F&B) =====
CREATE TABLE IF NOT EXISTS `store_floors` (
  `id`               INT NOT NULL AUTO_INCREMENT,
  `store_id`         INT NOT NULL,
  `name`             VARCHAR(100) NOT NULL COMMENT 'Tầng 1, Sân vườn,...',
  `sort_order`       INT NOT NULL DEFAULT 0,
  `background_color` VARCHAR(20) DEFAULT '#f0f0f0',
  `active`           TINYINT NOT NULL DEFAULT 1,
  `created_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('store_floors','fk_sf_store','store_id','stores','id','CASCADE','CASCADE');

CREATE TABLE IF NOT EXISTS `store_tables` (
  `id`          INT NOT NULL AUTO_INCREMENT,
  `floor_id`    INT NOT NULL,
  `name`        VARCHAR(50) NOT NULL COMMENT 'Bàn 1, Bàn VIP,...',
  `seats`       INT NOT NULL DEFAULT 4,
  `shape`       ENUM('square','round','rectangle') NOT NULL DEFAULT 'square',
  `position_x`  INT NOT NULL DEFAULT 0 COMMENT 'Vị trí ngang trên sơ đồ',
  `position_y`  INT NOT NULL DEFAULT 0,
  `width`       INT NOT NULL DEFAULT 100,
  `height`      INT NOT NULL DEFAULT 100,
  `status`      ENUM('available','occupied','reserved','closed') NOT NULL DEFAULT 'available',
  `active`      TINYINT NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_floor` (`floor_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('store_tables','fk_st_floor','floor_id','store_floors','id','CASCADE','CASCADE');

-- Link orders với bàn
CALL _qlbh_add_col('orders', 'table_id', 'INT NULL DEFAULT NULL COMMENT "Bàn (F&B)"');
CALL _qlbh_add_col('orders', 'guest_count', 'INT NULL DEFAULT NULL COMMENT "Số khách"');
CALL _qlbh_add_fk('orders','fk_o_table','table_id','store_tables','id','SET NULL','CASCADE');

-- KDS status cho orders_item (Kitchen Display System)
CALL _qlbh_add_col('orders_item', 'kitchen_status', 'ENUM("pending","preparing","ready","served") NOT NULL DEFAULT "pending"');
CALL _qlbh_add_col('orders_item', 'kitchen_sent_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('orders_item', 'kitchen_ready_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('orders_item', 'note', 'VARCHAR(255) NULL DEFAULT NULL COMMENT "Ít đá, không hành,..."');

-- ===== 7. LOYALTY CARDS + TRANSACTIONS (thay loyalty_points string) =====
CREATE TABLE IF NOT EXISTS `loyalty_cards` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `customer_id`     INT NOT NULL,
  `code`            VARCHAR(50) NOT NULL COMMENT 'Mã thẻ (auto-generate hoặc nhập tay)',
  `points_balance`  DECIMAL(10,2) NOT NULL DEFAULT 0,
  `total_earned`    DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm đã tích cả đời',
  `total_redeemed`  DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Tổng điểm đã đổi',
  `status`          ENUM('active','expired','suspended') NOT NULL DEFAULT 'active',
  `expires_at`      TIMESTAMP NULL DEFAULT NULL,
  `created_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lc_code` (`code`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('loyalty_cards','fk_lc_customer','customer_id','customers','id','CASCADE','CASCADE');

CREATE TABLE IF NOT EXISTS `loyalty_transactions` (
  `id`            BIGINT NOT NULL AUTO_INCREMENT,
  `card_id`       INT NOT NULL,
  `order_id`      INT DEFAULT NULL,
  `type`          ENUM('earn','redeem','expire','adjust') NOT NULL,
  `points`        DECIMAL(10,2) NOT NULL COMMENT '+/- điểm',
  `balance_after` DECIMAL(10,2) NOT NULL,
  `description`   VARCHAR(255) DEFAULT NULL,
  `created_at`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_card` (`card_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_type` (`type`),
  KEY `idx_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CALL _qlbh_add_fk('loyalty_transactions','fk_lt_card','card_id','loyalty_cards','id','CASCADE','CASCADE');
CALL _qlbh_add_fk('loyalty_transactions','fk_lt_order','order_id','orders','id','SET NULL','CASCADE');

-- ===== 8. ENRICH ORDERS với fields Odoo có =====
CALL _qlbh_add_col('orders', 'order_type', 'ENUM("pos","online","phone","delivery","wholesale") NOT NULL DEFAULT "pos"');
CALL _qlbh_add_col('orders', 'change_amount', 'DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT "Tiền thối"');
CALL _qlbh_add_col('orders', 'source', 'VARCHAR(50) NULL DEFAULT "pos" COMMENT "pos|web|app|zalo|shopee"');
CALL _qlbh_add_col('orders', 'cashier_id', 'INT NULL DEFAULT NULL COMMENT "Người thu ngân"');
CALL _qlbh_add_col('orders', 'cancelled_at', 'TIMESTAMP NULL DEFAULT NULL');
CALL _qlbh_add_col('orders', 'cancelled_by', 'INT NULL DEFAULT NULL');
CALL _qlbh_add_col('orders', 'cancel_reason', 'VARCHAR(255) NULL DEFAULT NULL');
CALL _qlbh_add_fk('orders','fk_o_cashier','cashier_id','users','id','SET NULL','CASCADE');
CALL _qlbh_add_fk('orders','fk_o_cancelled','cancelled_by','users','id','SET NULL','CASCADE');

-- ===== 9. ENRICH ORDERS_ITEM với cost_price snapshot =====
CALL _qlbh_add_col('orders_item', 'cost_price_snapshot', 'DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT "Giá vốn tại thời điểm bán"');
CALL _qlbh_add_col('orders_item', 'product_name_snapshot', 'VARCHAR(255) NULL DEFAULT NULL COMMENT "Tên SP snapshot, không đổi khi sửa product"');
CALL _qlbh_add_col('orders_item', 'sku_snapshot', 'VARCHAR(100) NULL DEFAULT NULL');

-- ===== 10. UNIQUE constraint cho offline_id (dedup khi sync) =====
DROP PROCEDURE IF EXISTS _qlbh_add_unique;
DELIMITER $$
CREATE PROCEDURE _qlbh_add_unique(IN tbl VARCHAR(64), IN idx_name VARCHAR(64), IN cols TEXT)
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.statistics WHERE table_schema=DATABASE() AND table_name=tbl AND index_name=idx_name)
     AND EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name=tbl) THEN
    SET @sql := CONCAT('ALTER TABLE `', tbl, '` ADD UNIQUE KEY `', idx_name, '`(', cols, ')');
    PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;
CALL _qlbh_add_unique('orders', 'uq_orders_offline_id', '`offline_id`');

-- ===== Cleanup =====
DROP PROCEDURE _qlbh_add_col;
DROP PROCEDURE _qlbh_add_fk;
DROP PROCEDURE _qlbh_add_unique;
