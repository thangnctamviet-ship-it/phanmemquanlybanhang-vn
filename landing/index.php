<?php
require_once __DIR__.'/includes/db.php';
$_env = env_load();
$_baseDomain = $_env['BASE_DOMAIN'] ?? 'quanlybanhang.shop';
$_host = strtolower($_SERVER['HTTP_HOST'] ?? '');
$_host = preg_replace('/:\d+$/', '', $_host);
$_invalidShop = null;
// Nếu user truy cập <sub>.quanlybanhang.shop nhưng subdomain không tồn tại trong tenants
if ($_host !== $_baseDomain && $_host !== 'www.'.$_baseDomain && str_ends_with($_host, '.'.$_baseDomain)) {
    $_sub = substr($_host, 0, -strlen('.'.$_baseDomain));
    // Subdomain hợp lệ về format nhưng không có tenant → coi như "Không tìm thấy"
    if (preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', $_sub)) {
        try {
            $_st = master_pdo()->prepare("SELECT id FROM tenants WHERE subdomain=? AND status!='suspended'");
            $_st->execute([$_sub]);
            if (!$_st->fetch()) {
                $_invalidShop = $_sub;
            }
        } catch (Exception $e) { /* nếu master DB lỗi, vẫn render landing bình thường */ }
    }
}
$page_title = $_invalidShop ? 'Không tìm thấy cửa hàng' : 'Phần mềm quản lý bán hàng đa chi nhánh';
include __DIR__.'/includes/header.php';
?>

<?php if ($_invalidShop): ?>
<section class="max-w-2xl mx-auto px-4 py-16">
  <div class="bg-white rounded-2xl border shadow-sm p-8 text-center">
    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center text-3xl mb-4">⚠️</div>
    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Không tìm thấy cửa hàng</h1>
    <p class="text-red-600 font-medium mb-1">
      Tên miền <code class="bg-red-50 px-2 py-0.5 rounded"><?= htmlspecialchars($_invalidShop) ?>.<?= htmlspecialchars($_baseDomain) ?></code> không tồn tại.
    </p>
    <p class="text-slate-600 text-sm mb-6">Bạn có thể đã gõ nhầm tên cửa hàng. Hãy thử lại hoặc đăng ký cửa hàng mới.</p>
    <form method="post" action="https://<?= htmlspecialchars($_baseDomain) ?>/landing/login.php" class="text-left">
      <label class="block text-sm font-medium mb-1">Tên cửa hàng của bạn</label>
      <div class="flex items-stretch border rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
        <input type="text" name="subdomain" value="<?= htmlspecialchars($_invalidShop) ?>" autofocus
               class="flex-1 px-3 py-2 outline-none text-sm" placeholder="tencuahang">
        <span class="bg-slate-100 px-3 py-2 text-slate-500 text-sm border-l">.<?= htmlspecialchars($_baseDomain) ?></span>
      </div>
      <button class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-lg font-semibold">
        Tìm cửa hàng
      </button>
    </form>
    <div class="mt-6 pt-6 border-t text-sm space-x-3">
      <a href="https://<?= htmlspecialchars($_baseDomain) ?>/landing/forgot.php" class="text-slate-500 hover:text-indigo-600 hover:underline">Quên tên cửa hàng?</a>
      <span class="text-slate-300">·</span>
      <a href="https://<?= htmlspecialchars($_baseDomain) ?>/landing/register.php" class="text-indigo-600 font-medium hover:underline">Đăng ký cửa hàng mới</a>
    </div>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; exit; endif; ?>

