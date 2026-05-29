<div class="content-wrapper">
  <section class="content-header">
    <h1>Quản lý <small>Chuyển kho giữa cửa hàng</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Chuyển kho</li>
    </ol>
  </section>
  <section class="content">
    <a href="<?= base_url('transfers/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tạo phiếu chuyển</a>
    <br><br>
    <div class="box">
      <div class="box-header"><h3 class="box-title">Danh sách phiếu chuyển</h3></div>
      <div class="box-body">
        <table id="manageTable" class="table table-bordered table-striped">
          <thead><tr><th>Mã phiếu</th><th>Từ kho</th><th>Đến kho</th><th>Ngày</th><th>Trạng thái</th><th></th></tr></thead>
        </table>
      </div>
    </div>
  </section>
</div>
<script>$(function(){ $('#manageTable').DataTable({ ajax: { url: '<?= base_url('transfers/fetchData') ?>', type: 'GET' }, order: [[3,'desc']] }); });</script>
