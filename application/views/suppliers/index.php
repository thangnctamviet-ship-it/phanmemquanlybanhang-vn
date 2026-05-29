<div class="content-wrapper">
  <section class="content-header">
    <h1>Quản lý <small>Nhà cung cấp</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Nhà cung cấp</li>
    </ol>
  </section>

  <section class="content">
    <div id="messages"></div>

    <button class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Thêm nhà cung cấp</button>
    <br><br>

    <div class="box">
      <div class="box-header"><h3 class="box-title">Danh sách NCC</h3></div>
      <div class="box-body">
        <table id="manageTable" class="table table-bordered table-striped">
          <thead><tr><th>Tên NCC</th><th>Điện thoại</th><th>Email</th><th>Công nợ</th><th>Thao tác</th></tr></thead>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Add modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="addModal">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Thêm nhà cung cấp</h4>
    </div>
    <form id="createForm" method="post" action="<?= base_url('suppliers/create') ?>">
      <div class="modal-body">
        <div class="form-group"><label>Tên NCC *</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Điện thoại</label><input type="text" name="phone" class="form-control"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control"></div>
        <div class="form-group"><label>Địa chỉ</label><input type="text" name="address" class="form-control"></div>
        <div class="form-group"><label>Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Lưu</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Edit modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Sửa nhà cung cấp</h4>
    </div>
    <form id="editForm" method="post">
      <div class="modal-body">
        <input type="hidden" name="supplier_id" id="edit_id">
        <div class="form-group"><label>Tên NCC *</label><input type="text" name="edit_name" id="edit_name" class="form-control" required></div>
        <div class="form-group"><label>Điện thoại</label><input type="text" name="edit_phone" id="edit_phone" class="form-control"></div>
        <div class="form-group"><label>Email</label><input type="email" name="edit_email" id="edit_email" class="form-control"></div>
        <div class="form-group"><label>Địa chỉ</label><input type="text" name="edit_address" id="edit_address" class="form-control"></div>
        <div class="form-group"><label>Ghi chú</label><textarea name="edit_note" id="edit_note" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
      </div>
    </form>
  </div></div>
</div>

<!-- Remove modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Xác nhận xóa</h4>
    </div>
    <form id="removeForm" method="post" action="<?= base_url('suppliers/remove') ?>">
      <div class="modal-body">
        <input type="hidden" name="supplier_id" id="remove_id">
        <p>Bạn có chắc muốn xóa nhà cung cấp này?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-danger">Xóa</button>
      </div>
    </form>
  </div></div>
</div>

<script>
$(function(){
  var dt = $('#manageTable').DataTable({
    ajax: { url: '<?= base_url('suppliers/fetchData') ?>', type: 'GET' },
    language: { url: '<?= base_url('assets/dist/js/Vietnamese.json') ?>' }
  });

  function ajaxSubmit($form, url, after){
    $.post(url, $form.serialize(), function(res){
      var m = (typeof res === 'string') ? JSON.parse(res) : res;
      $('#messages').html('<div class="alert alert-'+(m.success?'success':'danger')+'">'+m.messages+'</div>');
      if (m.success) { dt.ajax.reload(); after && after(); }
    });
  }

  $('#createForm').on('submit', function(e){ e.preventDefault();
    ajaxSubmit($(this), $(this).attr('action'), function(){ $('#addModal').modal('hide'); $('#createForm')[0].reset(); });
  });
  $('#editForm').on('submit', function(e){ e.preventDefault();
    var id = $('#edit_id').val();
    ajaxSubmit($(this), '<?= base_url('suppliers/update/') ?>'+id, function(){ $('#editModal').modal('hide'); });
  });
  $('#removeForm').on('submit', function(e){ e.preventDefault();
    ajaxSubmit($(this), $(this).attr('action'), function(){ $('#removeModal').modal('hide'); });
  });
});

function editSupplier(id){
  $.getJSON('<?= base_url('suppliers/getById/') ?>'+id, function(d){
    $('#edit_id').val(d.id);
    $('#edit_name').val(d.name); $('#edit_phone').val(d.phone);
    $('#edit_email').val(d.email); $('#edit_address').val(d.address);
    $('#edit_note').val(d.note);
  });
}
function removeSupplier(id){ $('#remove_id').val(id); }

$(function(){ $("#mainConfigNav, #suppliersNav").addClass('active'); });
</script>
