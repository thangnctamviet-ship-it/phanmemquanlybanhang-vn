-- Blog schema (apply lên master DB iqosvnsh_master_qlbh)
CREATE TABLE IF NOT EXISTS blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(200) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt VARCHAR(500),
  cover_image VARCHAR(500),
  content MEDIUMTEXT NOT NULL,
  tags VARCHAR(255),
  meta_title VARCHAR(255),
  meta_description VARCHAR(500),
  status ENUM('draft','published') DEFAULT 'draft',
  views INT DEFAULT 0,
  published_at DATETIME NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_published (published_at)
) DEFAULT CHARSET=utf8mb4;
