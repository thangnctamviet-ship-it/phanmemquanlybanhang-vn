<?php
require_once __DIR__ . '/includes/db.php';
$env = env_load();
$base = $env['BASE_DOMAIN'] ?? 'quanlybanhang.shop';

$services = [];

// Master DB
$t0 = microtime(true);
try {
    $pdo = master_pdo();
    $pdo->query("SELECT 1");
    $services[] = ['name'=>'Master Database', 'ok'=>true, 'ms'=>(int)((microtime(true)-$t0)*1000), 'detail'=>'OK'];
} catch (Exception $e) {
    $services[] = ['name'=>'Master Database', 'ok'=>false, 'ms'=>0, 'detail'=>'Connection error'];
}

function http_check($url, $timeout=5) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_NOBODY => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $t0 = microtime(true);
    curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ms = (int)((microtime(true)-$t0)*1000);
    curl_close($ch);
    return ['code'=>$code, 'ms'=>$ms];
}

// Landing
$r = http_check("https://{$base}/landing/");
$services[] = ['name'=>'Landing site', 'ok'=>($r['code']>=200 && $r['code']<400), 'ms'=>$r['ms'], 'detail'=>"HTTP {$r['code']}"];

// Admin panel
$r = http_check("https://{$base}/admin-panel/login.php");
$services[] = ['name'=>'Admin Panel', 'ok'=>($r['code']>=200 && $r['code']<500), 'ms'=>$r['ms'], 'detail'=>"HTTP {$r['code']}"];

// One sample tenant
try {
    $pdo = master_pdo();
    $row = $pdo->query("SELECT subdomain FROM tenants WHERE status IN ('active','trial') ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $r = http_check("https://{$row['subdomain']}.{$base}/");
        $services[] = ['name'=>"Tenant: {$row['subdomain']}", 'ok'=>($r['code']>=200 && $r['code']<500), 'ms'=>$r['ms'], 'detail'=>"HTTP {$r['code']}"];
    }
} catch (Exception $e) {}

$all_ok = true;
foreach ($services as $s) if (!$s['ok']) { $all_ok = false; break; }

$version = 'unknown';
$git_head = dirname(__DIR__) . '/.git/HEAD';
if (file_exists($git_head)) {
    $head = trim(file_get_contents($git_head));
    if (strpos($head, 'ref: ') === 0) {
        $ref = substr($head, 5);
        $rp = dirname(__DIR__) . '/.git/' . $ref;
        if (file_exists($rp)) $version = substr(trim(file_get_contents($rp)), 0, 7);
    } else {
        $version = substr($head, 0, 7);
    }
}
$vfile = dirname(__DIR__) . '/VERSION';
if (file_exists($vfile)) $version = trim(file_get_contents($vfile));
?><!doctype html><html lang="vi"><head>
<meta charset="utf-8"><title>Trạng thái hệ thống · quanlybanhang.shop</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f8fafc;color:#1e293b;margin:0;padding:0;}
  .wrap{max-width:760px;margin:40px auto;padding:0 16px;}
  h1{font-size:24px;margin:0 0 6px;}
  .banner{padding:18px 22px;border-radius:10px;color:#fff;margin:18px 0;font-weight:600;font-size:18px;}
  .ok{background:#10b981;}
  .bad{background:#f59e0b;}
  .card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:6px 0;margin-bottom:24px;}
  .row{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f1f5f9;}
  .row:last-child{border-bottom:none;}
  .name{font-weight:500;}
  .badge{font-size:13px;padding:3px 10px;border-radius:999px;}
  .bok{background:#dcfce7;color:#166534;}
  .bbad{background:#fef3c7;color:#92400e;}
  .meta{color:#64748b;font-size:13px;margin-top:18px;text-align:center;}
  a{color:#4f46e5;text-decoration:none;}
</style></head><body>
<div class="wrap">
  <h1>Trạng thái hệ thống</h1>
  <div class="muted" style="color:#64748b;">quanlybanhang.shop</div>
  <div class="banner <?= $all_ok ? 'ok' : 'bad' ?>">
    <?= $all_ok ? '✅ Tất cả dịch vụ Operational' : '⚠️ Một số dịch vụ Degraded' ?>
  </div>
  <div class="card">
    <?php foreach ($services as $s): ?>
      <div class="row">
        <div>
          <div class="name"><?= htmlspecialchars($s['name']) ?></div>
          <div style="color:#94a3b8;font-size:12px;"><?= htmlspecialchars($s['detail']) ?> · <?= (int)$s['ms'] ?>ms</div>
        </div>
        <div class="badge <?= $s['ok']?'bok':'bbad' ?>">
          <?= $s['ok'] ? '✅ Operational' : '⚠️ Degraded' ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="meta">
    Cập nhật lúc: <?= date('Y-m-d H:i:s') ?> · Phiên bản: <code><?= htmlspecialchars($version) ?></code><br>
    <a href="/landing/">← Về trang chủ</a>
  </div>
</div>
</body></html>
