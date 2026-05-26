<?php
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/mailer.php';
$page_title = 'Đang khởi tạo cửa hàng...';
$env = env_load();

$shop = trim($_POST['shop_name'] ?? '');
$sub  = strtolower(trim($_POST['subdomain'] ?? ''));
$email= trim($_POST['email'] ?? '');
$pass = $_POST['password'] ?? '';
$phone= trim($_POST['phone'] ?? '');

function fail($msg) {
    include __DIR__.'/includes/header.php';
    echo '<div class="max-w-xl mx-auto px-4 py-12"><div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg">'.htmlspecialchars($msg).'</div><p class="mt-4"><a href="register.php" class="text-indigo-600">← Quay lại</a></p></div>';
    include __DIR__.'/includes/footer.php';
    exit;
}

if (!$shop || !$sub || !$email || !$pass) fail('Vui lòng điền đầy đủ thông tin.');
if ($err = validate_subdomain($sub)) fail($err);

try {
    $pdo = master_pdo();
    $stmt = $pdo->prepare("SELECT id FROM tenants WHERE subdomain=?");
    $stmt->execute([$sub]);
    if ($stmt->fetch()) fail('Subdomain đã được sử dụng. Vui lòng chọn tên khác.');

    $now = date('Y-m-d H:i:s');
    $exp = date('Y-m-d H:i:s', strtotime('+7 days'));
    $db_name = "tenant_$sub";
    $stmt = $pdo->prepare("INSERT INTO tenants (subdomain, shop_name, owner_email, db_name, db_user, db_pass, status, plan, paid_branches, trial_started_at, expires_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$sub, $shop, $email, $db_name, "t_$sub", '', 'pending_provision', 'trial', 2, $now, $exp]);
    $tenant_id = $pdo->lastInsertId();
} catch (Exception $e) {
    fail('Lỗi DB chủ: '.$e->getMessage());
}

if (($env['MODE'] ?? 'local') === 'cpanel') {
    try {
        require __DIR__.'/includes/provisioner.php';
        $json = provision_tenant_cpanel([
            'subdomain' => $sub,
            'shop_name' => $shop,
            'email' => $email,
            'password' => $pass,
            'phone' => $phone,
        ]);
        $pdo->prepare("UPDATE tenants SET status='trial', db_name=?, db_user=?, db_pass=? WHERE id=?")
            ->execute([$json['db_name'], $json['db_user'], $json['db_pass'] ?? '', $tenant_id]);
    } catch (Exception $e) {
        $pdo->prepare("DELETE FROM tenants WHERE id=?")->execute([$tenant_id]);
        fail('Không thể khởi tạo tenant: '.$e->getMessage());
    }
} else {
    // LƯU Ý: cần script chạy được bash + docker exec. Trên cPanel dùng nhánh PHP ở trên.
    $root = dirname(__DIR__);
    $script = "$root/scripts/provision_tenant.sh";
    $cmd = sprintf('bash %s %s %s %s %s 2>&1',
        escapeshellarg($script),
        escapeshellarg($sub),
        escapeshellarg($shop),
        escapeshellarg($email),
        escapeshellarg($pass)
    );
    $output = shell_exec($cmd);

    // Parse JSON output từ provision script (lấy dòng JSON cuối)
    $json = null;
    foreach (array_reverse(explode("\n", trim((string)$output))) as $line) {
        $line = trim($line);
        if ($line && $line[0] === '{') { $json = json_decode($line, true); break; }
    }

    if (!$json || ($json['status'] ?? '') !== 'ok') {
        // Rollback tenant record
        $pdo->prepare("UPDATE tenants SET status='suspended' WHERE id=?")->execute([$tenant_id]);
        fail("Không thể khởi tạo tenant. Output:\n" . substr((string)$output, 0, 2000));
    }

    // Update status → trial
    $pdo->prepare("UPDATE tenants SET status='trial' WHERE id=?")->execute([$tenant_id]);
}

$tenant_url = 'https://'.$sub.'.'.($env['BASE_DOMAIN'] ?? 'quanlybanhang.shop');
if (($env['MODE'] ?? 'local') === 'local') {
    $tenant_url = $json['url'] ?? ('http://'.$sub.'.'.($env['LOCAL_BASE_DOMAIN'] ?? 'localhost:8080'));
}

$customer_body = '<p>Xin chào,</p>'
    . '<p>Cửa hàng <strong>'.htmlspecialchars($shop, ENT_QUOTES, 'UTF-8').'</strong> đã được khởi tạo.</p>'
    . '<p>URL: <a href="'.htmlspecialchars($tenant_url, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($tenant_url, ENT_QUOTES, 'UTF-8').'</a></p>'
    . '<p>Email đăng nhập: <strong>'.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</strong><br>Username: <strong>admin</strong></p>'
    . '<p>Vui lòng đổi mật khẩu sau lần đăng nhập đầu tiên. Nếu quên mật khẩu, liên hệ chủ hệ thống để được reset.</p>';
send_mail($email, 'Cửa hàng của bạn đã sẵn sàng', $customer_body);

if (!empty($env['OWNER_EMAIL'])) {
    $owner_body = '<p>Có đăng ký mới:</p>'
        . '<ul>'
        . '<li>Cửa hàng: '.htmlspecialchars($shop, ENT_QUOTES, 'UTF-8').'</li>'
        . '<li>Subdomain: '.htmlspecialchars($sub, ENT_QUOTES, 'UTF-8').'</li>'
        . '<li>Email: '.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</li>'
        . '<li>URL: <a href="'.htmlspecialchars($tenant_url, ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($tenant_url, ENT_QUOTES, 'UTF-8').'</a></li>'
        . '</ul>';
    send_mail($env['OWNER_EMAIL'], 'Đăng ký tenant mới: '.$sub, $owner_body);
}

include __DIR__.'/includes/header.php';
?>
<section class="max-w-xl mx-auto px-4 py-12">
  <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-xl">
    <h1 class="text-2xl font-bold text-emerald-700 mb-2">🎉 Cửa hàng đã sẵn sàng!</h1>
    <p class="mb-4">Cửa hàng <strong><?= htmlspecialchars($shop) ?></strong> đã được tạo. Bạn đang dùng thử <strong>7 ngày miễn phí</strong>.</p>
    <div class="bg-white p-4 rounded-lg border space-y-2 text-sm">
      <div><span class="text-slate-500">URL:</span> <a href="<?= htmlspecialchars($tenant_url) ?>" class="text-indigo-600 font-mono"><?= htmlspecialchars($tenant_url) ?></a></div>
      <div><span class="text-slate-500">Email đăng nhập:</span> <code><?= htmlspecialchars($email) ?></code></div>
      <div><span class="text-slate-500">Username:</span> <code>admin</code></div>
      <div><span class="text-slate-500">Mật khẩu:</span> <code><?= htmlspecialchars($pass) ?></code></div>
    </div>
    <a href="<?= htmlspecialchars($tenant_url) ?>" class="mt-6 inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">Đi đến cửa hàng của tôi →</a>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
