<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>POS - Bán hàng nhanh | <?= htmlspecialchars($company['company_name'] ?? 'QLBH') ?></title>
<link rel="stylesheet" href="<?= base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') ?>">
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; height: 100%; font-family: -apple-system, "Segoe UI", Inter, Tahoma, Arial, sans-serif; background: #f1f5f9; color: #0f172a; }
  body { display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

  /* Top bar */
  .pos-top { display: flex; align-items: center; gap: 12px; padding: 10px 16px; background: #fff; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,.04); flex: 0 0 auto; }
  .pos-top .logo { font-weight: 700; font-size: 16px; color: #4f46e5; }
  .pos-top .pos-store { display: flex; align-items: center; gap: 6px; }
  .pos-top select { padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-size: 13px; }
  .pos-top .spacer { flex: 1; }
  .pos-top a { color: #64748b; text-decoration: none; font-size: 13px; padding: 6px 12px; border-radius: 6px; }
  .pos-top a:hover { background: #f1f5f9; color: #0f172a; }

  /* Main 2 columns */
  .pos-main { display: flex; flex: 1 1 auto; min-height: 0; }
  .pos-left { flex: 1 1 60%; display: flex; flex-direction: column; padding: 14px; gap: 12px; min-width: 0; }
  .pos-right { flex: 0 0 380px; background: #fff; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column; }

  /* Search row */
  .pos-search { display: flex; gap: 8px; }
  .pos-search input { flex: 1; padding: 12px 14px; border: 2px solid #6366f1; border-radius: 10px; font-size: 16px; outline: none; }
  .pos-search input::placeholder { color: #94a3b8; }
  .pos-search input:focus { border-color: #4338ca; box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
  .pos-search .hint { font-size: 11px; color: #64748b; margin-top: 4px; }

  /* Product grid */
  .pos-grid-wrap { flex: 1 1 auto; overflow-y: auto; background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px; }
  .pos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
  .pos-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; cursor: pointer; transition: all .15s; background: #fafafa; user-select: none; }
  .pos-card:hover { border-color: #6366f1; background: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(99,102,241,.15); }
  .pos-card .name { font-weight: 600; font-size: 13px; margin-bottom: 4px; min-height: 36px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .pos-card .sku { font-size: 11px; color: #94a3b8; margin-bottom: 6px; }
  .pos-card .price { font-weight: 700; color: #059669; font-size: 14px; }
  .pos-card .stock { font-size: 11px; color: #64748b; margin-top: 2px; }
  .pos-card.out { opacity: .5; cursor: not-allowed; }
  .pos-empty { text-align: center; color: #94a3b8; padding: 40px 20px; font-size: 14px; }

  /* Cart */
  .cart-head { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
  .cart-head h2 { margin: 0; font-size: 16px; }
  .cart-head .count { background: #6366f1; color: #fff; font-size: 12px; padding: 2px 8px; border-radius: 10px; }
  .cart-body { flex: 1 1 auto; overflow-y: auto; padding: 8px 0; }
  .cart-empty { text-align: center; color: #94a3b8; padding: 40px 20px; }
  .cart-item { display: flex; padding: 10px 16px; border-bottom: 1px solid #f1f5f9; align-items: center; gap: 8px; }
  .cart-item .info { flex: 1; min-width: 0; }
  .cart-item .info .nm { font-weight: 600; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .cart-item .info .pr { font-size: 12px; color: #64748b; }
  .cart-item .qty-ctrl { display: flex; align-items: center; gap: 4px; }
  .cart-item .qty-ctrl button { width: 26px; height: 26px; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer; font-weight: 700; }
  .cart-item .qty-ctrl button:hover { background: #f1f5f9; }
  .cart-item .qty-ctrl input { width: 38px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; font-size: 13px; }
  .cart-item .amt { font-weight: 700; min-width: 80px; text-align: right; font-size: 13px; }
  .cart-item .rm { color: #ef4444; cursor: pointer; padding: 4px; }

  .cart-foot { border-top: 1px solid #e2e8f0; padding: 14px 16px; background: #f8fafc; }
  .cart-foot .row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
  .cart-foot .row.total { font-size: 18px; font-weight: 700; color: #059669; margin: 8px 0; padding-top: 8px; border-top: 1px dashed #cbd5e1; }
  .cart-foot input[type="number"], .cart-foot input[type="text"] { padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; width: 110px; text-align: right; font-size: 13px; }
  .cart-foot input.cust { width: 100%; text-align: left; margin-bottom: 6px; }
  .pay-method { display: inline-flex; gap: 6px; }
  .pm-btn { padding: 5px 10px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; font-size: 12px; cursor: pointer; color: #475569; }
  .pm-btn.active { background: #059669; color: #fff; border-color: #059669; font-weight: 600; }
  .btn-pay { width: 100%; padding: 14px; background: #059669; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 8px; }
  .btn-pay:hover { background: #047857; }
  .btn-pay:disabled { background: #94a3b8; cursor: not-allowed; }
  .btn-clear { width: 100%; padding: 8px; background: transparent; color: #ef4444; border: 1px solid #fca5a5; border-radius: 8px; font-size: 13px; cursor: pointer; margin-top: 6px; }

  .toast { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #0f172a; color: #fff; padding: 10px 18px; border-radius: 8px; font-size: 13px; opacity: 0; transition: opacity .2s; z-index: 999; }
  .toast.show { opacity: 1; }
  .toast.err { background: #dc2626; }

  /* Responsive */
  @media (max-width: 900px) {
    .pos-main { flex-direction: column; }
    .pos-right { flex: 1 1 auto; border-left: none; border-top: 1px solid #e2e8f0; max-height: 50vh; }
  }
</style>
</head>
<body>

<div class="pos-top">
  <div class="logo">⚡ POS</div>
  <div class="pos-store">
    <i class="fa fa-building-o" style="color:#94a3b8;"></i>
    <select id="storeSelect">
      <?php if (!empty($stores)): ?>
        <?php foreach ($stores as $s): if (empty($s['active'])) continue; ?>
          <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
        <?php endforeach; ?>
      <?php else: ?>
        <option value="0">— Chưa có cửa hàng —</option>
      <?php endif; ?>
    </select>
  </div>
  <div class="spacer"></div>
  <span style="color:#64748b;font-size:12px;">F2 = Tìm · F9 = Thanh toán · ESC = Hủy</span>
  <a href="<?= base_url('dashboard') ?>"><i class="fa fa-arrow-left"></i> Thoát POS</a>
</div>

<div class="pos-main">
  <!-- LEFT: search + grid -->
  <div class="pos-left">
    <div>
      <div class="pos-search">
        <input id="searchBox" type="text" placeholder="Quét mã vạch hoặc tìm theo tên / SKU..." autofocus autocomplete="off">
      </div>
      <div class="hint">Quét barcode (kết thúc bằng Enter) hoặc gõ tên để lọc danh sách bên dưới.</div>
    </div>

    <div class="pos-grid-wrap">
      <div id="productGrid" class="pos-grid"></div>
      <div id="emptyState" class="pos-empty" style="display:none;">Không tìm thấy sản phẩm.</div>
    </div>
  </div>

  <!-- RIGHT: cart -->
  <div class="pos-right">
    <div class="cart-head">
      <h2><i class="fa fa-shopping-cart"></i> Giỏ hàng <span class="count" id="cartCount">0</span></h2>
      <a href="#" id="clearBtn" style="font-size:12px;color:#ef4444;text-decoration:none;">Xóa hết</a>
    </div>
    <div class="cart-body" id="cartBody">
      <div class="cart-empty">Chưa có sản phẩm nào.<br><small>Quét barcode hoặc click sản phẩm bên trái.</small></div>
    </div>
    <div class="cart-foot">
      <input class="cust" type="text" id="custPhone" placeholder="SĐT khách (tự tìm KH)" autocomplete="off">
      <input class="cust" type="text" id="custName" placeholder="Tên khách hàng">
      <div id="custInfo" style="font-size:11px;color:#059669;margin:-4px 0 6px;display:none;"></div>
      <div class="row"><span>Tạm tính:</span> <span id="grossLabel">0đ</span></div>
      <div class="row"><span>Giảm giá:</span> <input type="number" id="discountInput" value="0" min="0"></div>
      <div class="row total"><span>TỔNG:</span> <span id="netLabel">0đ</span></div>
      <div class="row" style="align-items:center;">
        <span>Nguồn tiền:</span>
        <span class="pay-method">
          <button type="button" class="pm-btn active" data-method="cash">💵 Tiền mặt</button>
          <button type="button" class="pm-btn" data-method="bank">🏦 Chuyển khoản</button>
        </span>
      </div>
      <div class="row"><span>Tiền khách trả:</span> <input type="number" id="paidInput" value="0" min="0" placeholder="Để trống = trả đủ"></div>
      <div class="row"><span style="font-size:11px;color:#94a3b8;">(Để 0 nếu khách trả đủ. Chỉ nhập khi bán nợ.)</span></div>
      <div class="row"><span>Tiền thừa:</span> <span id="changeLabel">0đ</span></div>
      <button class="btn-pay" id="payBtn">F9 · Thanh toán &amp; In bill</button>
      <button class="btn-clear" id="cancelBtn">ESC · Hủy giỏ hàng</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script src="<?= base_url('assets/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<script>
(function(){
  var BASE = '<?= base_url() ?>';
  var products = [];
  var cart = [];  // {id, name, sku, price, qty, stock}

  var $grid = document.getElementById('productGrid');
  var $empty = document.getElementById('emptyState');
  var $search = document.getElementById('searchBox');
  var $cartBody = document.getElementById('cartBody');
  var $cartCount = document.getElementById('cartCount');
  var $gross = document.getElementById('grossLabel');
  var $net = document.getElementById('netLabel');
  var $change = document.getElementById('changeLabel');
  var $discount = document.getElementById('discountInput');
  var $paid = document.getElementById('paidInput');
  var $store = document.getElementById('storeSelect');

  // Lưu cửa hàng đang chọn
  var savedStore = localStorage.getItem('pos_store_id');
  if (savedStore && $store) $store.value = savedStore;
  $store && $store.addEventListener('change', function(){ localStorage.setItem('pos_store_id', this.value); loadProducts($search.value.trim()); });

  function fmt(n){ return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ'; }
  function showToast(msg, isErr){
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show' + (isErr ? ' err' : '');
    setTimeout(function(){ t.className = 'toast'; }, 2200);
  }

  function loadProducts(q){
    var sid = parseInt($store.value, 10) || 0;
    var qs = [];
    if (q) qs.push('q=' + encodeURIComponent(q));
    if (sid) qs.push('store_id=' + sid);
    fetch(BASE + 'pos/products' + (qs.length ? '?' + qs.join('&') : ''))
      .then(function(r){ return r.json(); })
      .then(function(data){
        products = data || [];
        renderGrid();
      });
  }

  function renderGrid(){
    if (!products.length) {
      $grid.innerHTML = '';
      $empty.style.display = 'block';
      return;
    }
    $empty.style.display = 'none';
    var html = '';
    products.forEach(function(p){
      var stock = parseInt(p.qty, 10) || 0;
      var out = stock <= 0;
      html += '<div class="pos-card ' + (out ? 'out' : '') + '" data-id="' + p.id + '">'
        + '<div class="name">' + escapeHtml(p.name) + '</div>'
        + '<div class="sku">' + escapeHtml(p.sku) + '</div>'
        + '<div class="price">' + fmt(parseFloat(p.price) || 0) + '</div>'
        + '<div class="stock">Tồn: ' + stock + '</div>'
        + '</div>';
    });
    $grid.innerHTML = html;
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }

  $grid.addEventListener('click', function(e){
    var card = e.target.closest('.pos-card');
    if (!card || card.classList.contains('out')) return;
    var id = parseInt(card.getAttribute('data-id'), 10);
    var p = products.find(function(x){ return parseInt(x.id,10) === id; });
    if (p) addToCart(p);
  });

  function addToCart(p){
    var stock = parseInt(p.qty, 10) || 0;
    var existing = cart.find(function(x){ return x.id === parseInt(p.id,10); });
    if (existing) {
      if (existing.qty + 1 > stock) { showToast('Vượt tồn kho!', true); return; }
      existing.qty += 1;
    } else {
      if (stock <= 0) { showToast('Hết hàng!', true); return; }
      cart.push({
        id: parseInt(p.id,10),
        name: p.name,
        sku: p.sku,
        price: parseFloat(p.price) || 0,
        qty: 1,
        stock: stock,
      });
    }
    renderCart();
  }

  function renderCart(){
    $cartCount.textContent = cart.length;
    if (!cart.length) {
      $cartBody.innerHTML = '<div class="cart-empty">Chưa có sản phẩm nào.<br><small>Quét barcode hoặc click sản phẩm bên trái.</small></div>';
      updateTotals();
      return;
    }
    var html = '';
    cart.forEach(function(it, idx){
      html += '<div class="cart-item">'
        + '<div class="info">'
        +   '<div class="nm">' + escapeHtml(it.name) + '</div>'
        +   '<div class="pr">' + fmt(it.price) + ' × </div>'
        + '</div>'
        + '<div class="qty-ctrl">'
        +   '<button data-idx="' + idx + '" data-act="dec">−</button>'
        +   '<input type="number" min="1" max="' + it.stock + '" value="' + it.qty + '" data-idx="' + idx + '" data-act="set">'
        +   '<button data-idx="' + idx + '" data-act="inc">+</button>'
        + '</div>'
        + '<div class="amt">' + fmt(it.qty * it.price) + '</div>'
        + '<div class="rm" data-idx="' + idx + '" data-act="rm" title="Xóa"><i class="fa fa-times"></i></div>'
        + '</div>';
    });
    $cartBody.innerHTML = html;
    updateTotals();
  }

  $cartBody.addEventListener('click', function(e){
    var t = e.target.closest('[data-act]');
    if (!t) return;
    var idx = parseInt(t.getAttribute('data-idx'),10);
    var act = t.getAttribute('data-act');
    if (act === 'inc') {
      if (cart[idx].qty + 1 > cart[idx].stock) { showToast('Vượt tồn kho!', true); return; }
      cart[idx].qty += 1;
    } else if (act === 'dec') {
      cart[idx].qty -= 1;
      if (cart[idx].qty <= 0) cart.splice(idx,1);
    } else if (act === 'rm') {
      cart.splice(idx,1);
    }
    renderCart();
  });
  $cartBody.addEventListener('change', function(e){
    var t = e.target.closest('[data-act="set"]');
    if (!t) return;
    var idx = parseInt(t.getAttribute('data-idx'),10);
    var v = parseInt(t.value,10) || 1;
    if (v > cart[idx].stock) { v = cart[idx].stock; showToast('Tồn tối đa: ' + cart[idx].stock, true); }
    if (v < 1) v = 1;
    cart[idx].qty = v;
    renderCart();
  });

  function updateTotals(){
    var gross = cart.reduce(function(s,it){ return s + it.qty*it.price; }, 0);
    var disc = parseFloat($discount.value) || 0;
    var net = Math.max(0, gross - disc);
    var paid = parseFloat($paid.value) || 0;
    var change = Math.max(0, paid - net);
    $gross.textContent = fmt(gross);
    $net.textContent = fmt(net);
    $change.textContent = fmt(change);
  }
  $discount.addEventListener('input', updateTotals);
  $paid.addEventListener('input', updateTotals);

  // Search + barcode
  var searchTimer;
  $search.addEventListener('input', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function(){ loadProducts($search.value.trim()); }, 250);
  });
  $search.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
      e.preventDefault();
      var code = $search.value.trim();
      if (!code) return;
      // Thử lookup chính xác trước (barcode)
      var sid = parseInt($store.value, 10) || 0;
      fetch(BASE + 'pos/lookup?code=' + encodeURIComponent(code) + (sid ? '&store_id=' + sid : ''))
        .then(function(r){ return r.json(); })
        .then(function(p){
          if (p) {
            addToCart(p);
            $search.value = '';
            loadProducts('');
          } else {
            showToast('Không tìm thấy: ' + code, true);
          }
        });
    }
  });

  // Clear / Cancel
  document.getElementById('clearBtn').addEventListener('click', function(e){
    e.preventDefault();
    if (cart.length && !confirm('Xóa toàn bộ giỏ hàng?')) return;
    cart = []; renderCart();
  });
  document.getElementById('cancelBtn').addEventListener('click', function(){
    if (cart.length && !confirm('Hủy giỏ hàng?')) return;
    cart = []; $discount.value=0; $paid.value=0; renderCart();
    $search.focus();
  });

  // Nguồn tiền (tiền mặt / chuyển khoản)
  var payMethod = 'cash';
  Array.prototype.forEach.call(document.querySelectorAll('.pm-btn'), function(b){
    b.addEventListener('click', function(){
      payMethod = b.getAttribute('data-method');
      Array.prototype.forEach.call(document.querySelectorAll('.pm-btn'), function(x){ x.classList.remove('active'); });
      b.classList.add('active');
    });
  });

  // Pay
  document.getElementById('payBtn').addEventListener('click', checkout);

  function checkout(){
    if (!cart.length) { showToast('Giỏ trống', true); return; }
    var sid = parseInt($store.value, 10) || 0;
    if (!sid) { showToast('Vui lòng tạo & chọn cửa hàng trước (vào menu Cửa hàng)', true); return; }
    var payload = {
      items: cart.map(function(it){ return { id: it.id, qty: it.qty, price: it.price }; }),
      discount: parseFloat($discount.value) || 0,
      paid_amount: parseFloat($paid.value) || 0,
      customer_name: document.getElementById('custName').value.trim(),
      customer_phone: document.getElementById('custPhone').value.trim(),
      store_id: parseInt($store.value, 10) || 0,
      payment_method: payMethod,
    };
    var btn = document.getElementById('payBtn');
    btn.disabled = true; btn.textContent = 'Đang xử lý...';
    fetch(BASE + 'pos/checkout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(function(r){ return r.json(); })
    .then(function(res){
      btn.disabled = false; btn.textContent = 'F9 · Thanh toán & In bill';
      if (res.ok) {
        showToast('Đã tạo đơn ' + res.bill_no);
        // Mở bill in
        window.open(res.print_url, '_blank', 'width=400,height=600');
        // Reset
        cart = []; $discount.value=0; $paid.value=0;
        document.getElementById('custName').value = '';
        document.getElementById('custPhone').value = '';
        renderCart();
        loadProducts('');
        $search.value = ''; $search.focus();
      } else {
        showToast(res.error || 'Lỗi', true);
      }
    })
    .catch(function(){
      btn.disabled = false; btn.textContent = 'F9 · Thanh toán & In bill';
      showToast('Lỗi kết nối', true);
    });
  }

  // Hotkeys
  document.addEventListener('keydown', function(e){
    if (e.key === 'F2') { e.preventDefault(); $search.focus(); $search.select(); }
    else if (e.key === 'F9') { e.preventDefault(); checkout(); }
    else if (e.key === 'Escape') {
      if (cart.length) { if (confirm('Hủy giỏ hàng?')) { cart=[]; renderCart(); } }
      $search.focus();
    }
  });

  // Auto-find customer by phone
  var phoneTimer;
  document.getElementById('custPhone').addEventListener('input', function(){
    var v = this.value.trim();
    clearTimeout(phoneTimer);
    if (v.length < 6) { document.getElementById('custInfo').style.display='none'; return; }
    phoneTimer = setTimeout(function(){
      fetch(BASE + 'pos/findCustomer?phone=' + encodeURIComponent(v))
        .then(function(r){ return r.json(); })
        .then(function(c){
          var $info = document.getElementById('custInfo');
          if (c) {
            document.getElementById('custName').value = c.name;
            $info.innerHTML = '✓ KH: <b>' + escapeHtml(c.name) + '</b> · Điểm: ' + (c.loyalty_points||0) + (parseFloat(c.debt)>0 ? ' · <span style="color:#dc2626;">Nợ: ' + new Intl.NumberFormat('vi-VN').format(c.debt) + 'đ</span>' : '');
            $info.style.display = 'block'; $info.style.color = '#059669';
          } else {
            $info.innerHTML = 'KH mới — sẽ tự tạo khi thanh toán';
            $info.style.display = 'block'; $info.style.color = '#94a3b8';
          }
        });
    }, 400);
  });

  // Init
  loadProducts('');
})();
</script>
</body>
</html>
