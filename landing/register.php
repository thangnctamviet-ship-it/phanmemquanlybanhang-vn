<?php
$page_title = 'Đăng ký';
include __DIR__.'/includes/header.php';
$selected_plan = preg_replace('/[^a-z_]/', '', $_GET['plan'] ?? 'trial');
$buy_now = !empty($_GET['buy']) && $_GET['buy'] == '1' && $selected_plan !== 'trial';

$plan_labels = [
  'monthly'    => ['name' => 'Gói tháng',    'price' => '120.000đ',   'duration' => '1 tháng'],
  'semiannual' => ['name' => 'Gói 6 tháng',  'price' => '600.000đ',   'duration' => '6 tháng'],
  'annual'     => ['name' => 'Gói năm',      'price' => '1.100.000đ', 'duration' => '12 tháng'],
];
$cur = $plan_labels[$selected_plan] ?? null;
?>
<section class="max-w-xl mx-auto px-4 py-12">
  <?php if ($buy_now && $cur): ?>
    <h1 class="text-3xl font-bold mb-2">Đăng ký &amp; thanh toán <?= htmlspecialchars($cur['name']) ?></h1>
    <p class="text-slate-600 mb-6">Tạo cửa hàng và thanh toán <strong class="text-indigo-600"><?= htmlspecialchars($cur['price']) ?></strong> qua QR code để kích hoạt <?= htmlspecialchars($cur['duration']) ?> dùng ngay (bỏ qua trial 7 ngày).</p>
    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-5 text-sm text-indigo-900">
      <div class="font-semibold mb-1">📦 Gói đã chọn: <?= htmlspecialchars($cur['name']) ?> — <?= htmlspecialchars($cur['price']) ?></div>
      <div class="text-xs">Sau khi tạo cửa hàng xong, bạn sẽ được chuyển sang trang thanh toán QR. Đăng nhập + dùng được ngay khi admin xác nhận CK (24h).</div>
      <a href="/landing/pricing.php" class="text-xs underline mt-1 inline-block">← Đổi gói khác</a>
    </div>
  <?php else: ?>
    <h1 class="text-3xl font-bold mb-2">Đăng ký dùng thử miễn phí 7 ngày</h1>
    <p class="text-slate-600 mb-6">Không cần thẻ tín dụng. Cửa hàng của bạn sẽ sẵn sàng trong vài giây.</p>
    <?php if ($cur): ?>
      <div class="bg-slate-50 border rounded-lg p-3 mb-5 text-sm text-slate-700 flex items-center justify-between gap-2">
        <span>Đang xem gói <strong><?= htmlspecialchars($cur['name']) ?></strong> (<?= htmlspecialchars($cur['price']) ?>). Bạn sẽ có 7 ngày dùng thử trước khi tính phí.</span>
        <a href="/landing/register.php?plan=<?= htmlspecialchars($selected_plan) ?>&buy=1" class="bg-indigo-600 text-white text-xs px-3 py-1.5 rounded font-medium hover:bg-indigo-700 whitespace-nowrap">Mua ngay</a>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <form id="regForm" action="/landing/provision.php" method="POST" class="bg-white p-6 rounded-xl shadow-sm space-y-4">
    <input type="hidden" name="plan" value="<?= htmlspecialchars($selected_plan) ?>">
    <input type="hidden" name="buy_now" value="<?= $buy_now ? '1' : '0' ?>">
    <div>
      <label class="block text-sm font-medium mb-1">Tên cửa hàng / thương hiệu *</label>
      <input id="shop_name" name="shop_name" required class="w-full border rounded-lg px-3 py-2" placeholder="VD: Điện Thoại Số">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Subdomain *</label>
      <div class="flex items-center">
        <input id="subdomain" name="subdomain" required class="flex-1 border rounded-l-lg px-3 py-2" placeholder="dienthoaiso">
        <span class="bg-slate-100 border border-l-0 rounded-r-lg px-3 py-2 text-slate-600">.quanlybanhang.shop</span>
      </div>
      <p id="sub_msg" class="text-xs mt-1 text-slate-500">Sẽ tự sinh từ tên cửa hàng. Bạn có thể chỉnh sửa.</p>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Email *</label>
      <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Mật khẩu *</label>
      <input type="password" name="password" required minlength="6" class="w-full border rounded-lg px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Số điện thoại</label>
      <input name="phone" class="w-full border rounded-lg px-3 py-2">
    </div>
    <button type="submit" id="submitBtn" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed">
      <?= $buy_now ? 'Tạo cửa hàng &amp; chuyển sang thanh toán →' : 'Tạo cửa hàng của tôi (Dùng thử 7 ngày)' ?>
    </button>
  </form>

  <!-- Loading overlay khi đang provision (mất 20-30s) -->
  <div id="provisionOverlay" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
      <div class="mx-auto w-16 h-16 mb-5 relative">
        <div class="absolute inset-0 rounded-full border-4 border-indigo-100"></div>
        <div class="absolute inset-0 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin"></div>
      </div>
      <h2 class="text-xl font-bold text-slate-900 mb-2">Đang khởi tạo cửa hàng của bạn...</h2>
      <p class="text-sm text-slate-500 mb-6">Vui lòng đợi 20–30 giây. Đừng tắt trình duyệt nhé!</p>
      <ul class="text-left space-y-2 text-sm">
        <li id="stepDb" class="flex items-center gap-2 text-slate-400"><span class="step-icon">⏳</span> Tạo cơ sở dữ liệu riêng</li>
        <li id="stepSchema" class="flex items-center gap-2 text-slate-400"><span class="step-icon">⏳</span> Cài đặt cấu trúc dữ liệu</li>
        <li id="stepSubdomain" class="flex items-center gap-2 text-slate-400"><span class="step-icon">⏳</span> Tạo địa chỉ cửa hàng (subdomain)</li>
        <li id="stepDeploy" class="flex items-center gap-2 text-slate-400"><span class="step-icon">⏳</span> Triển khai phần mềm</li>
        <li id="stepFinal" class="flex items-center gap-2 text-slate-400"><span class="step-icon">⏳</span> Hoàn tất & gửi email</li>
      </ul>
    </div>
  </div>
