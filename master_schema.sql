-- Master DB cho SaaS Multi-tenant Quản lý bán hàng
CREATE DATABASE IF NOT EXISTS master_quanlybanhang DEFAULT CHARSET utf8mb4;
USE master_quanlybanhang;

CREATE TABLE IF NOT EXISTS tenants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subdomain VARCHAR(63) UNIQUE NOT NULL,
  shop_name VARCHAR(255) NOT NULL,
  owner_email VARCHAR(255) NOT NULL,
  db_name VARCHAR(64) NOT NULL,
  db_user VARCHAR(64) NOT NULL,
  db_pass VARCHAR(64) NOT NULL,
  status ENUM('trial','active','expired','suspended','pending_provision') DEFAULT 'trial',
  plan VARCHAR(20) DEFAULT 'trial',
  paid_branches INT DEFAULT 2,
  trial_started_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenant_id INT NOT NULL,
  plan VARCHAR(20) NOT NULL,
  amount INT NOT NULL,
  months_added INT DEFAULT 0,
  branches_added INT DEFAULT 0,
  bank_ref VARCHAR(255),
  status ENUM('pending','confirmed','rejected') DEFAULT 'pending',
  confirmed_at DATETIME NULL,
  notes TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);
