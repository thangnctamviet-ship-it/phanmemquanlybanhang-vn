<div class="content-wrapper"><section class="content" style="padding:20px;max-width:700px;">
  <h2>Hướng dẫn thanh toán</h2>
  <div class="box box-warning"><div class="box-body">
    <p>Vui lòng chuyển khoản đến:</p>
    <ul>
      <li><strong>Ngân hàng:</strong> <?= htmlspecialchars($bank['name']) ?></li>
      <li><strong>Số tài khoản:</strong> <?= htmlspecialchars($bank['account']) ?></li>
      <li><strong>Chủ tài khoản:</strong> <?= htmlspecialchars($bank['holder']) ?></li>
      <li><strong>Số tiền:</strong> <?= number_format($info['amount']) ?> ₫</li>
      <li><strong>Nội dung CK:</strong> <code style="font-size:16px;background:#fff3cd;padding:4px 8px;"><?= htmlspecialchars($ref) ?></code></li>
    </ul>
    <p>Sau khi chuyển khoản, admin sẽ xác nhận trong vòng 24h. Tài khoản sẽ tự động được gia hạn.</p>
    <a href="<?= site_url('dashboard') ?>" class="btn btn-default">← Về dashboard</a>
  </div></div>
</section></div>