</section>

<script>
document.getElementById('regForm').addEventListener('submit', function(e){
  document.getElementById('submitBtn').disabled = true;
  document.getElementById('submitBtn').textContent = 'Đang xử lý...';
  document.getElementById('provisionOverlay').classList.remove('hidden');
  // Tiến trình UI mô phỏng (provision thật chạy ở server, ta chỉ hiển thị tiến độ ước lượng)
  var steps = ['stepDb','stepSchema','stepSubdomain','stepDeploy','stepFinal'];
  var delays = [3000, 7000, 13000, 20000, 27000];
  steps.forEach(function(id, i){
    setTimeout(function(){
      var el = document.getElementById(id);
      el.classList.remove('text-slate-400');
      el.classList.add('text-emerald-600','font-medium');
      el.querySelector('.step-icon').textContent = '✓';
    }, delays[i]);
  });
});
</script>
<script>
function slugifyVN(str) {
  // Style hiện đại: liền tù tì, không dash (Sapo/KiotViet/Shopify/Slack style)
  // VD: "Bao Cao Su Rẻ Đẹp" -> "baocaosuredep"
  str = str.toLowerCase();
  str = str.normalize('NFD').replace(/[\u0300-\u036f]/g,'');  // bỏ dấu
  str = str.replace(/đ/g,'d').replace(/Đ/g,'d');
  str = str.replace(/[^a-z0-9]/g,'');  // bỏ tất cả ký tự không phải chữ/số (kể cả space, dash)
  return str.slice(0,30);
}
const shopName = document.getElementById('shop_name');
const sub = document.getElementById('subdomain');
const msg = document.getElementById('sub_msg');
let userEdited = false;
sub.addEventListener('input', ()=>{ userEdited = true; checkSub(); });
shopName.addEventListener('input', ()=>{
  if (!userEdited) { sub.value = slugifyVN(shopName.value); checkSub(); }
});
let checkTimer;
function checkSub(){
  clearTimeout(checkTimer);
  if (sub.value.length < 3) { msg.textContent='Tối thiểu 3 ký tự'; msg.className='text-xs mt-1 text-amber-600'; return; }
  checkTimer = setTimeout(async()=>{
    const fd = new FormData(); fd.append('subdomain', sub.value);
    try {
      const r = await fetch('check_subdomain.php',{method:'POST',body:fd});
      const j = await r.json();
      msg.textContent = j.message;
      msg.className = 'text-xs mt-1 ' + (j.available?'text-emerald-600':'text-red-600');
    } catch(e){ msg.textContent='Lỗi kiểm tra'; }
  }, 350);
}
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
