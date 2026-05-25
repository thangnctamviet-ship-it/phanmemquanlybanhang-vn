<?php
require_once __DIR__ . '/../landing/includes/mailer.php';

function cron_env_load() {
    $path = dirname(__DIR__) . '/.env';
    $env = file_exists($path) ? (parse_ini_file($path) ?: []) : [];
    $env += [
        'MASTER_DB_HOST' => 'db',
        'MASTER_DB_NAME' => 'master_quanlybanhang',
        'MASTER_DB_USER' => 'root',
        'MASTER_DB_PASS' => 'root',
        'BASE_DOMAIN' => 'quanlybanhang.shop',
    ];
    return $env;
}

$env = cron_env_load();
$pdo = new PDO(
    "mysql:host={$env['MASTER_DB_HOST']};dbname={$env['MASTER_DB_NAME']};charset=utf8mb4",
    $env['MASTER_DB_USER'],
    $env['MASTER_DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$pdo->exec("UPDATE tenants SET status='expired' WHERE status IN ('trial','active') AND expires_at < NOW()");

$stmt = $pdo->query("SELECT * FROM tenants WHERE status IN ('trial','active','expired') ORDER BY expires_at ASC");
$sent = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $tenant) {
    $expires = strtotime($tenant['expires_at']);
    $today = strtotime(date('Y-m-d 00:00:00'));
    $expire_day = strtotime(date('Y-m-d 00:00:00', $expires));
    $days = (int)floor(($expire_day - $today) / 86400);
    if (!in_array($days, [3, 1, 0], true) && $tenant['status'] !== 'expired') {
        continue;
    }

    $url = 'https://' . $tenant['subdomain'] . '.' . $env['BASE_DOMAIN'];
    if ($tenant['status'] === 'expired' || $days <= 0) {
        $subject = 'Tài khoản đã hết hạn';
        $lead = 'Tài khoản của bạn đã hết hạn. Vui lòng gia hạn để tiếp tục sử dụng đầy đủ.';
    } else {
        $subject = 'Tài khoản sắp hết hạn trong ' . $days . ' ngày';
        $lead = 'Tài khoản của bạn sẽ hết hạn trong ' . $days . ' ngày.';
    }

    $body = '<p>' . htmlspecialchars($lead, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p>Cửa hàng: <strong>' . htmlspecialchars($tenant['shop_name'], ENT_QUOTES, 'UTF-8') . '</strong><br>'
        . 'URL: <a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a><br>'
        . 'Hạn dùng: <strong>' . htmlspecialchars($tenant['expires_at'], ENT_QUOTES, 'UTF-8') . '</strong></p>';
    if (send_mail($tenant['owner_email'], $subject, $body)) {
        $sent++;
    }
}

echo "cron_check_expired: sent={$sent}\n";
