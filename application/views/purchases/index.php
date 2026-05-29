<div class="content-wrapper">
  <section class="content-header">
    <h1>Quản lý <small>Nhập hàng</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Nhập hàng</li>
    </ol>
  </section>
  <section class="content">
    <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tạo phiếu nhập</a>
    <br><br>
    <div class="box">
      <div class="box-header"><h3 class="box-title">Danh sách phiếu nhập</h3></div>
      <div class="box-body">
        <table id="manageTable" class="table table-bordered table-striped">
          <thead><tr><th>Mã phiếu</th><th>NCC</th><th>Cửa hàng</th><th>Ngày</th><th>Tổng tiền</th><th>Công nợ</th><th></th></tr></thead>
        </table>
      </div>
    </div>
  </section>
</div>
<script>
$(function(){ $('#manageTable').DataTable({ ajax: { url: '<?= base_url('purchases/fetchData') ?>', type: 'GET' }, order: [[3,'desc']] }); });
</script>
