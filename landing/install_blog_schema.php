<?php
// Chạy 1 lần qua HTTP để cài bảng blog_posts. Tự xoá sau khi xong.
require_once __DIR__ . '/includes/db.php';
header('Content-Type: text/plain; charset=utf-8');
try {
    $pdo = master_pdo();
    $sql = file_get_contents(dirname(__DIR__) . '/blog_schema.sql');
    if (!$sql) {
        // Fallback inline
        $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
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
        ) DEFAULT CHARSET=utf8mb4;";
    }
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if (stripos($stmt, 'CREATE') === 0 || stripos($stmt, 'ALTER') === 0) {
            $pdo->exec($stmt);
        }
    }
    echo "OK: bảng blog_posts đã sẵn sàng.\n";
    @unlink(__FILE__);
    echo "Self-deleted.\n";
} catch (Exception $e) {
    http_response_code(500);
    echo "ERROR: " . $e->getMessage();
}
