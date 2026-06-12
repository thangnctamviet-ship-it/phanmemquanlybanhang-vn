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
  (1, 'Tiền mặt', 'cash', 0),
  (2, 'Chuyển khoản', 'bank', 0);
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
