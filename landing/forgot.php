<?php
$page_title = 'Khôi phục tài khoản';
require __DIR__.'/includes/db.php';
require __DIR__.'/includes/mailer.php';
$env = env_load();
$base_domain = $env['BASE_DOMAIN'] ?? 'quanlybanhang.shop';

$message = '';
$is_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Vui lòng nhập email hợp lệ.';
        $is_error = true;
    } else {
        try {
            $pdo = master_pdo();
            $stmt = $pdo->prepare("SELECT id, subdomain, shop_name, db_name, db_user, db_pass FROM tenants WHERE owner_email=? AND status!='suspended'");
            $stmt->execute([$email]);
            $tenants = $stmt->fetchAll();

            if (!$tenants) {
                // Vẫn báo OK để tránh leak: kẻ xấu không biết email có trong hệ thống hay không
                $message = 'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi thông tin khôi phục đến hộp thư của bạn. Vui lòng kiểm tra (kể cả thư mục Spam).';
            } else {
                $rows = '';
                foreach ($tenants as $t) {
                    $new_pass = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                    $hash = password_hash($new_pass, PASSWORD_BCRYPT);
                    // Update password admin trong DB tenant
                    try {
                        $tdsn = "mysql:host=localhost;dbname=".$t['db_name'].";charset=utf8mb4";
                        $tpdo = new PDO($tdsn, $t['db_user'], $t['db_pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
                        $tpdo->prepare("UPDATE users SET password=? WHERE email=? OR username='admin' ORDER BY (email=?) DESC LIMIT 1")
                             ->execute([$hash, $email, $email]);
                    } catch (Exception $e) {
                        // bỏ qua tenant lỗi DB, tiếp tục tenant khác
                        continue;
                    }
                    $url = 'https://'.$t['subdomain'].'.'.$base_domain.'/auth/login';
                    $rows .= '<tr>'
                        . '<td style="padding:8px;border-bottom:1px solid #eee;"><strong>'.htmlspecialchars($t['shop_name']).'</strong><br><a href="'.$url.'" style="color:#4f46e5;">'.htmlspecialchars($t['subdomain'].'.'.$base_domain).'</a></td>'
                        . '<td style="padding:8px;border-bottom:1px solid #eee;font-family:monospace;background:#fff7ed;color:#c2410c;font-weight:bold;">'.$new_pass.'</td>'
                        . '</tr>';
                }
                $body = '<p>Xin chào,</p>'
                    . '<p>Bạn đã yêu cầu khôi phục tài khoản. Dưới đây là thông tin các cửa hàng đăng ký với email này, kèm <strong>mật khẩu mới</strong>:</p>'
                    . '<table style="border-collapse:collapse;width:100%;margin:16px 0;">'
                    . '<thead><tr style="background:#f8fafc;"><th style="padding:8px;text-align:left;">Cửa hàng / URL</th><th style="padding:8px;text-align:left;">Mật khẩu mới</th></tr></thead>'
                    . '<tbody>'.$rows.'</tbody></table>'
                    . '<p>Username đăng nhập: <code>admin</code> hoặc <code>'.htmlspecialchars($email).'</code></p>'
                    . '<p style="color:#dc2626;"><strong>Lưu ý bảo mật:</strong> Vui lòng đăng nhập và đổi mật khẩu ngay sau khi nhận được email này.</p>'
                    . '<p>Nếu bạn KHÔNG yêu cầu khôi phục, hãy đăng nhập đổi mật khẩu ngay và liên hệ <a href="mailto:hotroquanlybanhang.shop@gmail.com">hotroquanlybanhang.shop@gmail.com</a></p>';
                send_mail($email, 'Khôi phục tài khoản - Quản Lý Bán Hàng', $body);
                $message = 'Đã gửi thông tin khôi phục đến <strong>'.htmlspecialchars($email).'</strong>. Vui lòng kiểm tra hộp thư (kể cả Spam) trong vài phút tới.';
            }
        } catch (Exception $e) {
            $message = 'Lỗi hệ thống. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
            $is_error = true;
        }
    }
}
include __DIR__.'/includes/header.php';
?>
<section class="max-w-md mx-auto px-4 py-16">
  <div class="bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-2xl font-bold mb-2 text-center">Quên tên cửa hàng / mật khẩu?</h1>
    <p class="text-slate-500 text-center mb-6 text-sm">Nhập email đã đăng ký, chúng tôi sẽ gửi danh sách cửa hàng & mật khẩu mới qua email.</p>

    <?php if ($message): ?>
      <div class="<?= $is_error ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' ?> border p-4 rounded-lg text-sm mb-4">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <?php if (!$message || $is_error): ?>
    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Email đăng ký</label>
        <input type="email" name="email" required autofocus
               placeholder="email@cuahang.com"
               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
      </div>
      <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg">
        Gửi thông tin khôi phục
      </button>
    </form>
    <?php endif; ?>

    <div class="mt-6 text-center text-sm text-slate-500 space-x-3">
      <a href="login.php" class="text-indigo-600 hover:underline">← Quay lại đăng nhập</a>
      <span>·</span>
      <a href="register.php" class="text-indigo-600 hover:underline">Đăng ký mới</a>
    </div>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
