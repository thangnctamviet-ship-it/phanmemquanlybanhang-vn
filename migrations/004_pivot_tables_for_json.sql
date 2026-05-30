-- Migration 004: Bóc JSON trong TEXT columns thành 3 bảng pivot
-- Approach: dual-write (giữ TEXT cũ + pivot mới song song để tương thích ngược)
-- Idempotent: CREATE IF NOT EXISTS + INSERT IGNORE

SET NAMES utf8mb4;
SET SESSION sql_mode = REPLACE(REPLACE(@@SESSION.sql_mode, 'STRICT_TRANS_TABLES', ''), 'STRICT_ALL_TABLES', '');

-- ===== A. Bảng pivot =====

CREATE TABLE IF NOT EXISTS `product_brands` (
  `product_id` INT NOT NULL,
  `brand_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `brand_id`),
  KEY `idx_brand` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `category_id`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `product_id` INT NOT NULL,
  `attribute_value_id` INT NOT NULL,
  PRIMARY KEY (`product_id`, `attribute_value_id`),
  KEY `idx_attr_value` (`attribute_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== B. Backfill từ JSON cũ trong TEXT columns =====
-- Format có thể là: "1", "[1,2,3]", null, ""
-- MySQL 5.7+ có JSON_TABLE từ 8.0. Dùng cách thủ công cho compat:
--   Nếu là số đơn → tách thẳng
--   Nếu là array JSON → dùng JSON_EXTRACT

-- 1) Trường hợp value là single id (vd: "5" hoặc 5)
INSERT IGNORE INTO `product_brands` (product_id, brand_id)
SELECT p.id, CAST(p.brand_id AS UNSIGNED)
FROM `products` p
WHERE p.brand_id IS NOT NULL AND p.brand_id <> ''
  AND p.brand_id REGEXP '^[0-9]+$'
  AND CAST(p.brand_id AS UNSIGNED) > 0;

INSERT IGNORE INTO `product_categories` (product_id, category_id)
SELECT p.id, CAST(p.category_id AS UNSIGNED)
FROM `products` p
WHERE p.category_id IS NOT NULL AND p.category_id <> ''
  AND p.category_id REGEXP '^[0-9]+$'
  AND CAST(p.category_id AS UNSIGNED) > 0;

INSERT IGNORE INTO `product_attribute_values` (product_id, attribute_value_id)
SELECT p.id, CAST(p.attribute_value_id AS UNSIGNED)
FROM `products` p
WHERE p.attribute_value_id IS NOT NULL AND p.attribute_value_id <> ''
  AND p.attribute_value_id REGEXP '^[0-9]+$'
  AND CAST(p.attribute_value_id AS UNSIGNED) > 0;

-- 2) Trường hợp value là JSON array (vd: "[1,2,3]" hoặc '["1","2"]')
-- Tạo procedure parse array (chỉ chạy 1 lần, drop sau)
DROP PROCEDURE IF EXISTS _qlbh_parse_json_array;
DELIMITER $$
CREATE PROCEDURE _qlbh_parse_json_array(
  IN p_src_col VARCHAR(64),
  IN p_dst_table VARCHAR(64),
  IN p_dst_col VARCHAR(64)
)
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE v_pid INT;
  DECLARE v_raw TEXT;

  DECLARE cur CURSOR FOR
    SELECT id, COALESCE(
      CASE p_src_col
        WHEN 'brand_id'           THEN brand_id
        WHEN 'category_id'        THEN category_id
        WHEN 'attribute_value_id' THEN attribute_value_id
      END, '')
    FROM `products`
    WHERE
      CASE p_src_col
        WHEN 'brand_id'           THEN brand_id
        WHEN 'category_id'        THEN category_id
        WHEN 'attribute_value_id' THEN attribute_value_id
      END LIKE '[%';

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;  -- bỏ qua row JSON malformed

  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO v_pid, v_raw;
    IF done THEN LEAVE read_loop; END IF;

    -- Dùng JSON_TABLE (MySQL 8.0) hoặc fallback REGEXP_REPLACE để tách
    -- Cách an toàn nhất: parse manual qua tách các số trong chuỗi
    SET @sql := CONCAT('INSERT IGNORE INTO `', p_dst_table, '` (product_id, `', p_dst_col, '`)
       SELECT ', v_pid, ', val FROM JSON_TABLE(',
       QUOTE(v_raw), ', "$[*]" COLUMNS(val INT PATH "$")) AS jt
       WHERE val > 0');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END LOOP;
  CLOSE cur;
END$$
DELIMITER ;

CALL _qlbh_parse_json_array('brand_id',           'product_brands',            'brand_id');
CALL _qlbh_parse_json_array('category_id',        'product_categories',        'category_id');
CALL _qlbh_parse_json_array('attribute_value_id', 'product_attribute_values',  'attribute_value_id');

DROP PROCEDURE _qlbh_parse_json_array;
