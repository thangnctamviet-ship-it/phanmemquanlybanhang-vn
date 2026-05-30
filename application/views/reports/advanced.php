<?php
$fmt = function($n){ return number_format((float)$n,0,',','.').'đ'; };
$num = function($n){ return number_format((int)$n,0,',','.'); };
$qs  = '?from=' . urlencode($from) . '&to=' . urlencode($to);
$tabs = array(
  'overview'    => array('Tổng quan', 'fa-pie-chart'),
  'top_products'=> array('Top sản phẩm', 'fa-star'),
  'by_employee' => array('Theo nhân viên', 'fa-user'),
  'by_store'    => array('Theo cửa hàng', 'fa-building-o'),
  'slow_moving' => array('Tồn lâu', 'fa-hourglass-end'),
  'inventory'   => array('Giá trị tồn kho', 'fa-cubes'),
);
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Báo cáo nâng cao <small>Phân tích kinh doanh chi tiết</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li><a href="<?= base_url('reports') ?>">Báo cáo</a></li>
      <li class="active">Nâng cao</li>
    </ol>
  </section>

  <section class="content">
    <!-- Filter -->
    <div class="box box-default">
      <div class="box-body">
        <form method="GET" action="<?= base_url('reports/advanced') ?>" class="form-inline">
          <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
          <div class="form-group" style="margin-right:10px;">
            <label>Từ ngày:</label>
            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control">
          </div>
          <div class="form-group" style="margin-right:10px;">
            <label>Đến:</label>
            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control">
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Lọc</button>
          <div class="btn-group" style="margin-left:10px;">
            <?php
              $today = date('Y-m-d');
              $shortcuts = array(
                array('Hôm nay', $today, $today),
                array('Hôm qua', date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day'))),
                array('7 ngày', date('Y-m-d', strtotime('-6 day')), $today),
                array('30 ngày', date('Y-m-d', strtotime('-29 day')), $today),
                array('Tháng này', date('Y-m-01'), $today),
                array('Tháng trước', date('Y-m-01', strtotime('-1 month')), date('Y-m-t', strtotime('-1 month'))),
              );
              foreach ($shortcuts as $s):
            ?>
              <a class="btn btn-default btn-sm" href="<?= base_url('reports/advanced?tab='.$tab.'&from='.$s[1].'&to='.$s[2]) ?>"><?= $s[0] ?></a>
            <?php endforeach; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" style="margin-bottom:14px;">
      <?php foreach ($tabs as $k => $v): ?>
        <li class="<?= $tab === $k ? 'active' : '' ?>">
          <a href="<?= base_url('reports/advanced?tab='.$k.'&from='.$from.'&to='.$to) ?>">
            <i class="fa <?= $v[1] ?>"></i> <?= $v[0] ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if ($tab === 'overview'): ?>
      <!-- ========== OVERVIEW ========== -->
      <div class="row">
        <div class="col-md-3 col-xs-6"><div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="fa fa-money"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Doanh thu</span>
            <span class="info-box-number" style="font-size:18px;"><?= $fmt($summary['revenue']) ?></span>
          </div>
        </div></div>
        <div class="col-md-3 col-xs-6"><div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-line-chart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Lợi nhuận tạm tính</span>
            <span class="info-box-number" style="font-size:18px;"><?= $fmt($summary['profit']) ?></span>
          </div>
        </div></div>
        <div class="col-md-3 col-xs-6"><div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="fa fa-shopping-cart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Số đơn</span>
            <span class="info-box-number"><?= $num($summary['order_count']) ?></span>
          </div>
        </div></div>
        <div class="col-md-3 col-xs-6"><div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-percent"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Tổng giảm giá</span>
            <span class="info-box-number" style="font-size:18px;"><?= $fmt($summary['discount']) ?></span>
          </div>
        </div></div>
      </div>

      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Doanh thu theo ngày</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/daily'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body"><canvas id="dailyChart" style="height:280px; max-height:280px;"></canvas></div>
      </div>

    <?php elseif ($tab === 'top_products'): ?>
      <!-- ========== TOP PRODUCTS ========== -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Top sản phẩm bán chạy</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/top_products'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <thead><tr><th>#</th><th>Sản phẩm</th><th>SKU</th><th style="text-align:right;">SL bán</th><th style="text-align:right;">Doanh thu</th></tr></thead>
            <tbody>
              <?php if (empty($top_products)): ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:20px;">Chưa có dữ liệu trong kỳ.</td></tr>
              <?php else: foreach ($top_products as $i => $p): ?>
                <tr>
                  <td><?= $i + 1 ?></td>
                  <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                  <td><small><?= htmlspecialchars($p['sku']) ?></small></td>
                  <td style="text-align:right;"><?= $num($p['qty']) ?></td>
                  <td style="text-align:right;color:#059669;font-weight:600;"><?= $fmt($p['revenue']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif ($tab === 'by_employee'): ?>
      <!-- ========== BY EMPLOYEE ========== -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Doanh số theo nhân viên</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/by_employee'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <thead><tr><th>Nhân viên</th><th style="text-align:right;">Số đơn</th><th style="text-align:right;">Doanh thu</th><th style="text-align:right;">Giảm giá đã cho</th></tr></thead>
            <tbody>
              <?php if (empty($by_employee)): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">Không có dữ liệu.</td></tr>
              <?php else: foreach ($by_employee as $u):
                $name = trim(($u['firstname'] ?? '').' '.($u['lastname'] ?? '')) ?: $u['username'];
              ?>
                <tr>
                  <td><strong><?= htmlspecialchars($name) ?></strong> <small class="text-muted"><?= htmlspecialchars($u['username']) ?></small></td>
                  <td style="text-align:right;"><?= $num($u['order_count']) ?></td>
                  <td style="text-align:right;color:#059669;font-weight:600;"><?= $fmt($u['revenue']) ?></td>
                  <td style="text-align:right;color:#dc2626;"><?= $fmt($u['discount']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif ($tab === 'by_store'): ?>
      <!-- ========== BY STORE ========== -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Doanh số theo cửa hàng</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/by_store'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <thead><tr><th>Cửa hàng</th><th style="text-align:right;">Số đơn</th><th style="text-align:right;">Doanh thu</th></tr></thead>
            <tbody>
              <?php if (empty($by_store)): ?>
                <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">Không có dữ liệu (cần đơn có store_id).</td></tr>
              <?php else: foreach ($by_store as $s): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($s['name'] ?: '(Chưa gán cửa hàng)') ?></strong></td>
                  <td style="text-align:right;"><?= $num($s['order_count']) ?></td>
                  <td style="text-align:right;color:#059669;font-weight:600;"><?= $fmt($s['revenue']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif ($tab === 'slow_moving'): ?>
      <!-- ========== SLOW MOVING ========== -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Hàng tồn lâu (chưa bán &gt; 90 ngày)</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/slow_moving'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <thead><tr><th>Sản phẩm</th><th>SKU</th><th style="text-align:right;">Tồn</th><th>Lần bán gần nhất</th></tr></thead>
            <tbody>
              <?php if (empty($slow_moving)): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">✓ Không có hàng tồn lâu.</td></tr>
              <?php else: foreach ($slow_moving as $p): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                  <td><small><?= htmlspecialchars($p['sku']) ?></small></td>
                  <td style="text-align:right;"><span class="label label-warning"><?= $num($p['qty']) ?></span></td>
                  <td><?= $p['last_sold_ts'] ? date('d/m/Y', $p['last_sold_ts']) : '<em class="text-muted">Chưa bao giờ</em>' ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif ($tab === 'inventory'): ?>
      <!-- ========== INVENTORY VALUE ========== -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Giá trị tồn kho theo cửa hàng</h3>
          <div class="box-tools"><a href="<?= base_url('reports/exportCsv/inventory'.$qs) ?>" class="btn btn-default btn-sm"><i class="fa fa-file-excel-o"></i> Export CSV</a></div>
        </div>
        <div class="box-body no-padding">
          <table class="table table-striped">
            <thead><tr><th>Cửa hàng</th><th style="text-align:right;">Tổng SL</th><th style="text-align:right;">Giá trị tồn (giá vốn)</th></tr></thead>
            <tbody>
              <?php
                $total_qty = 0; $total_val = 0;
                if (empty($inventory)):
              ?>
                <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">Chưa có dữ liệu tồn kho.</td></tr>
              <?php else: foreach ($inventory as $r):
                $total_qty += (int)$r['qty']; $total_val += (float)$r['value'];
              ?>
                <tr>
                  <td><strong><?= htmlspecialchars($r['store_name'] ?: '(N/A)') ?></strong></td>
                  <td style="text-align:right;"><?= $num($r['qty']) ?></td>
                  <td style="text-align:right;color:#059669;font-weight:600;"><?= $fmt($r['value']) ?></td>
                </tr>
              <?php endforeach; ?>
                <tr style="background:#f8fafc;font-weight:700;">
                  <td>TỔNG</td>
                  <td style="text-align:right;"><?= $num($total_qty) ?></td>
                  <td style="text-align:right;color:#059669;"><?= $fmt($total_val) ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

  </section>
</div>

<?php if ($tab === 'overview'): ?>
<script>
$(function(){
  var data = <?= json_encode($daily) ?>;
  var ctx = document.getElementById('dailyChart');
  if (ctx && typeof Chart !== 'undefined') {
    new Chart(ctx.getContext('2d'), {
      type: 'bar',
      data: {
        labels: data.map(function(x){ return x.date; }),
        datasets: [{
          label: 'Doanh thu',
          data: data.map(function(x){ return x.revenue; }),
          backgroundColor: 'rgba(60,141,188,0.7)',
          borderColor: 'rgba(60,141,188,1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        legend: { display: false },
        scales: { yAxes: [{ ticks: { beginAtZero: true, callback: function(v){
          if (v >= 1000000) return (v/1000000).toFixed(1) + 'tr';
          if (v >= 1000) return (v/1000).toFixed(0) + 'k';
          return v;
        } } }] },
        tooltips: { callbacks: { label: function(it){
          return new Intl.NumberFormat('vi-VN').format(it.yLabel) + 'đ';
        } } }
      }
    });
  }
});
</script>
<?php endif; ?>
