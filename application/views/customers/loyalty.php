<?php
$fmt = function($n){ return number_format((float)$n,0,',','.').'đ'; };
$num = function($n){ return number_format((int)$n,0,',','.'); };
$month_name = date('n');
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Khách hàng thân thiết <small>Top KH · Tích điểm · Sinh nhật</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li><a href="<?= base_url('customers') ?>">Khách hàng</a></li>
      <li class="active">Thân thiết</li>
    </ol>
  </section>

  <section class="content">

    <!-- Sinh nhật hôm nay -->
    <?php if (!empty($birthdays_today)): ?>
      <div class="alert alert-warning" style="border-left:4px solid #f59e0b;">
        <strong>🎂 Sinh nhật hôm nay (<?= count($birthdays_today) ?> KH):</strong>
        <?php foreach ($birthdays_today as $b): ?>
          <span class="label label-warning" style="font-size:13px;margin-right:6px;">
            <?= htmlspecialchars($b['name']) ?> · <?= htmlspecialchars($b['phone'] ?: '—') ?>
          </span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- Cấu hình điểm -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-cog"></i> Tỷ lệ tích điểm</h3>
      </div>
      <div class="box-body">
        <form id="rateForm" class="form-inline">
          <label>Cứ <strong>1.000đ</strong> được</label>
          <input type="number" id="rateInput" value="<?= htmlspecialchars($loyalty_rate) ?>" step="0.1" min="0" class="form-control" style="width:90px;">
          <label>điểm</label>
          <button type="submit" class="btn btn-primary btn-sm" style="margin-left:10px;"><i class="fa fa-save"></i> Lưu</button>
          <span class="text-muted" style="margin-left:10px;font-size:12px;">Ví dụ: Đặt 1 → mua 100k = 100 điểm. Đặt 0.5 → mua 100k = 50 điểm.</span>
        </form>
        <p id="rateMsg" style="margin:8px 0 0;display:none;"></p>
      </div>
    </div>

    <div class="row">
      <!-- Top theo điểm -->
      <div class="col-md-6">
        <div class="box box-warning">
          <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-star"></i> Top 20 KH theo điểm tích lũy</h3></div>
          <div class="box-body no-padding">
            <?php if (empty($top_points)): ?>
              <p class="text-center text-muted" style="padding:20px;">Chưa có KH nào tích điểm.</p>
            <?php else: ?>
              <table class="table table-striped">
                <thead><tr><th>#</th><th>Tên</th><th>SĐT</th><th style="text-align:right;">Điểm</th><th style="text-align:right;">Nợ</th></tr></thead>
                <tbody>
                  <?php foreach ($top_points as $i => $c): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                      <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                      <td style="text-align:right;"><span class="label label-warning"><?= $num($c['loyalty_points']) ?></span></td>
                      <td style="text-align:right;color:<?= (float)$c['debt']>0?'#dc2626':'#94a3b8' ?>;"><?= $fmt($c['debt']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Top theo chi tiêu -->
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-money"></i> Top 20 KH theo chi tiêu</h3></div>
          <div class="box-body no-padding">
            <?php if (empty($top_spent)): ?>
              <p class="text-center text-muted" style="padding:20px;">Chưa có đơn hàng nào gắn với KH.</p>
            <?php else: ?>
              <table class="table table-striped">
                <thead><tr><th>#</th><th>Tên</th><th>SĐT</th><th style="text-align:right;">Đơn</th><th style="text-align:right;">Tổng chi</th></tr></thead>
                <tbody>
                  <?php foreach ($top_spent as $i => $c): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                      <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                      <td style="text-align:right;"><?= $num($c['order_count']) ?></td>
                      <td style="text-align:right;color:#059669;font-weight:600;"><?= $fmt($c['total_spent']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Sinh nhật tháng -->
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-birthday-cake"></i> Sinh nhật tháng <?= $month_name ?> (<?= count($birthdays) ?> KH)</h3>
      </div>
      <div class="box-body no-padding">
        <?php if (empty($birthdays)): ?>
          <p class="text-center text-muted" style="padding:20px;">Không có KH nào sinh tháng này.</p>
        <?php else: ?>
          <table class="table table-striped">
            <thead><tr><th>Ngày</th><th>Tên</th><th>SĐT</th><th style="text-align:right;">Điểm</th><th></th></tr></thead>
            <tbody>
              <?php foreach ($birthdays as $b):
                $today = date('m-d') === date('m-d', strtotime($b['birthday']));
              ?>
                <tr <?= $today ? 'style="background:#fef3c7;"' : '' ?>>
                  <td><strong><?= date('d/m', strtotime($b['birthday'])) ?></strong> <?= $today ? '<span class="label label-danger">Hôm nay!</span>' : '' ?></td>
                  <td><?= htmlspecialchars($b['name']) ?></td>
                  <td><?= htmlspecialchars($b['phone'] ?: '—') ?></td>
                  <td style="text-align:right;"><?= $num($b['loyalty_points']) ?></td>
                  <td><?php if ($b['phone']): ?>
                    <a href="tel:<?= htmlspecialchars($b['phone']) ?>" class="btn btn-xs btn-default"><i class="fa fa-phone"></i></a>
                    <a href="sms:<?= htmlspecialchars($b['phone']) ?>" class="btn btn-xs btn-default"><i class="fa fa-envelope"></i></a>
                  <?php endif; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </section>
</div>

<script>
$('#rateForm').on('submit', function(e){
  e.preventDefault();
  var rate = parseFloat($('#rateInput').val()) || 0;
  $.post('<?= base_url('customers/setLoyaltyRate') ?>', { rate: rate }, function(res){
    var r = (typeof res === 'string') ? JSON.parse(res) : res;
    $('#rateMsg').show().css('color', r.ok ? '#059669' : '#dc2626').text(r.ok ? '✓ Đã lưu' : (r.error || 'Lỗi'));
    setTimeout(function(){ $('#rateMsg').fadeOut(); }, 2000);
  });
});
</script>
