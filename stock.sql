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
