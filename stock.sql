-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2018 at 10:20 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stock`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value`
--

CREATE TABLE `attribute_value` (
  `id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `attribute_parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attribute_value`
--

INSERT INTO `attribute_value` (`id`, `value`, `attribute_parent_id`) VALUES
(5, 'Blue', 2),
(6, 'White', 2),
(7, 'M', 3),
(8, 'L', 3),
(9, 'Green', 2),
(10, 'Black', 2),
(12, 'Grey', 2),
(13, 'S', 3);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `active`) VALUES
(4, 'ABC Inc.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `active`) VALUES
(4, 'Microscope', 1);

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `service_charge_value` varchar(255) NOT NULL,
  `vat_charge_value` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `currency` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_name`, `service_charge_value`, `vat_charge_value`, `address`, `phone`, `country`, `message`, `currency`) VALUES
(1, 'ABC Inc.', '13', '10', '1234 Main St. Los Angeles, CA 98765 U.S.A.', '(123) 456-7890', 'United States of America', 'Sample message<br>', 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `permission` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `permission`) VALUES
(1, 'Administrator', 'a:36:{i:0;s:10:\"createUser\";i:1;s:10:\"updateUser\";i:2;s:8:\"viewUser\";i:3;s:10:\"deleteUser\";i:4;s:11:\"createGroup\";i:5;s:11:\"updateGroup\";i:6;s:9:\"viewGroup\";i:7;s:11:\"deleteGroup\";i:8;s:11:\"createBrand\";i:9;s:11:\"updateBrand\";i:10;s:9:\"viewBrand\";i:11;s:11:\"deleteBrand\";i:12;s:14:\"createCategory\";i:13;s:14:\"updateCategory\";i:14;s:12:\"viewCategory\";i:15;s:14:\"deleteCategory\";i:16;s:11:\"createStore\";i:17;s:11:\"updateStore\";i:18;s:9:\"viewStore\";i:19;s:11:\"deleteStore\";i:20;s:15:\"createAttribute\";i:21;s:15:\"updateAttribute\";i:22;s:13:\"viewAttribute\";i:23;s:15:\"deleteAttribute\";i:24;s:13:\"createProduct\";i:25;s:13:\"updateProduct\";i:26;s:11:\"viewProduct\";i:27;s:13:\"deleteProduct\";i:28;s:11:\"createOrder\";i:29;s:11:\"updateOrder\";i:30;s:9:\"viewOrder\";i:31;s:11:\"deleteOrder\";i:32;s:11:\"viewReports\";i:33;s:13:\"updateCompany\";i:34;s:11:\"viewProfile\";i:35;s:13:\"updateSetting\";}'),
(4, 'Owners', 'a:36:{i:0;s:10:\"createUser\";i:1;s:10:\"updateUser\";i:2;s:8:\"viewUser\";i:3;s:10:\"deleteUser\";i:4;s:11:\"createGroup\";i:5;s:11:\"updateGroup\";i:6;s:9:\"viewGroup\";i:7;s:11:\"deleteGroup\";i:8;s:11:\"createBrand\";i:9;s:11:\"updateBrand\";i:10;s:9:\"viewBrand\";i:11;s:11:\"deleteBrand\";i:12;s:14:\"createCategory\";i:13;s:14:\"updateCategory\";i:14;s:12:\"viewCategory\";i:15;s:14:\"deleteCategory\";i:16;s:11:\"createStore\";i:17;s:11:\"updateStore\";i:18;s:9:\"viewStore\";i:19;s:11:\"deleteStore\";i:20;s:15:\"createAttribute\";i:21;s:15:\"updateAttribute\";i:22;s:13:\"viewAttribute\";i:23;s:15:\"deleteAttribute\";i:24;s:13:\"createProduct\";i:25;s:13:\"updateProduct\";i:26;s:11:\"viewProduct\";i:27;s:13:\"deleteProduct\";i:28;s:11:\"createOrder\";i:29;s:11:\"updateOrder\";i:30;s:9:\"viewOrder\";i:31;s:11:\"deleteOrder\";i:32;s:11:\"viewReports\";i:33;s:13:\"updateCompany\";i:34;s:11:\"viewProfile\";i:35;s:13:\"updateSetting\";}');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `bill_no` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `date_time` varchar(255) NOT NULL,
  `gross_amount` varchar(255) NOT NULL,
  `service_charge_rate` varchar(255) NOT NULL,
  `service_charge` varchar(255) NOT NULL,
  `vat_charge_rate` varchar(255) NOT NULL,
  `vat_charge` varchar(255) NOT NULL,
  `net_amount` varchar(255) NOT NULL,
  `discount` varchar(255) NOT NULL,
  `paid_status` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders_item`
--

CREATE TABLE `orders_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `qty` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `description` text NOT NULL,
  `attribute_value_id` text,
  `brand_id` text NOT NULL,
  `category_id` text NOT NULL,
  `store_id` int(11) NOT NULL,
  `availability` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `gender` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `phone`, `gender`) VALUES
(1, 'admin', '$2y$10$ZrBk2zWOLhPAaOhncDBJv.pKAfhFYywahFQXY4NXDmhOcaRtLdAfS', 'admin@admin.com', 'admin', 'a', '12345678910', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attribute_value`
--
ALTER TABLE `attribute_value`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attribute_value`
--
ALTER TABLE `attribute_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders_item`
--
ALTER TABLE `orders_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
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
