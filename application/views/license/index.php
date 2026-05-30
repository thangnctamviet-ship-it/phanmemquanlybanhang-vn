<div class="content-wrapper"><section class="content" style="padding:20px;">
  <h2>Gia hạn / Mua thêm chi nhánh</h2>

  <h3 style="margin-top:24px;">📦 Gói gia hạn sử dụng</h3>
  <div class="row" style="margin-top:12px;">
    <?php
    $plans = [
      ['monthly','Gói tháng','120.000 ₫','1 tháng',''],
      ['semiannual','Gói 6 tháng','600.000 ₫','6 tháng','Tiết kiệm 17%'],
      ['annual','Gói năm','1.100.000 ₫','12 tháng','Tiết kiệm 24%'],
    ];
    foreach($plans as $p): ?>
    <div class="col-md-4">
      <div class="box box-primary"><div class="box-body" style="text-align:center;">
        <h3><?= $p[1] ?></h3>
        <div style="font-size:28px;color:#3c8dbc;font-weight:bold;"><?= $p[2] ?></div>
        <p><?= $p[3] ?></p>
        <?php if($p[4]): ?><span class="label label-success"><?= $p[4] ?></span><?php endif; ?>
        <form method="POST" action="<?= site_url('license/buy') ?>" style="margin-top:14px;">
          <input type="hidden" name="plan" value="<?= $p[0] ?>">
          <button class="btn btn-primary btn-block">Mua gói này</button>
        </form>
      </div></div>
    </div>
    <?php endforeach; ?>
  </div>

  <h3 style="margin-top:32px;">🏬 Mua thêm chi nhánh</h3>
  <p class="text-muted">Mỗi chi nhánh thêm: <strong>50.000₫ / tháng</strong>. Mua nhiều tháng được giảm giá tương đương gói chính.</p>
  <div class="box box-warning"><div class="box-body">
    <form method="POST" action="<?= site_url('license/buy') ?>" id="extraForm">
      <input type="hidden" name="plan" value="extra_branch">
      <div class="row">
        <div class="col-md-4">
          <label>Số chi nhánh muốn thêm</label>
          <input type="number" name="qty" id="branchQty" min="1" max="50" value="1" class="form-control" style="font-size:18px;">
        </div>
        <div class="col-md-4">
          <label>Thời hạn</label>
          <select name="duration" id="branchDuration" class="form-control" style="font-size:18px;">
            <option value="1">1 tháng</option>
            <option value="6">6 tháng (giảm 17%)</option>
            <option value="12">12 tháng (giảm 25%)</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Thành tiền</label>
          <div id="branchTotal" style="font-size:28px;font-weight:bold;color:#d9534f;padding:6px 0;">50.000 ₫</div>
        </div>
      </div>
      <button class="btn btn-warning btn-lg" style="margin-top:16px;">
        💳 Thanh toán & Xem QR
      </button>
    </form>
  </div></div>
</section></div>

<script>
function fmtVnd(n){ return Math.round(n).toLocaleString('vi-VN') + ' ₫'; }
function calcBranch(){
  var q = parseInt(document.getElementById('branchQty').value) || 1;
  var d = parseInt(document.getElementById('branchDuration').value) || 1;
  var base = 50000 * q * d;
  var discount = (d===6) ? 0.17 : (d===12 ? 0.25 : 0);
  var total = Math.round(base * (1 - discount) / 1000) * 1000; // làm tròn nghìn
  document.getElementById('branchTotal').textContent = fmtVnd(total);
}
document.getElementById('branchQty').addEventListener('input', calcBranch);
document.getElementById('branchDuration').addEventListener('change', calcBranch);
calcBranch();

// Auto-submit khi đến từ register?plan=monthly|semiannual|annual
(function(){
  var p = new URLSearchParams(location.search).get('plan');
  if (!p) return;
  if (!['monthly','semiannual','annual'].includes(p)) return;
  // Highlight gói tương ứng + auto submit sau 1.5s với toast
  var forms = document.querySelectorAll('form[action$="/license/buy"]');
  var targetForm = null;
  forms.forEach(function(f){
    var input = f.querySelector('input[name="plan"]');
    if (input && input.value === p && !targetForm) targetForm = f;
  });
  if (targetForm) {
    var box = targetForm.closest('.box');
    if (box) box.style.boxShadow = '0 0 0 3px #4f46e5';
    var t = document.createElement('div');
    t.innerHTML = '⏳ Đang tự chuyển đến trang thanh toán <strong>' + p + '</strong>...';
    t.style.cssText = 'position:fixed;top:80px;right:20px;background:#059669;color:#fff;padding:14px 20px;border-radius:10px;font-size:14px;z-index:9999;box-shadow:0 6px 20px rgba(0,0,0,.2);';
    document.body.appendChild(t);
    setTimeout(function(){ targetForm.submit(); }, 1500);
  }
})();
</script>
