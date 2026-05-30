<?php $fmt = function($n){ return number_format((float)$n,0,',','.').'đ'; }; ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Phiếu nhập <small><?= htmlspecialchars($purchase['code']) ?></small></h1>
  </section>
  <section class="content">
    <a href="<?= base_url('purchases') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Quay lại</a>
    <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tạo phiếu mới</a>
    <br><br>
    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Chi tiết</h3></div>
          <div class="box-body">
            <table class="table table-bordered">
              <thead><tr><th>Sản phẩm</th><th>SKU</th><th>SL</th><th>Giá nhập</th><th>Thành tiền</th></tr></thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr>
                    <td><?= htmlspecialchars($it['product_name']) ?></td>
                    <td><small><?= htmlspecialchars($it['sku']) ?></small></td>
                    <td><?= (int)$it['qty'] ?></td>
                    <td><?= $fmt($it['cost_price']) ?></td>
                    <td><?= $fmt($it['amount']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr><th colspan="4" class="text-right">Tổng:</th><th><?= $fmt($purchase['total_amount']) ?></th></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Thông tin</h3></div>
          <div class="box-body">
            <p><strong>Mã:</strong> <?= htmlspecialchars($purchase['code']) ?></p>
            <p><strong>NCC:</strong> <?= htmlspecialchars($purchase['supplier_name'] ?: '—') ?></p>
            <p><strong>Cửa hàng:</strong> <?= htmlspecialchars($purchase['store_name'] ?: '—') ?></p>
            <p><strong>Ngày:</strong> <?= date('d/m/Y H:i', strtotime($purchase['created_at'])) ?></p>
            <hr>
            <p><strong>Tổng tiền:</strong> <?= $fmt($purchase['total_amount']) ?></p>
            <p><strong>Đã trả:</strong> <?= $fmt($purchase['paid_amount']) ?></p>
            <p style="color:<?= $purchase['debt_amount'] > 0 ? '#dc2626' : '#059669' ?>;">
              <strong>Còn nợ:</strong> <?= $fmt($purchase['debt_amount']) ?>
            </p>
            <?php if ($purchase['note']): ?>
              <hr><p><strong>Ghi chú:</strong><br><?= nl2br(htmlspecialchars($purchase['note'])) ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
