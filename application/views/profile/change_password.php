<div class="content-wrapper">
  <section class="content-header">
    <h1>Đổi mật khẩu</h1>
  </section>
  <section class="content">
    <?php if ($msg = $this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = $this->session->flashdata('error')): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <div class="row">
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header with-border"><h3 class="box-title">Cập nhật mật khẩu</h3></div>
          <form method="post" action="<?= base_url('profile/change_password') ?>">
            <div class="box-body">
              <div class="form-group">
                <label>Mật khẩu cũ</label>
                <input type="password" name="old_password" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Mật khẩu mới (≥ 6 ký tự)</label>
                <input type="password" name="new_password" class="form-control" minlength="6" required>
              </div>
              <div class="form-group">
                <label>Xác nhận mật khẩu mới</label>
                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
              </div>
            </div>
            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Cập nhật</button>
              <a href="<?= base_url('dashboard') ?>" class="btn btn-default">Huỷ</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
