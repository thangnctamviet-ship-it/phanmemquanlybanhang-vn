<?php $page_title = 'Bảng giá'; include __DIR__.'/includes/header.php'; ?>
<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold text-center mb-2">Bảng giá đơn giản, minh bạch</h1>
  <p class="text-center text-slate-600 mb-10">🎁 Dùng thử miễn phí 7 ngày · 2 chi nhánh miễn phí · 50.000đ/tháng cho mỗi chi nhánh thêm</p>
  <div class="grid md:grid-cols-3 gap-6">
    <?php
    $plans = [
      ['name'=>'Gói tháng','price'=>'120.000','sub'=>'₫ / tháng','note'=>'','months'=>1,'tag'=>''],
      ['name'=>'Gói 6 tháng','price'=>'600.000','sub'=>'₫ / 6 tháng','note'=>'Tiết kiệm 17%','months'=>6,'tag'=>'Phổ biến'],
      ['name'=>'Gói năm','price'=>'1.100.000','sub'=>'₫ / năm','note'=>'Tiết kiệm 24%','months'=>12,'tag'=>'Tốt nhất'],
    ];
    foreach($plans as $p): ?>
    <div class="bg-white rounded-2xl shadow-sm p-6 border-2 <?= $p['tag']==='Tốt nhất'?'border-indigo-500':'border-transparent' ?>">
      <?php if($p['tag']): ?><div class="text-xs inline-block bg-indigo-100 text-indigo-700 px-2 py-1 rounded mb-2"><?= $p['tag'] ?></div><?php endif; ?>
      <h3 class="text-xl font-semibold mb-1"><?= $p['name'] ?></h3>
      <div class="text-3xl font-bold text-indigo-600"><?= $p['price'] ?><span class="text-sm font-normal text-slate-500"><?= $p['sub'] ?></span></div>
      <?php if($p['note']): ?><p class="text-emerald-600 text-sm mt-1"><?= $p['note'] ?></p><?php endif; ?>
      <ul class="mt-4 space-y-2 text-sm text-slate-700">
        <li>✓ Sản phẩm không giới hạn</li>
        <li>✓ Đơn hàng không giới hạn</li>
        <li>✓ 2 chi nhánh miễn phí</li>
        <li>✓ Hỗ trợ qua email</li>
      </ul>
      <a href="register.php" class="mt-6 block text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">Đăng ký ngay</a>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
