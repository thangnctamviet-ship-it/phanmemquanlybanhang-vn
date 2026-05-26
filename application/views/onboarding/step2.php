<div class="content-wrapper"><section class="content">
  <div class="box box-primary" style="max-width:680px;margin:40px auto;">
    <div class="box-header with-border"><h3 class="box-title">Bước 2/3 · Cấu hình công ty</h3></div>
    <form method="post" action="<?= base_url('onboarding/step2') ?>">
      <div class="box-body">
        <div class="form-group"><label>Tên công ty</label>
          <input class="form-control" name="company_name" required value="<?= htmlspecialchars($default_name) ?>"></div>
        <div class="form-group"><label>Địa chỉ</label>
          <input class="form-control" name="address" value="<?= htmlspecialchars($company['address'] ?? '') ?>"></div>
        <div class="form-group"><label>Số điện thoại</label>
          <input class="form-control" name="phone" value="<?= htmlspecialchars($company['phone'] ?? '') ?>"></div>
        <div class="form-group"><label>VAT mặc định (%)</label>
          <input class="form-control" name="vat" type="number" step="0.01" min="0" value="<?= htmlspecialchars($company['vat_charge_value'] ?? '0') ?>"></div>
      </div>
      <div class="box-footer">
        <button class="btn btn-primary" type="submit">Tiếp theo</button>
        <a href="<?= base_url('onboarding/skip') ?>" class="btn btn-link pull-right">Bỏ qua</a>
      </div>
    </form>
  </div>
</section></div>
