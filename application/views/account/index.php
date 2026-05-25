<div class="content-wrapper"><section class="content" style="padding:20px;">
  <h2>Tài khoản</h2>

  <div class="box box-primary"><div class="box-body">
    <?php if($tenant): ?>
      <div class="row">
        <div class="col-md-3"><strong>Subdomain</strong><br><code><?= htmlspecialchars($tenant['subdomain']) ?></code></div>
        <div class="col-md-3"><strong>Trạng thái</strong><br><?= htmlspecialchars($tenant['status']) ?></div>
        <div class="col-md-3"><strong>Gói</strong><br><?= htmlspecialchars($tenant['plan']) ?></div>
        <div class="col-md-3"><strong>Hết hạn</strong><br><?= htmlspecialchars($tenant['expires_at']) ?></div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-6"><strong>Chi nhánh</strong><br><?= (int)$used_branches ?> / <?= (int)$max_branches ?></div>
        <div class="col-md-6 text-right">
          <form method="POST" action="<?= site_url('license/buy') ?>" style="display:inline-block;">
            <input type="hidden" name="plan" value="monthly">
            <button class="btn btn-primary">Gia hạn</button>
          </form>
          <form method="POST" action="<?= site_url('license/buy') ?>" style="display:inline-block;">
            <input type="hidden" name="plan" value="extra_branch">
            <button class="btn btn-info">Thêm chi nhánh</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <p>Không tìm thấy thông tin license cho tenant hiện tại.</p>
    <?php endif; ?>
  </div></div>

  <div class="box"><div class="box-header"><h3 class="box-title">Lịch sử thanh toán</h3></div><div class="box-body table-responsive">
    <table class="table table-bordered table-striped">
      <thead><tr><th>#</th><th>Gói</th><th>Số tiền</th><th>+Tháng</th><th>+CN</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead>
      <tbody>
      <?php foreach($payments as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['plan']) ?></td>
          <td><?= number_format((int)$p['amount']) ?> ₫</td>
          <td><?= (int)$p['months_added'] ?></td>
          <td><?= (int)$p['branches_added'] ?></td>
          <td><?= htmlspecialchars($p['status']) ?></td>
          <td><?= htmlspecialchars($p['created_at']) ?></td>
        </tr>
      <?php endforeach; if(!$payments): ?>
        <tr><td colspan="7" class="text-center text-muted">Chưa có thanh toán nào</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div></div>
</section></div>
