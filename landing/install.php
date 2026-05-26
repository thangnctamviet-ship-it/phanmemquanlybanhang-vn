<?php $page_title = 'Tải app - Quản lý bán hàng'; include 'includes/header.php'; ?>

<section class="max-w-5xl mx-auto px-4 py-12">
  <div class="text-center mb-10">
    <h1 class="text-3xl md:text-4xl font-bold text-slate-900">📱 Cài app Quản lý bán hàng</h1>
    <p class="text-slate-600 mt-3">Dùng trực tiếp trên điện thoại như một ứng dụng thật. Hoạt động ngay cả khi mạng chập chờn.</p>
  </div>

  <div class="grid md:grid-cols-2 gap-6">

    <!-- iOS -->
    <div class="bg-white border rounded-2xl p-6 shadow-sm">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-2xl"></div>
        <div>
          <h2 class="text-xl font-bold">iOS / iPhone &amp; iPad</h2>
          <p class="text-sm text-slate-500">Safari · 3 bước</p>
        </div>
      </div>
      <ol class="space-y-3 text-sm text-slate-700">
        <li class="flex gap-3"><span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">1</span>
          Mở website <code class="bg-slate-100 px-2 py-0.5 rounded">quanlybanhang.shop</code> trên <b>Safari</b> (không phải Chrome).</li>
        <li class="flex gap-3"><span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">2</span>
          Bấm nút <b>Chia sẻ</b> <span class="inline-block px-1.5 py-0.5 border rounded text-xs">⬆️</span> ở thanh dưới.</li>
        <li class="flex gap-3"><span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">3</span>
          Chọn <b>Add to Home Screen</b> / <b>Thêm vào màn hình chính</b> → Bấm <b>Thêm</b>.</li>
      </ol>
      <a href="/" class="mt-6 block w-full text-center bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg font-medium">Mở trong Safari</a>
    </div>

    <!-- Android -->
    <div class="bg-white border rounded-2xl p-6 shadow-sm">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center text-2xl">🤖</div>
        <div>
          <h2 class="text-xl font-bold">Android</h2>
          <p class="text-sm text-slate-500">Chrome hoặc tải APK</p>
        </div>
      </div>

      <div class="border rounded-lg p-4 mb-3 bg-indigo-50/40">
        <div class="font-semibold text-indigo-700 mb-2">✅ Cách 1: Cài qua trình duyệt (Khuyến nghị)</div>
        <ol class="text-sm text-slate-700 space-y-1 list-decimal list-inside">
          <li>Mở website trên <b>Chrome</b>.</li>
          <li>Bấm menu <b>3 chấm</b> góc trên phải.</li>
          <li>Chọn <b>Add to Home Screen</b> / <b>Thêm vào màn hình chính</b>.</li>
        </ol>
        <a href="/" class="mt-3 inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Mở web ngay</a>
      </div>

      <div class="border rounded-lg p-4">
        <div class="font-semibold text-slate-800 mb-2">📦 Cách 2: Tải file APK</div>
        <a href="/downloads/quanlybanhang.apk" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium">⬇️ Tải APK</a>
        <p class="text-xs text-slate-500 mt-2">
          ⚠️ Khi cài lần đầu, Android sẽ hỏi cấp quyền <b>“Cài đặt từ nguồn không xác định”</b>.
          <a class="text-indigo-600 underline" target="_blank" href="https://www.google.com/search?q=cách+bật+cài+đặt+ứng+dụng+từ+nguồn+không+xác+định+android">Xem hướng dẫn</a>.
        </p>
      </div>
    </div>
  </div>

  <div class="text-center mt-10 text-sm text-slate-500">
    Cần hỗ trợ? Email <a class="text-indigo-600 underline" href="mailto:hotroquanlybanhang.shop@gmail.com">hotroquanlybanhang.shop@gmail.com</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
