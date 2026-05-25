<?php
// Helper: load .env + kết nối Master DB
function env_load() {
    static $env = null;
    if ($env !== null) return $env;
    $p = dirname(__DIR__, 2) . '/.env';
    if (!file_exists($p)) $p = dirname(__DIR__, 2) . '/.env.example';
    $env = file_exists($p) ? (parse_ini_file($p) ?: []) : [];
    $env += [
        'MASTER_DB_HOST' => 'db',
        'MASTER_DB_NAME' => 'master_quanlybanhang',
        'MASTER_DB_USER' => 'root',
        'MASTER_DB_PASS' => 'root',
        'BASE_DOMAIN' => 'quanlybanhang.shop',
        'LOCAL_BASE_DOMAIN' => 'localhost:8080',
    ];
    return $env;
}

function master_pdo() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $e = env_load();
    $pdo = new PDO("mysql:host={$e['MASTER_DB_HOST']};dbname={$e['MASTER_DB_NAME']};charset=utf8mb4",
        $e['MASTER_DB_USER'], $e['MASTER_DB_PASS'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    return $pdo;
}

function reserved_subdomains() {
    return ['admin','api','www','mail','ftp','blog','shop','store','app','dev','staging','test','support','help'];
}

function validate_subdomain($s) {
    if (!preg_match('/^[a-z0-9][a-z0-9-]{1,28}[a-z0-9]$/', $s)) return 'Subdomain phải 3-30 ký tự, chỉ [a-z0-9-], không bắt đầu/kết thúc bằng dấu gạch.';
    if (in_array($s, reserved_subdomains())) return 'Subdomain này đã bị giữ chỗ. Vui lòng chọn tên khác.';
    return null;
}
