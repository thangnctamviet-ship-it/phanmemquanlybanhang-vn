<div class="content-wrapper"><section class="content" style="padding:20px;">
  <h2>Gia hạn / Mua thêm chi nhánh</h2>
  <div class="row" style="margin-top:20px;">
    <?php
    $plans = [
      ['monthly','Gói tháng','120.000 ₫','1 tháng',''],
      ['semiannual','Gói 6 tháng','600.000 ₫','6 tháng','Tiết kiệm 17%'],
      ['annual','Gói năm','1.100.000 ₫','12 tháng','Tiết kiệm 24%'],
      ['extra_branch','Thêm chi nhánh','50.000 ₫','+1 chi nhánh',''],
    ];
    foreach($plans as $p): ?>
    <div class="col-md-3">
      <div class="box box-primary"><div class="box-body" style="text-align:center;">
        <h3><?= $p[1] ?></h3>
        <div style="font-size:24px;color:#3c8dbc;font-weight:bold;"><?= $p[2] ?></div>
        <p><?= $p[3] ?></p>
        <?php if($p[4]): ?><span class="label label-success"><?= $p[4] ?></span><?php endif; ?>
        <form method="POST" action="<?= site_url('license/buy') ?>" style="margin-top:10px;">
          <input type="hidden" name="plan" value="<?= $p[0] ?>">
          <button class="btn btn-primary btn-block">Chọn gói này</button>
        </form>
      </div></div>
    </div>
    <?php endforeach; ?>
  </div>
</section></div>
