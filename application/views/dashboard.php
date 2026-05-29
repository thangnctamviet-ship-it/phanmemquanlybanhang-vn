<?php
$fmt = function($n) { return number_format((float)$n, 0, ',', '.') . 'đ'; };
?>
<div class="content-wrapper">
  <?php
    $CI =& get_instance();
    if (!empty($CI->license) && $CI->license->hasTenant()):
      $t = $CI->license->getTenant();
      $days = $CI->license->daysLeft();
      $expired = $CI->license->isExpired();
      $trial = $CI->license->isTrial();
  ?>
    <?php if ($expired): ?>
      <div style="background:#fee2e2;border-left:4px solid #dc2626;padding:12px;margin:10px;border-radius:6px;">
        <strong>⚠️ Bản dùng thử đã kết thúc.</strong> Tính năng tạo đơn hàng/chi nhánh mới đã bị khoá.
        <a href="<?= site_url('license') ?>" class="btn btn-danger btn-sm" style="margin-left:10px;">Gia hạn ngay</a>
      </div>
    <?php elseif ($trial): ?>
      <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:12px;margin:10px;border-radius:6px;">
        <strong>🎁 Bạn đang dùng thử.</strong> Còn <strong><?= $days ?></strong> ngày.
        <a href="<?= site_url('license') ?>" class="btn btn-warning btn-sm" style="margin-left:10px;">Nâng cấp ngay</a>
      </div>
    <?php else: ?>
      <div style="background:#d1fae5;border-left:4px solid #059669;padding:8px 12px;margin:10px;border-radius:6px;font-size:13px;color:#065f46;">
        ✓ Gói <strong><?= htmlspecialchars($t['plan']) ?></strong>, hết hạn <?= $t['expires_at'] ?> (còn <?= $days ?> ngày)
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <section class="content-header">
    <h1>Bảng điều khiển <small>Tổng quan kinh doanh</small></h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Bảng điều khiển</li>
    </ol>
  </section>

  <section class="content">

    <!-- Hàng 1: 4 widget chính -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3 style="font-size:24px;"><?= $fmt($rev_today) ?></h3>
            <p>Doanh thu hôm nay
              <?php if ($rev_yesterday > 0): ?>
                <br><small style="font-size:12px;">
                  <?php if ($rev_diff_pct >= 0): ?>
                    <i class="fa fa-arrow-up"></i> +<?= number_format($rev_diff_pct, 1) ?>% so hôm qua
                  <?php else: ?>
                    <i class="fa fa-arrow-down"></i> <?= number_format($rev_diff_pct, 1) ?>% so hôm qua
                  <?php endif; ?>
                </small>
              <?php endif; ?>
            </p>
          </div>
          <div class="icon"><i class="fa fa-money"></i></div>
          <a href="<?= base_url('orders/') ?>" class="small-box-footer">Xem đơn hàng <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?= (int)$orders_today ?></h3>
            <p>Số đơn hôm nay</p>
          </div>
          <div class="icon"><i class="fa fa-shopping-cart"></i></div>
          <a href="<?= base_url('orders/') ?>" class="small-box-footer">Xem chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3 style="font-size:22px;"><?= $fmt($rev_this_month) ?></h3>
            <p>Doanh thu tháng này</p>
          </div>
          <div class="icon"><i class="fa fa-calendar"></i></div>
          <a href="<?= base_url('reports/') ?>" class="small-box-footer">Báo cáo <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box <?= $low_stock_count > 0 ? 'bg-red' : 'bg-gray' ?>">
          <div class="inner">
            <h3><?= (int)$low_stock_count ?></h3>
            <p>Sản phẩm sắp hết hàng</p>
          </div>
          <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
          <a href="<?= base_url('products/') ?>" class="small-box-footer">Kiểm tra kho <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>

    <!-- Hàng 2: biểu đồ + bảng top -->
    <div class="row">
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-line-chart"></i> Doanh thu 30 ngày gần nhất</h3>
          </div>
          <div class="box-body">
            <canvas id="revenue30dChart" style="height:280px; max-height:280px;"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-star"></i> Top 5 bán chạy (7 ngày)</h3>
          </div>
          <div class="box-body no-padding">
            <?php if (empty($top_products)): ?>
              <p style="padding:20px;text-align:center;color:#999;">Chưa có dữ liệu bán hàng trong 7 ngày qua.</p>
            <?php else: ?>
              <table class="table table-striped" style="margin-bottom:0;">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th style="text-align:right;">SL</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($top_products as $i => $p): ?>
                    <tr>
                      <td><?= $i + 1 ?></td>
                      <td>
                        <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                        <small style="color:#999;"><?= htmlspecialchars($p['sku']) ?></small>
                      </td>
                      <td style="text-align:right;"><strong><?= (int)$p['total_qty'] ?></strong></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Hàng 3: SP sắp hết + placeholder công nợ -->
    <div class="row">
      <div class="col-md-6">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Sản phẩm sắp hết hàng</h3>
            <div class="box-tools"><small style="color:#999;">Tồn ≤ 5</small></div>
          </div>
          <div class="box-body no-padding">
            <?php if (empty($low_stock)): ?>
              <p style="padding:20px;text-align:center;color:#27ae60;">✓ Tất cả sản phẩm đều còn đủ hàng.</p>
            <?php else: ?>
              <table class="table table-striped" style="margin-bottom:0;">
                <thead>
                  <tr><th>Sản phẩm</th><th>SKU</th><th style="text-align:right;">Tồn</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($low_stock as $p): ?>
                    <tr>
                      <td><a href="<?= base_url('products/update/' . $p['id']) ?>"><?= htmlspecialchars($p['name']) ?></a></td>
                      <td><small><?= htmlspecialchars($p['sku']) ?></small></td>
                      <td style="text-align:right;">
                        <span class="label <?= (int)$p['qty'] == 0 ? 'label-danger' : 'label-warning' ?>"><?= (int)$p['qty'] ?></span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-credit-card"></i> Công nợ</h3>
          </div>
          <div class="box-body" style="text-align:center; color:#999; padding:40px 20px;">
            <i class="fa fa-clock-o" style="font-size:36px;"></i>
            <p style="margin-top:10px;">Module công nợ đang được phát triển.<br><small>Có ở phiên bản tiếp theo.</small></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Hàng 4: thống kê hệ thống (cũ, gọn lại) -->
    <?php if ($is_admin): ?>
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="ion ion-bag"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Tổng sản phẩm</span>
              <span class="info-box-number"><?= $total_products ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-stats-bars"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Đơn đã thanh toán</span>
              <span class="info-box-number"><?= $total_paid_orders ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-android-people"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Người dùng</span>
              <span class="info-box-number"><?= $total_users ?></span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="ion ion-android-home"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Cửa hàng</span>
              <span class="info-box-number"><?= $total_stores ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </section>
</div>

<script type="text/javascript">
$(document).ready(function() {
  $("#dashboardMainMenu").addClass('active');

  var chartData = <?= json_encode($chart_30d) ?>;
  var labels = chartData.map(function(x){ return x.date; });
  var values = chartData.map(function(x){ return parseFloat(x.revenue); });

  var ctx = document.getElementById('revenue30dChart');
  if (ctx && typeof Chart !== 'undefined') {
    new Chart(ctx.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Doanh thu (đ)',
          data: values,
          backgroundColor: 'rgba(60,141,188,0.15)',
          borderColor: 'rgba(60,141,188,0.9)',
          pointBackgroundColor: 'rgba(60,141,188,1)',
          pointRadius: 3,
          borderWidth: 2,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true,
              callback: function(v){
                if (v >= 1000000) return (v/1000000).toFixed(1) + 'tr';
                if (v >= 1000) return (v/1000).toFixed(0) + 'k';
                return v;
              }
            }
          }]
        },
        tooltips: {
          callbacks: {
            label: function(item){
              return new Intl.NumberFormat('vi-VN').format(item.yLabel) + 'đ';
            }
          }
        }
      }
    });
  }
});
</script>
