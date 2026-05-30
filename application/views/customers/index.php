<div class="content-wrapper">
  <section class="content-header">
    <h1>Quản lý <small>Khách hàng</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Khách hàng</li>
    </ol>
  </section>

  <section class="content">
    <div id="messages"></div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Thêm khách hàng</button>
    <br><br>
    <div class="box">
      <div class="box-header"><h3 class="box-title">Danh sách khách hàng</h3></div>
      <div class="box-body">
        <table id="manageTable" class="table table-bordered table-striped">
          <thead><tr><th>Tên</th><th>SĐT</th><th>Điểm tích lũy</th><th>Công nợ</th><th>Thao tác</th></tr></thead>
        </table>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="addModal"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Thêm khách hàng</h4></div>
  <form id="createForm" method="post" action="<?= base_url('customers/create') ?>">
    <div class="modal-body">
      <div class="form-group"><label>Tên *</label><input type="text" name="name" class="form-control" required></div>
      <div class="form-group"><label>SĐT</label><input type="text" name="phone" class="form-control"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
      <div class="form-group"><label>Địa chỉ</label><input type="text" name="address" class="form-control"></div>
      <div class="form-group"><label>Ngày sinh</label><input type="date" name="birthday" class="form-control"></div>
      <div class="form-group"><label>Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu</button></div>
  </form>
</div></div></div>

<div class="modal fade" tabindex="-1" role="dialog" id="editModal"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Sửa khách hàng</h4></div>
  <form id="editForm" method="post">
    <div class="modal-body">
      <input type="hidden" name="customer_id" id="edit_id">
      <div class="form-group"><label>Tên *</label><input type="text" name="edit_name" id="edit_name" class="form-control" required></div>
      <div class="form-group"><label>SĐT</label><input type="text" name="edit_phone" id="edit_phone" class="form-control"></div>
      <div class="form-group"><label>Email</label><input type="email" name="edit_email" id="edit_email" class="form-control"></div>
      <div class="form-group"><label>Địa chỉ</label><input type="text" name="edit_address" id="edit_address" class="form-control"></div>
      <div class="form-group"><label>Ngày sinh</label><input type="date" name="edit_birthday" id="edit_birthday" class="form-control"></div>
      <div class="form-group"><label>Ghi chú</label><textarea name="edit_note" id="edit_note" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Cập nhật</button></div>
  </form>
</div></div></div>

<div class="modal fade" tabindex="-1" role="dialog" id="removeModal"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Xác nhận xóa</h4></div>
  <form id="removeForm" method="post" action="<?= base_url('customers/remove') ?>">
    <div class="modal-body"><input type="hidden" name="customer_id" id="remove_id"><p>Bạn có chắc muốn xóa?</p></div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button><button type="submit" class="btn btn-danger">Xóa</button></div>
  </form>
</div></div></div>

<script>
$(function(){
  var dt = $('#manageTable').DataTable({ ajax: { url: '<?= base_url('customers/fetchData') ?>', type: 'GET' } });
  function ajaxSubmit($f, url, after){ $.post(url, $f.serialize(), function(res){
    var m = (typeof res === 'string') ? JSON.parse(res) : res;
    $('#messages').html('<div class="alert alert-'+(m.success?'success':'danger')+'">'+m.messages+'</div>');
    if (m.success) { dt.ajax.reload(); after && after(); }
  }); }
  $('#createForm').on('submit', function(e){ e.preventDefault(); ajaxSubmit($(this), $(this).attr('action'), function(){ $('#addModal').modal('hide'); $('#createForm')[0].reset(); }); });
  $('#editForm').on('submit', function(e){ e.preventDefault(); var id = $('#edit_id').val(); ajaxSubmit($(this), '<?= base_url('customers/update/') ?>'+id, function(){ $('#editModal').modal('hide'); }); });
  $('#removeForm').on('submit', function(e){ e.preventDefault(); ajaxSubmit($(this), $(this).attr('action'), function(){ $('#removeModal').modal('hide'); }); });
});
function editCustomer(id){ $.getJSON('<?= base_url('customers/getById/') ?>'+id, function(d){
  $('#edit_id').val(d.id); $('#edit_name').val(d.name); $('#edit_phone').val(d.phone); $('#edit_email').val(d.email);
  $('#edit_address').val(d.address); $('#edit_birthday').val(d.birthday); $('#edit_note').val(d.note);
}); }
function removeCustomer(id){ $('#remove_id').val(id); }
</script>
