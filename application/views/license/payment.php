<?php
// VietQR cần mã ngân hàng (BIN). CIMB Việt Nam = 422589
$bank_bins = [
  'CIMB Việt Nam' => '422589',
  'Vietcombank' => '970436',
  'Techcombank' => '970407',
  'BIDV' => '970418',
  'MB Bank' => '970422',
  'TPBank' => '970423',
  'ACB' => '970416',
  'VPBank' => '970432',
  'Sacombank' => '970403',
  'VietinBank' => '970415',
];
$bank_bin = $bank_bins[$bank['name']] ?? '';
$qr_url = '';
if ($bank_bin) {
  $qr_url = "https://img.vietqr.io/image/{$bank_bin}-" . urlencode($bank['account']) . "-compact2.png"
          . "?amount=" . intval($info['amount'])
          . "&addInfo=" . urlencode($ref)
          . "&accountName=" . urlencode($bank['holder']);
}
?>
<div class="content-wrapper"><section class="content" style="padding:20px;max-width:780px;">
  <h2>Hướng dẫn thanh toán</h2>
  <div class="box box-warning"><div class="box-body">
    <div class="row">
      <div class="col-sm-6">
        <p><strong>Vui lòng chuyển khoản đến:</strong></p>
        <ul style="line-height:2;">
          <li><strong>Ngân hàng:</strong> <?= htmlspecialchars($bank['name']) ?></li>
          <li><strong>Số tài khoản:</strong> <code><?= htmlspecialchars($bank['account']) ?></code></li>
          <li><strong>Chủ tài khoản:</strong> <?= htmlspecialchars($bank['holder']) ?></li>
          <li><strong>Số tiền:</strong> <span style="color:#d9534f;font-size:18px;font-weight:bold;"><?= number_format($info['amount']) ?> ₫</span></li>
          <li><strong>Nội dung CK:</strong><br><code style="font-size:15px;background:#fff3cd;padding:4px 8px;display:inline-block;margin-top:4px;"><?= htmlspecialchars($ref) ?></code></li>
        </ul>
      </div>
      <div class="col-sm-6 text-center">
        <?php if ($qr_url): ?>
          <p><strong>Quét mã để thanh toán nhanh:</strong></p>
          <img src="<?= $qr_url ?>" alt="QR thanh toán" style="max-width:280px;border:1px solid #eee;padding:8px;background:white;">
          <p style="font-size:12px;color:#777;margin-top:8px;">Mở app ngân hàng → quét QR → đã điền sẵn nội dung & số tiền</p>
        <?php else: ?>
          <p style="color:#888;"><em>(Chưa có mã QR cho ngân hàng này)</em></p>
        <?php endif; ?>
      </div>
    </div>
    <hr>
    <p>📩 Sau khi chuyển khoản, admin sẽ xác nhận trong vòng <strong>24 giờ</strong>. Tài khoản của bạn sẽ tự động được gia hạn.</p>
    <p>Cần hỗ trợ? Liên hệ: <a href="mailto:hotroquanlybanhang.shop@gmail.com">hotroquanlybanhang.shop@gmail.com</a></p>
    <a href="<?= site_url('dashboard') ?>" class="btn btn-default">← Về dashboard</a>
  </div></div>
</section></div>
