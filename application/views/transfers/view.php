<div class="content-wrapper">
  <section class="content-header">
    <h1>Phiếu chuyển kho <small><?= htmlspecialchars($transfer['code']) ?></small></h1>
  </section>
  <section class="content">
    <a href="<?= base_url('transfers') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Quay lại</a>
    <a href="<?= base_url('transfers/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tạo phiếu mới</a>
    <br><br>
    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Sản phẩm đã chuyển</h3></div>
          <div class="box-body">
            <table class="table table-bordered">
              <thead><tr><th>Sản phẩm</th><th>SKU</th><th>Số lượng</th></tr></thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr>
                    <td><?= htmlspecialchars($it['product_name']) ?></td>
                    <td><small><?= htmlspecialchars($it['sku']) ?></small></td>
                    <td><?= (int)$it['qty'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Thông tin</h3></div>
          <div class="box-body">
            <p><strong>Mã:</strong> <?= htmlspecialchars($transfer['code']) ?></p>
            <p><strong>Từ kho:</strong> <?= htmlspecialchars($transfer['from_name'] ?: '—') ?></p>
            <p><strong>Đến kho:</strong> <?= htmlspecialchars($transfer['to_name'] ?: '—') ?></p>
            <p><strong>Ngày:</strong> <?= date('d/m/Y H:i', strtotime($transfer['created_at'])) ?></p>
            <p><strong>Trạng thái:</strong> <span class="label label-success">Hoàn tất</span></p>
            <?php if ($transfer['note']): ?>
              <hr><p><strong>Ghi chú:</strong><br><?= nl2br(htmlspecialchars($transfer['note'])) ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
