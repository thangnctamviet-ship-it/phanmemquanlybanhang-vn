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

// Migration: add last_warning_sent column if not exists
try {
    $col = $pdo->query("SHOW COLUMNS FROM tenants LIKE 'last_warning_sent'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE tenants ADD COLUMN last_warning_sent VARCHAR(32) NULL");
    }
} catch (Exception $e) {
    fwrite(STDERR, "Migration warn: ".$e->getMessage()."\n");
}

// Mark expired
$pdo->exec("UPDATE tenants SET status='expired' WHERE status IN ('trial','active') AND expires_at < NOW()");

$stmt = $pdo->query("SELECT * FROM tenants WHERE status IN ('trial','active','expired') ORDER BY expires_at ASC");
$sent = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $tenant) {
    $expires = strtotime($tenant['expires_at']);
    $now = time();
    $diff = $expires - $now;
    $days = (int)floor($diff / 86400);

    $kind = null;
    if ($tenant['status'] === 'expired' && $diff > -86400) {
        $kind = 'expired';
        $subject = '[Quản lý bán hàng] Tài khoản đã hết hạn';
        $lead = 'Tài khoản của bạn đã hết hạn. Vui lòng gia hạn để mở khoá đầy đủ tính năng.';
    } elseif ($diff > 0 && $diff <= 86400) {
        $kind = '1day';
        $subject = '[Quản lý bán hàng] Chỉ còn 1 ngày!';
        $lead = 'Tài khoản của bạn sẽ hết hạn trong vòng 1 ngày. Hãy gia hạn ngay để tránh gián đoạn.';
    } elseif ($diff > 86400 && $diff <= 3*86400) {
        $kind = '3day';
        $subject = '[Quản lý bán hàng] Còn 3 ngày, gia hạn sớm';
        $lead = 'Tài khoản của bạn sẽ hết hạn trong 2-3 ngày tới. Gia hạn sớm để không bị gián đoạn.';
    }

    if (!$kind) continue;
    if (($tenant['last_warning_sent'] ?? '') === $kind) continue;

    $url = 'https://' . $tenant['subdomain'] . '.' . $env['BASE_DOMAIN'] . '/license';
    $body = '<p>' . htmlspecialchars($lead, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p>Cửa hàng: <strong>' . htmlspecialchars($tenant['shop_name'], ENT_QUOTES, 'UTF-8') . '</strong><br>'
        . 'Hạn dùng: <strong>' . htmlspecialchars($tenant['expires_at'], ENT_QUOTES, 'UTF-8') . '</strong><br>'
        . 'Gia hạn: <a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a></p>';

    if (send_mail($tenant['owner_email'], $subject, $body)) {
        $sent++;
        $upd = $pdo->prepare("UPDATE tenants SET last_warning_sent=? WHERE id=?");
        $upd->execute([$kind, $tenant['id']]);
    }
}

echo "cron_check_expired: sent={$sent}\n";