<script src="https://unpkg.com/heroicons@2.1.5/24/outline/index.js" type="module"></script>
<section class="bg-white">
  <div class="max-w-6xl mx-auto px-4 py-16 grid md:grid-cols-2 gap-10 items-center">
    <div>
      <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">Quản Lý Bán Hàng</h1>
      <p class="text-lg text-slate-600 mb-8">Phần mềm SaaS cho cửa hàng Việt: bán hàng, kho, đơn hàng, chi nhánh và báo cáo trong một hệ thống riêng cho từng cửa hàng.</p>
      <div class="flex flex-wrap gap-3">
        <a href="register.php" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700">Dùng thử miễn phí 7 ngày</a>
        <a href="pricing.php" class="border border-slate-300 text-slate-700 px-6 py-3 rounded-lg font-semibold hover:bg-slate-50">Xem bảng giá</a>
      </div>
    </div>
    <div class="bg-slate-900 text-white rounded-lg p-6 shadow-sm">
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white/10 p-4 rounded"><div class="text-sm text-slate-300">Doanh thu hôm nay</div><div class="text-2xl font-bold">8.450.000đ</div></div>
        <div class="bg-white/10 p-4 rounded"><div class="text-sm text-slate-300">Đơn hàng</div><div class="text-2xl font-bold">42</div></div>
        <div class="bg-white/10 p-4 rounded"><div class="text-sm text-slate-300">Tồn kho thấp</div><div class="text-2xl font-bold">7</div></div>
        <div class="bg-white/10 p-4 rounded"><div class="text-sm text-slate-300">Chi nhánh</div><div class="text-2xl font-bold">2/5</div></div>
      </div>
    </div>
  </div>
</section>
<section class="max-w-6xl mx-auto px-4 py-14">
  <div class="grid md:grid-cols-3 gap-6">
    <?php
    $features = [
      ['📦','Quản lý kho','Theo dõi sản phẩm, tồn kho, thuộc tính và biến thể.'],
      ['🧾','Bán hàng nhanh','Tạo đơn, in hóa đơn và kiểm tra trạng thái thanh toán.'],
      ['🏬','Đa chi nhánh','Giới hạn chi nhánh theo license, mua thêm khi cần.'],
      ['📊','Báo cáo','Xem doanh thu, đơn hàng và dữ liệu vận hành.'],
      ['🔐','Tenant riêng','Mỗi cửa hàng có subdomain và database riêng.'],
      ['✉️','Thông báo email','Nhắc hạn dùng và xác nhận thanh toán tự động.'],
    ];
    foreach ($features as $f): ?>
      <div class="bg-white border rounded-lg p-5">
        <div class="text-2xl mb-3"><?= $f[0] ?></div>
        <h3 class="font-semibold text-lg mb-2"><?= $f[1] ?></h3>
        <p class="text-slate-600 text-sm"><?= $f[2] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<section class="bg-slate-100">
  <div class="max-w-6xl mx-auto px-4 py-14">
    <h2 class="text-2xl font-bold text-center mb-8">Bảng giá</h2>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="bg-white border rounded-lg p-6 flex flex-col">
        <h3 class="font-semibold text-xl">Gói tháng</h3>
        <div class="text-3xl font-bold text-indigo-600 mt-2">120.000đ</div>
        <p class="text-slate-500 mb-4">1 tháng</p>
        <a href="register.php?plan=monthly" class="mt-auto inline-block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">Đăng ký ngay</a>
      </div>
      <div class="bg-white border-2 border-indigo-500 rounded-lg p-6 flex flex-col relative">
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">Phổ biến</span>
        <h3 class="font-semibold text-xl">Gói 6 tháng</h3>
        <div class="text-3xl font-bold text-indigo-600 mt-2">600.000đ</div>
        <p class="text-emerald-600 mb-4">Tiết kiệm 17%</p>
        <a href="register.php?plan=semiannual" class="mt-auto inline-block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">Đăng ký ngay</a>
      </div>
      <div class="bg-white border rounded-lg p-6 flex flex-col">
        <h3 class="font-semibold text-xl">Gói năm</h3>
        <div class="text-3xl font-bold text-indigo-600 mt-2">1.100.000đ</div>
        <p class="text-emerald-600 mb-4">Tiết kiệm 24%</p>
        <a href="register.php?plan=annual" class="mt-auto inline-block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded">Đăng ký ngay</a>
      </div>
    </div>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
