<?php
$page_title = 'Đăng nhập vào cửa hàng';
require __DIR__.'/includes/db.php';
$env = env_load();
$base_domain = $env['BASE_DOMAIN'] ?? 'quanlybanhang.shop';

$error = '';
$prefill = $_COOKIE['last_shop'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub = strtolower(trim($_POST['subdomain'] ?? ''));
    // Cho phép user dán URL đầy đủ - tự extract subdomain
    if (preg_match('~^https?://([^./]+)\.~', $sub, $m)) $sub = $m[1];
    $sub = preg_replace('/[^a-z0-9-]/', '', $sub);

    if (!$sub) {
        $error = 'Vui lòng nhập tên cửa hàng của bạn.';
    } else {
        try {
            $pdo = master_pdo();
            $stmt = $pdo->prepare("SELECT subdomain, status FROM tenants WHERE subdomain=? LIMIT 1");
            $stmt->execute([$sub]);
            $tenant = $stmt->fetch();
            if (!$tenant) {
                $error = 'Không tìm thấy cửa hàng "'.htmlspecialchars($sub).'". Bạn nhập đúng tên chưa?';
            } elseif ($tenant['status'] === 'suspended') {
                $error = 'Cửa hàng này đang bị tạm khoá. Liên hệ hỗ trợ.';
            } else {
                // Lưu cookie để lần sau auto điền
                setcookie('last_shop', $sub, time()+86400*60, '/', '.'.$base_domain);
                $url = 'https://'.$sub.'.'.$base_domain.'/auth/login';
                header('Location: '.$url);
                exit;
            }
        } catch (Exception $e) {
            $error = 'Lỗi hệ thống. Vui lòng thử lại sau.';
        }
    }
}
include __DIR__.'/includes/header.php';
?>
<section class="max-w-md mx-auto px-4 py-16">
  <div class="bg-white rounded-2xl shadow-sm border p-8">
    <h1 class="text-2xl font-bold mb-2 text-center">Đăng nhập cửa hàng</h1>
    <p class="text-slate-500 text-center mb-6 text-sm">Nhập tên cửa hàng của bạn để tiếp tục</p>

    <?php if ($error): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Tên cửa hàng</label>
        <div class="flex items-stretch border rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
          <input type="text"
                 name="subdomain"
                 value="<?= htmlspecialchars($prefill) ?>"
                 placeholder="tencuahang"
                 autofocus
                 required
                 autocomplete="off"
                 class="flex-1 px-3 py-2 outline-none text-sm">
          <span class="bg-slate-100 px-3 py-2 text-slate-500 text-sm border-l">.<?= htmlspecialchars($base_domain) ?></span>
        </div>
        <p class="text-xs text-slate-400 mt-1">Ví dụ: nếu URL là <code>dienthoaiso.<?= htmlspecialchars($base_domain) ?></code>, bạn nhập <code>dienthoaiso</code></p>
      </div>
      <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg">
        Truy cập cửa hàng →
      </button>
    </form>

    <div class="mt-4 text-center text-sm">
      <a href="forgot.php" class="text-slate-500 hover:text-indigo-600 hover:underline">Quên tên cửa hàng / mật khẩu?</a>
    </div>
    <div class="mt-4 pt-4 border-t text-center text-sm text-slate-500">
      Chưa có cửa hàng?
      <a href="register.php" class="text-indigo-600 font-medium hover:underline">Đăng ký dùng thử miễn phí</a>
    </div>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
