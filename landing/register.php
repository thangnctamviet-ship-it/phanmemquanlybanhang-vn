<?php $page_title = 'Đăng ký dùng thử'; include __DIR__.'/includes/header.php'; $selected_plan = preg_replace('/[^a-z_]/', '', $_GET['plan'] ?? 'trial'); ?>
<section class="max-w-xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold mb-2">Đăng ký dùng thử miễn phí 7 ngày</h1>
  <p class="text-slate-600 mb-6">Không cần thẻ tín dụng. Cửa hàng của bạn sẽ sẵn sàng trong vài giây.</p>
  <form id="regForm" action="provision.php" method="POST" class="bg-white p-6 rounded-xl shadow-sm space-y-4">
    <input type="hidden" name="plan" value="<?= htmlspecialchars($selected_plan) ?>">
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
    <button type="submit" id="submitBtn" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700">Tạo cửa hàng của tôi</button>
  </form>
</section>
<script>
function slugifyVN(str) {
  str = str.toLowerCase();
  str = str.normalize('NFD').replace(/[\u0300-\u036f]/g,'');
  str = str.replace(/đ/g,'d').replace(/Đ/g,'d');
  str = str.replace(/[^a-z0-9\s-]/g,'').trim();
  str = str.replace(/\s+/g,'-').replace(/-+/g,'-');
  str = str.replace(/^-+|-+$/g,'');
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
