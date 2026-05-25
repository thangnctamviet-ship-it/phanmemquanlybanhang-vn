

  <!-- Content Wrapper. Contains page content -->
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
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Bảng điều khiển
        <small>Bảng tổng quan</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li class="active">Bảng điều khiển</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <?php if($is_admin == true): ?>

        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo $total_products ?></h3>

                <p>Tổng sản phẩm</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="<?php echo base_url('products/') ?>" class="small-box-footer">Xem thêm <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php echo $total_paid_orders ?></h3>

                <p>Tổng đơn hàng đã thanh toán</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo base_url('orders/') ?>" class="small-box-footer">Xem thêm <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3><?php echo $total_users; ?></h3>

                <p>Tổng người dùng</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-people"></i>
              </div>
              <a href="<?php echo base_url('users/') ?>" class="small-box-footer">Xem thêm <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3><?php echo $total_stores ?></h3>

                <p>Tổng cửa hàng</p>
              </div>
              <div class="icon">
                <i class="ion ion-android-home"></i>
              </div>
              <a href="<?php echo base_url('stores/') ?>" class="small-box-footer">Xem thêm <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
      <?php endif; ?>


    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <script type="text/javascript">
    $(document).ready(function() {
      $("#dashboardMainMenu").addClass('active');
    });
  </script>
