<?php
require_once __DIR__.'/includes/db.php';
$_env = env_load();
$_baseDomain = $_env['BASE_DOMAIN'] ?? 'quanlybanhang.shop';
$_host = strtolower($_SERVER['HTTP_HOST'] ?? '');
$_host = preg_replace('/:\d+$/', '', $_host);
$_invalidShop = null;
// Nếu user truy cập <sub>.quanlybanhang.shop nhưng subdomain không tồn tại trong tenants
$_suffix = '.'.$_baseDomain;
if ($_host !== $_baseDomain && $_host !== 'www.'.$_baseDomain
    && strlen($_host) > strlen($_suffix)
    && substr($_host, -strlen($_suffix)) === $_suffix) {
    $_sub = substr($_host, 0, -strlen($_suffix));
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

<style>
  .reveal{opacity:0;transform:translateY(24px);transition:opacity .6s ease,transform .6s ease}
  .reveal.in{opacity:1;transform:none}
  .hero-blob{background:radial-gradient(1200px 500px at 80% -10%,rgba(255,255,255,.18),transparent 60%)}
  .browser-bar{background:#0f172a}
</style>

<!-- ============ HERO ============ -->
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-700 text-white">
  <div class="hero-blob absolute inset-0"></div>
  <div class="relative max-w-6xl mx-auto px-4 pt-16 pb-20 grid md:grid-cols-2 gap-12 items-center">
    <div>
      <span class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-3 py-1.5 rounded-full text-sm font-medium mb-5">
        🧾 Có sẵn Sổ thuế S1-HKD — sẵn sàng cho Nghị định 70/2025
      </span>
      <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-5">
        Phần mềm bán hàng<br>cho <span class="text-amber-300">hộ kinh doanh &amp; shop nhỏ</span>
      </h1>
      <p class="text-lg text-indigo-100 mb-8 max-w-lg">
        Bán hàng nhanh, quản lý kho, QR chuyển khoản, chốt ca tiền mặt và sổ sách thuế — gọn nhẹ, dễ dùng, giá chỉ từ <b class="text-white">120.000đ/tháng</b>.
      </p>
      <div class="flex flex-wrap gap-3">
        <a href="/landing/register.php" class="bg-white text-indigo-700 px-7 py-3.5 rounded-xl font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition">Dùng thử miễn phí 14 ngày →</a>
        <a href="#demo" class="bg-white/10 border border-white/30 text-white px-7 py-3.5 rounded-xl font-semibold hover:bg-white/20 transition">▶ Xem demo</a>
      </div>
      <p class="mt-5 text-sm text-indigo-200 flex flex-wrap gap-x-5 gap-y-1">
        <span>✓ Không cần thẻ tín dụng</span>
        <span>✓ Cài đặt trong 5 phút</span>
        <span>✓ Hỗ trợ tiếng Việt</span>
      </p>
    </div>
    <div class="reveal">
      <div class="rounded-2xl shadow-2xl overflow-hidden ring-1 ring-white/20">
        <div class="browser-bar flex items-center gap-1.5 px-4 py-2.5">
          <span class="w-3 h-3 rounded-full bg-red-400"></span>
          <span class="w-3 h-3 rounded-full bg-amber-400"></span>
          <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
          <span class="ml-3 text-xs text-slate-400">cuahang.quanlybanhang.shop</span>
        </div>
        <img src="/landing/media/shot-dashboard.jpg" alt="Bảng điều khiển" class="w-full block">
      </div>
    </div>
  </div>
</section>

<!-- ============ TRUST BAR ============ -->
<section class="bg-white border-b">
  <div class="max-w-6xl mx-auto px-4 py-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
    <div><div class="text-2xl font-extrabold text-indigo-600">QR</div><div class="text-sm text-slate-500">Chuyển khoản mọi ngân hàng</div></div>
    <div><div class="text-2xl font-extrabold text-indigo-600">S1-HKD</div><div class="text-sm text-slate-500">Sổ thuế Thông tư 88</div></div>
    <div><div class="text-2xl font-extrabold text-indigo-600">∞</div><div class="text-sm text-slate-500">Đa chi nhánh</div></div>
    <div><div class="text-2xl font-extrabold text-indigo-600">14 ngày</div><div class="text-sm text-slate-500">Dùng thử miễn phí</div></div>
  </div>
</section>

<!-- ============ DEMO VIDEO ============ -->
<section id="demo" class="bg-slate-50 border-b border-slate-100">
  <div class="max-w-4xl mx-auto px-4 py-16 text-center reveal">
    <h2 class="text-3xl font-bold text-slate-900 mb-2">Xem demo trong 35 giây</h2>
    <p class="text-slate-600 mb-8">Bán hàng · QR chuyển khoản · sổ thuế S1-HKD · chốt ca tiền mặt — tất cả trong một phần mềm.</p>
    <div class="rounded-2xl overflow-hidden shadow-2xl bg-black ring-1 ring-slate-200">
      <video src="/landing/media/demo.mp4" controls preload="metadata" playsinline poster="/landing/media/demo-poster.jpg" class="w-full h-auto block"></video>
    </div>
    <div class="mt-8">
      <a href="/landing/register.php" class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-bold shadow hover:bg-indigo-700 inline-block transition">Bắt đầu dùng thử miễn phí</a>
    </div>
  </div>
</section>

<!-- ============ KILLER FEATURES (ảnh thật) ============ -->
<section class="max-w-6xl mx-auto px-4 py-20 space-y-20">
  <!-- 1: POS + QR -->
  <div class="grid md:grid-cols-2 gap-10 items-center reveal">
    <div class="rounded-2xl overflow-hidden shadow-xl ring-1 ring-slate-200 order-2 md:order-1">
      <img src="/landing/media/shot-pos-qr.jpg" alt="POS và QR chuyển khoản" class="w-full block">
    </div>
    <div class="order-1 md:order-2">
      <span class="text-indigo-600 font-semibold">BÁN HÀNG</span>
      <h3 class="text-2xl md:text-3xl font-bold mt-2 mb-3">Bán nhanh + QR chuyển khoản tự động</h3>
      <p class="text-slate-600 mb-4">Quét mã vạch, in bill K80, phím tắt tiện lợi. Khách chuyển khoản? Hệ thống hiện <b>mã QR đúng số tiền</b> để khách quét — không cần gõ tay, không nhầm số.</p>
      <ul class="space-y-2 text-slate-700">
        <li>✅ Quét barcode, tìm nhanh sản phẩm</li>
        <li>✅ QR VietQR động theo từng đơn</li>
        <li>✅ Tách nguồn tiền: tiền mặt / chuyển khoản</li>
      </ul>
    </div>
  </div>
  <!-- 2: S1-HKD -->
  <div class="grid md:grid-cols-2 gap-10 items-center reveal">
    <div>
      <span class="text-emerald-600 font-semibold">THUẾ</span>
      <h3 class="text-2xl md:text-3xl font-bold mt-2 mb-3">Sổ thuế S1-HKD — xuất Excel 1 chạm</h3>
      <p class="text-slate-600 mb-4">Sổ chi tiết doanh thu theo đúng mẫu <b>Thông tư 88/2021/TT-BTC</b>. Gộp theo ngày, chọn cửa hàng, xuất file nộp cơ quan thuế — sẵn sàng cho Nghị định 70/2025.</p>
      <ul class="space-y-2 text-slate-700">
        <li>✅ Đúng mẫu sổ S1-HKD</li>
        <li>✅ Gộp doanh thu theo ngày</li>
        <li>✅ Xuất Excel, chọn kỳ &amp; cửa hàng</li>
      </ul>
    </div>
    <div class="rounded-2xl overflow-hidden shadow-xl ring-1 ring-slate-200">
      <img src="/landing/media/shot-s1hkd.jpg" alt="Sổ thuế S1-HKD" class="w-full block">
    </div>
  </div>
  <!-- 3: Dashboard -->
  <div class="grid md:grid-cols-2 gap-10 items-center reveal">
    <div class="rounded-2xl overflow-hidden shadow-xl ring-1 ring-slate-200 order-2 md:order-1">
      <img src="/landing/media/shot-pos.jpg" alt="Màn hình bán hàng POS" class="w-full block">
    </div>
    <div class="order-1 md:order-2">
      <span class="text-violet-600 font-semibold">QUẢN LÝ</span>
      <h3 class="text-2xl md:text-3xl font-bold mt-2 mb-3">Nắm cửa hàng trong lòng bàn tay</h3>
      <p class="text-slate-600 mb-4">Doanh thu, đơn hàng, tồn kho, công nợ, top bán chạy — cập nhật real-time. Có nhân viên? <b>Chốt ca tiền mặt</b> chống thất thoát: đếm đầu ca, cuối ca, hiện chênh lệch.</p>
      <ul class="space-y-2 text-slate-700">
        <li>✅ Báo cáo doanh thu &amp; lợi nhuận</li>
        <li>✅ Chốt ca / kết ca tiền mặt</li>
        <li>✅ Cảnh báo sắp hết hàng, công nợ</li>
      </ul>
    </div>
  </div>
</section>

<!-- ============ FEATURE GRID ============ -->
<section class="bg-slate-50 border-y">
  <div class="max-w-6xl mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center mb-3">Đầy đủ mọi thứ shop cần</h2>
    <p class="text-center text-slate-500 mb-10">Một phần mềm — thay cho sổ sách, máy tính tiền và file Excel rời rạc.</p>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
      <?php
      $features = [
        ['📦','Quản lý kho','Sản phẩm, tồn kho, nhập hàng, chuyển kho, cảnh báo hết hàng.'],
        ['🧾','Bán hàng POS','Quét mã, in bill K80, phím tắt, tách nguồn tiền.'],
        ['🏬','Đa chi nhánh','Quản lý nhiều cửa hàng, mỗi nơi 1 tài khoản riêng.'],
        ['📊','Báo cáo &amp; lợi nhuận','Doanh thu theo kỳ, theo nhân viên, theo nguồn tiền.'],
        ['👥','Khách hàng &amp; công nợ','Lưu khách quen, theo dõi phải thu / phải trả.'],
        ['⬆️','Nhập liệu bằng Excel','Chuyển từ phần mềm cũ sang chỉ trong vài phút.'],
      ];
      foreach ($features as $f): ?>
        <div class="bg-white border rounded-xl p-5 hover:shadow-lg hover:-translate-y-0.5 transition reveal">
          <div class="text-3xl mb-3"><?= $f[0] ?></div>
          <h3 class="font-semibold text-lg mb-1"><?= $f[1] ?></h3>
          <p class="text-slate-600 text-sm"><?= $f[2] ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ 3 BƯỚC ============ -->
<section class="max-w-6xl mx-auto px-4 py-16">
  <h2 class="text-3xl font-bold text-center mb-12">Bắt đầu trong 3 bước</h2>
  <div class="grid md:grid-cols-3 gap-8">
    <div class="text-center reveal">
      <div class="w-14 h-14 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-4">1</div>
      <h3 class="font-semibold text-lg mb-2">Đăng ký</h3>
      <p class="text-slate-600 text-sm">Nhập email + tên cửa hàng, có ngay hệ thống riêng. Miễn phí 14 ngày.</p>
    </div>
    <div class="text-center reveal">
      <div class="w-14 h-14 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-4">2</div>
      <h3 class="font-semibold text-lg mb-2">Nhập hàng hoá</h3>
      <p class="text-slate-600 text-sm">Thêm sản phẩm tay hoặc nhập hàng loạt bằng Excel từ phần mềm cũ.</p>
    </div>
    <div class="text-center reveal">
      <div class="w-14 h-14 mx-auto rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-4">3</div>
      <h3 class="font-semibold text-lg mb-2">Bán hàng</h3>
      <p class="text-slate-600 text-sm">Mở POS, bán ngay. Doanh thu, sổ thuế, báo cáo tự động lên số.</p>
    </div>
  </div>
</section>

<!-- ============ PRICING ============ -->
<section class="bg-slate-100">
  <div class="max-w-6xl mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-center mb-2">Bảng giá đơn giản, minh bạch</h2>
    <p class="text-center text-slate-500 mb-10">Dùng thử 14 ngày miễn phí — không cần thẻ tín dụng.</p>
    <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
      <div class="bg-white border rounded-2xl p-6 flex flex-col">
        <h3 class="font-semibold text-xl">Gói tháng</h3>
        <div class="text-3xl font-extrabold text-indigo-600 mt-2">120.000đ</div>
        <p class="text-slate-500 mb-4">mỗi tháng</p>
        <a href="/landing/register.php?plan=monthly" class="mt-auto text-center bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-lg transition">Đăng ký</a>
      </div>
      <div class="bg-white border-2 border-indigo-500 rounded-2xl p-6 flex flex-col relative shadow-xl md:-mt-3">
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-xs px-3 py-1 rounded-full font-medium">Phổ biến nhất</span>
        <h3 class="font-semibold text-xl">Gói 6 tháng</h3>
        <div class="text-3xl font-extrabold text-indigo-600 mt-2">600.000đ</div>
        <p class="text-emerald-600 font-medium mb-4">Tiết kiệm 17%</p>
        <a href="/landing/register.php?plan=semiannual" class="mt-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg transition">Đăng ký ngay</a>
      </div>
      <div class="bg-white border rounded-2xl p-6 flex flex-col">
        <h3 class="font-semibold text-xl">Gói năm</h3>
        <div class="text-3xl font-extrabold text-indigo-600 mt-2">1.100.000đ</div>
        <p class="text-emerald-600 font-medium mb-4">Tiết kiệm 24%</p>
        <a href="/landing/register.php?plan=annual" class="mt-auto text-center bg-slate-900 hover:bg-slate-800 text-white font-semibold py-2.5 px-4 rounded-lg transition">Đăng ký</a>
      </div>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="max-w-3xl mx-auto px-4 py-16">
  <h2 class="text-3xl font-bold text-center mb-8">Câu hỏi thường gặp</h2>
  <div class="space-y-3">
    <?php
    $faqs = [
      ['Dùng thử có mất phí không?','Không. Bạn đăng ký bằng email + tên cửa hàng là dùng ngay 14 ngày miễn phí, không cần thẻ tín dụng.'],
      ['Tôi đang dùng phần mềm khác, chuyển sang có khó không?','Rất dễ — bạn tải file Excel mẫu, dán dữ liệu sản phẩm/khách hàng vào rồi upload. Toàn bộ lên hệ thống trong vài phút.'],
      ['Phần mềm có xuất được sổ thuế không?','Có. Hệ thống xuất sẵn Sổ chi tiết doanh thu S1-HKD theo Thông tư 88/2021/TT-BTC để nộp cơ quan thuế.'],
      ['Có dùng trên điện thoại được không?','Được. Giao diện tối ưu cho điện thoại, có thể cài như một ứng dụng (PWA) trên màn hình chính.'],
      ['QR chuyển khoản hỗ trợ ngân hàng nào?','Hầu hết ngân hàng phổ biến (VietQR): Vietcombank, Techcombank, BIDV, MB, VietinBank, ACB, VPBank, Sacombank, TPBank...'],
    ];
    foreach ($faqs as $q): ?>
      <details class="group bg-white border rounded-xl px-5 py-4">
        <summary class="flex justify-between items-center cursor-pointer font-semibold text-slate-800 list-none">
          <span><?= $q[0] ?></span>
          <span class="text-indigo-500 group-open:rotate-45 transition text-xl leading-none">+</span>
        </summary>
        <p class="text-slate-600 text-sm mt-3"><?= $q[1] ?></p>
      </details>
    <?php endforeach; ?>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="bg-gradient-to-br from-indigo-600 to-violet-700 text-white">
  <div class="max-w-4xl mx-auto px-4 py-16 text-center reveal">
    <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Sẵn sàng quản lý cửa hàng dễ hơn?</h2>
    <p class="text-indigo-100 text-lg mb-8">Tạo cửa hàng của bạn ngay hôm nay — dùng thử miễn phí 14 ngày, không cần thẻ.</p>
    <a href="/landing/register.php" class="bg-white text-indigo-700 px-8 py-4 rounded-xl font-bold text-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition inline-block">Dùng thử miễn phí 14 ngày →</a>
  </div>
</section>

<script>
// Fade-in khi cuộn tới
(function(){
  var els=document.querySelectorAll('.reveal');
  if(!('IntersectionObserver' in window)){els.forEach(function(e){e.classList.add('in')});return;}
  var io=new IntersectionObserver(function(es){es.forEach(function(en){if(en.isIntersecting){en.target.classList.add('in');io.unobserve(en.target);}})},{threshold:.15});
  els.forEach(function(e){io.observe(e)});
})();
</script>
<?php include __DIR__.'/includes/footer.php'; ?>