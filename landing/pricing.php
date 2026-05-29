<?php $page_title = 'Bảng giá'; include __DIR__.'/includes/header.php'; ?>

<section class="max-w-6xl mx-auto px-4 py-12">
  <h1 class="text-3xl md:text-4xl font-bold text-center mb-3">Bảng giá đơn giản, minh bạch</h1>
  <p class="text-center text-slate-600 mb-2">Dùng thử <strong>7 ngày miễn phí</strong> — không cần thẻ tín dụng</p>
  <p class="text-center text-slate-500 text-sm mb-10">Tất cả các gói đã bao gồm <strong>2 chi nhánh</strong>. Cần thêm chi nhánh: <strong>50.000đ/tháng/chi nhánh</strong> (giảm 17% gói 6 tháng, giảm 25% gói năm)</p>

  <?php
  // Tính năng dùng chung cho mọi gói (chỉ ghi cái phần mềm THỰC SỰ có)
  $common_features = [
    'Không giới hạn sản phẩm & đơn hàng',
    'Không giới hạn nhân viên',
    '2 chi nhánh / cửa hàng',
    'Quản lý kho, nhập/xuất, tồn kho',
    'Bán hàng, in hoá đơn khổ K80',
    'Báo cáo doanh thu, đơn hàng',
    'Phân quyền nhân viên theo cửa hàng',
    'Database riêng cho mỗi cửa hàng',
    'Không phí khởi tạo',
    'Hỗ trợ qua email trong giờ hành chính',
  ];

  $plans = [
    [
      'id' => 'monthly',
      'name' => 'GÓI THÁNG',
      'price' => '120.000',
      'sub' => '₫ / tháng',
      'desc' => 'Phù hợp cửa hàng nhỏ, muốn thử trước khi cam kết dài hạn.',
      'tag' => '',
      'highlight' => false,
    ],
    [
      'id' => 'semiannual',
      'name' => 'GÓI 6 THÁNG',
      'price' => '600.000',
      'sub' => '₫ / 6 tháng',
      'desc' => 'Lựa chọn phổ biến nhất. Tiết kiệm 17% so với trả theo tháng.',
      'tag' => 'Phổ biến',
      'highlight' => true,
      'effective' => '~100.000đ/tháng',
    ],
    [
      'id' => 'annual',
      'name' => 'GÓI NĂM',
      'price' => '1.100.000',
      'sub' => '₫ / năm',
      'desc' => 'Tiết kiệm tối đa cho cửa hàng đã ổn định kinh doanh.',
      'tag' => 'Tiết kiệm nhất',
      'highlight' => false,
      'effective' => '~92.000đ/tháng',
    ],
  ];
  ?>

  <div class="grid md:grid-cols-3 gap-6">
    <?php foreach($plans as $p): ?>
    <div class="bg-white rounded-2xl shadow-sm p-6 relative flex flex-col <?= $p['highlight']?'border-2 border-indigo-500 shadow-lg':'border border-slate-200' ?>">
      <?php if($p['tag']): ?>
        <span class="absolute -top-3 left-1/2 -translate-x-1/2 <?= $p['highlight']?'bg-indigo-600':'bg-emerald-600' ?> text-white text-xs px-3 py-1 rounded-full font-medium"><?= $p['tag'] ?></span>
      <?php endif; ?>

      <h3 class="text-sm font-semibold text-slate-500 tracking-wide"><?= $p['name'] ?></h3>
      <div class="mt-3">
        <span class="text-4xl font-bold text-slate-900"><?= $p['price'] ?></span>
        <span class="text-sm text-slate-500"><?= $p['sub'] ?></span>
      </div>
      <?php if(!empty($p['effective'])): ?>
        <p class="text-emerald-600 text-xs font-medium mt-1">Tương đương <?= $p['effective'] ?></p>
      <?php endif; ?>
      <p class="text-sm text-slate-600 mt-3 min-h-[40px]"><?= $p['desc'] ?></p>

      <a href="/landing/register.php?plan=<?= urlencode($p['id']) ?>"
         class="mt-5 block text-center py-2.5 rounded-lg font-semibold transition
                <?= $p['highlight'] ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
        Dùng thử miễn phí 7 ngày
      </a>

      <div class="border-t mt-6 pt-4">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Bao gồm:</p>
        <ul class="space-y-2 text-sm text-slate-700">
          <?php foreach($common_features as $f): ?>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
              <span><?= htmlspecialchars($f) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Add-on chi nhánh -->
  <div class="mt-12 bg-amber-50 border border-amber-200 rounded-2xl p-6 md:p-8">
    <div class="md:flex items-center justify-between gap-6">
      <div>
        <h3 class="text-xl font-bold text-amber-900 mb-1">🏬 Cần nhiều hơn 2 chi nhánh?</h3>
        <p class="text-amber-800 text-sm">Mua thêm chi nhánh ngay trong phần mềm — chỉ <strong>50.000đ/tháng/chi nhánh</strong>.</p>
        <ul class="text-amber-800 text-sm mt-2 space-y-1">
          <li>• Mua 6 tháng: <strong>giảm 17%</strong></li>
          <li>• Mua 12 tháng: <strong>giảm 25%</strong></li>
        </ul>
      </div>
      <div class="bg-white rounded-xl p-4 mt-4 md:mt-0 min-w-[200px]">
        <p class="text-xs text-slate-500 uppercase font-semibold">Ví dụ</p>
        <p class="text-slate-700 text-sm mt-1">3 chi nhánh thêm × 12 tháng:</p>
        <p class="text-2xl font-bold text-amber-700 mt-1">1.350.000đ</p>
        <p class="text-xs text-slate-500">(thay vì 1.800.000đ)</p>
      </div>
    </div>
  </div>

  <!-- FAQ ngắn -->
  <div class="mt-12">
    <h2 class="text-2xl font-bold text-center mb-6">Câu hỏi thường gặp</h2>
    <div class="max-w-3xl mx-auto space-y-3">
      <details class="bg-white border rounded-lg p-4">
        <summary class="cursor-pointer font-semibold">Tôi có cần thẻ tín dụng để dùng thử không?</summary>
        <p class="text-slate-600 mt-2 text-sm">Không. Bạn đăng ký bằng email + đặt tên cửa hàng là dùng được ngay 7 ngày, không cần thanh toán gì.</p>
      </details>
      <details class="bg-white border rounded-lg p-4">
        <summary class="cursor-pointer font-semibold">Khi hết hạn dùng thử mà chưa thanh toán thì sao?</summary>
        <p class="text-slate-600 mt-2 text-sm">Bạn vẫn login được, xem báo cáo và sửa sản phẩm bình thường. Tính năng tạo đơn hàng mới sẽ tạm khoá cho đến khi gia hạn.</p>
      </details>
      <details class="bg-white border rounded-lg p-4">
        <summary class="cursor-pointer font-semibold">Có phí khởi tạo, phí ẩn không?</summary>
        <p class="text-slate-600 mt-2 text-sm">Không có bất kỳ phí ẩn nào. Bạn chỉ trả tiền theo gói đã chọn. Dữ liệu cửa hàng được lưu riêng trên database độc lập.</p>
      </details>
      <details class="bg-white border rounded-lg p-4">
        <summary class="cursor-pointer font-semibold">Thanh toán bằng cách nào?</summary>
        <p class="text-slate-600 mt-2 text-sm">Chuyển khoản qua mã VietQR (quét bằng app ngân hàng bất kỳ — Napas hỗ trợ). Tài khoản sẽ được gia hạn trong vòng 24 giờ sau khi nhận được CK.</p>
      </details>
      <details class="bg-white border rounded-lg p-4">
        <summary class="cursor-pointer font-semibold">Dữ liệu của tôi có an toàn không?</summary>
        <p class="text-slate-600 mt-2 text-sm">Mỗi cửa hàng có một database riêng biệt — không chia sẻ với bất kỳ khách hàng nào khác. Toàn bộ truy cập đều qua HTTPS bảo mật.</p>
      </details>
    </div>
  </div>
</section>
<?php include __DIR__.'/includes/footer.php'; ?>
