<?php
$g = function($k, $default = '') use ($s) { return htmlspecialchars($s[$k] ?? $default); };
$on = function($k) use ($s) { return !empty($s[$k]) && $s[$k] != '0'; };
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Cấu hình hệ thống <small>Thiết bị · Ngành hàng · Tính năng</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Cấu hình</li>
    </ol>
  </section>

  <section class="content">
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php elseif ($this->session->flashdata('errors')): ?>
      <div class="alert alert-danger"><?= $this->session->flashdata('errors') ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('settings/save') ?>">

      <div class="row">
        <div class="col-md-6">
          <!-- Ngành hàng -->
          <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-tag"></i> Ngành hàng</h3></div>
            <div class="box-body">
              <p class="text-muted" style="font-size:12px;">Chọn loại shop để tự bật/ẩn các trường phù hợp.</p>
              <select name="industry_preset" class="form-control">
                <?php
                  $presets = array(
                    'general'   => 'Chung (mặc định)',
                    'grocery'   => 'Tạp hóa',
                    'fashion'   => 'Thời trang (bật size/màu)',
                    'cosmetics' => 'Mỹ phẩm (bật lô + HSD)',
                    'pharmacy'  => 'Nhà thuốc (bật lô + HSD + đơn vị quy đổi)',
                    'mom_baby'  => 'Mẹ &amp; bé (bật combo + HSD)',
                    'phone'     => 'Điện thoại / Phụ kiện',
                    'food'      => 'Thực phẩm (bật HSD + trọng lượng)',
                  );
                  $cur = $s['industry_preset'] ?? 'general';
                  foreach ($presets as $k => $v):
                ?>
                  <option value="<?= $k ?>" <?= $cur === $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Bật tính năng -->
          <div class="box box-info">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-cogs"></i> Bật/tắt tính năng</h3></div>
            <div class="box-body">
              <?php
                $flags = array(
                  'enable_loyalty'        => 'Tích điểm khách hàng',
                  'enable_variants'       => 'Biến thể (size/màu)',
                  'enable_batches'        => 'Lô hàng &amp; HSD',
                  'enable_combos'         => 'Combo / Quà tặng',
                  'enable_wholesale'      => 'Bán sỉ (giá sỉ riêng)',
                  'enable_returns'        => 'Trả hàng (KH &amp; NCC)',
                  'enable_multi_unit'     => 'Đơn vị quy đổi (thùng/lốc/lon)',
                  'enable_promotions'     => 'Khuyến mãi &amp; Voucher',
                  'enable_employee_shift' => 'Ca làm việc nhân viên',
                );
                foreach ($flags as $k => $label):
              ?>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="<?= $k ?>" value="1" <?= $on($k) ? 'checked' : '' ?>>
                    <?= $label ?>
                  </label>
                </div>
              <?php endforeach; ?>
              <p class="text-muted" style="font-size:12px;margin-top:10px;"><i class="fa fa-info-circle"></i> Tắt tính năng chỉ ẩn UI — dữ liệu cũ vẫn giữ trong DB. Bật lại bất cứ lúc nào.</p>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <!-- Máy in -->
          <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-print"></i> Máy in hóa đơn</h3></div>
            <div class="box-body">
              <div class="form-group">
                <label>Khổ giấy in</label>
                <select name="print_bill_width" class="form-control">
                  <?php
                    $widths = array('58' => '58mm (máy in nhỏ)', '80' => '80mm (K80, phổ biến)', 'a4' => 'A4 (in laser)');
                    $cw = $s['print_bill_width'] ?? '80';
                    foreach ($widths as $k => $v):
                  ?>
                    <option value="<?= $k ?>" <?= $cw === $k ? 'selected' : '' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Cách mở cửa sổ in</label>
                <select name="print_bill_open_method" class="form-control">
                  <?php
                    $opts = array('popup' => 'Popup mới (khuyến nghị)', 'iframe' => 'Trong tab hiện tại', 'new_tab' => 'Tab mới (không tự đóng)');
                    $cm = $s['print_bill_open_method'] ?? 'popup';
                    foreach ($opts as $k => $v):
                  ?>
                    <option value="<?= $k ?>" <?= $cm === $k ? 'selected' : '' ?>><?= $v ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="print_auto" value="1" <?= $on('print_auto') ? 'checked' : '' ?>>
                  Tự động in ngay sau khi mở (window.print())
                </label>
              </div>
              <p class="text-muted" style="font-size:12px;">Đối với máy in nhiệt qua USB/cổng COM, dùng driver Windows + đặt máy in K80 làm máy in mặc định trên trình duyệt.</p>
              <a href="<?= base_url('pos') ?>" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-external-link"></i> Test in từ POS</a>
            </div>
          </div>

          <!-- Mã vạch -->
          <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-barcode"></i> Máy quét mã vạch</h3></div>
            <div class="box-body">
              <p class="text-muted" style="font-size:12px;">Máy quét USB hoạt động như bàn phím — chỉ cần focus ô tìm kiếm rồi quét. POS đã hỗ trợ sẵn.</p>
              <div class="form-group">
                <label>Tiền tố mã vạch tự sinh</label>
                <input type="text" name="barcode_prefix" value="<?= $g('barcode_prefix', '299') ?>" class="form-control" maxlength="4">
                <p class="help-block">Khi tạo SP không có mã vạch, hệ thống tự sinh: PREFIX + ID + check digit. Mặc định <code>299</code> (EAN13 dùng nội bộ).</p>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="barcode_check_digit" value="1" <?= $on('barcode_check_digit') ? 'checked' : '' ?>>
                  Thêm check digit EAN13
                </label>
              </div>
            </div>
          </div>

          <!-- Tồn kho -->
          <div class="box box-danger">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-cubes"></i> Cảnh báo tồn kho</h3></div>
            <div class="box-body">
              <div class="form-group">
                <label>Ngưỡng cảnh báo mặc định (tồn ≤)</label>
                <input type="number" name="low_stock_threshold" value="<?= $g('low_stock_threshold', '5') ?>" class="form-control" min="0">
                <p class="help-block">Áp dụng cho SP chưa đặt riêng <code>min_stock</code>. Có thể đặt riêng từng SP trong trang Sản phẩm.</p>
              </div>
              <div class="form-group">
                <label>Tỷ lệ tích điểm (điểm cho mỗi 1.000đ)</label>
                <input type="number" step="0.1" name="loyalty_points_per_1000" value="<?= $g('loyalty_points_per_1000', '1') ?>" class="form-control" min="0">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-qrcode"></i> Tài khoản nhận chuyển khoản (QR VietQR ở POS)</h3></div>
            <div class="box-body">
              <p class="help-block">Nhập tài khoản ngân hàng của cửa hàng. Khi bán ở POS chọn <b>Chuyển khoản</b>, hệ thống hiện mã QR đúng số tiền để khách quét chuyển nhanh.</p>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Ngân hàng</label>
                    <?php $banks = array('Vietcombank','Techcombank','BIDV','MB Bank','TPBank','ACB','VPBank','Sacombank','VietinBank','CIMB Việt Nam'); $cur = $s['pos_bank_name'] ?? ''; ?>
                    <select name="pos_bank_name" class="form-control">
                      <option value="">— Chọn ngân hàng —</option>
                      <?php foreach ($banks as $b): ?>
                        <option value="<?= htmlspecialchars($b) ?>" <?= $cur===$b?'selected':'' ?>><?= htmlspecialchars($b) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Số tài khoản</label>
                    <input type="text" name="pos_bank_account" value="<?= $g('pos_bank_account') ?>" class="form-control" placeholder="Ví dụ: 0123456789">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Tên chủ tài khoản</label>
                    <input type="text" name="pos_bank_holder" value="<?= $g('pos_bank_holder') ?>" class="form-control" placeholder="NGUYEN VAN A (không dấu)">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center" style="margin:20px 0;">
        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Lưu tất cả cấu hình</button>
      </div>
    </form>
  </section>
</div>
