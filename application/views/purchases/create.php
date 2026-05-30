<div class="content-wrapper">
  <section class="content-header">
    <h1>Tạo phiếu nhập <small>Nhập hàng mới</small></h1>
  </section>
  <section class="content">
    <div id="messages"></div>
    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Sản phẩm nhập</h3></div>
          <div class="box-body">
            <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;">
              <select id="productSelect" class="form-control" style="flex:1;">
                <option value="">— Chọn sản phẩm để thêm vào phiếu —</option>
                <?php foreach ($products as $p): ?>
                  <option value="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-sku="<?= htmlspecialchars($p['sku']) ?>" data-price="<?= htmlspecialchars($p['price']) ?>">
                    <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['sku']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" id="addItemBtn" class="btn btn-default"><i class="fa fa-plus"></i> Thêm</button>
            </div>
            <table class="table table-bordered" id="itemsTable">
              <thead><tr><th>Sản phẩm</th><th width="100">SL</th><th width="140">Giá nhập</th><th width="140">Thành tiền</th><th width="40"></th></tr></thead>
              <tbody><tr id="emptyRow"><td colspan="5" class="text-center text-muted">Chưa có sản phẩm</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Thông tin phiếu</h3></div>
          <div class="box-body">
            <div class="form-group">
              <label>Nhà cung cấp *</label>
              <select id="supplierSelect" class="form-control">
                <option value="">— Chọn NCC —</option>
                <?php foreach ($suppliers as $s): ?>
                  <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <a href="<?= base_url('suppliers') ?>" target="_blank" style="font-size:12px;">+ Thêm NCC mới</a>
            </div>
            <div class="form-group">
              <label>Nhập vào cửa hàng *</label>
              <select id="storeSelect" class="form-control">
                <?php foreach ($stores as $s): if(empty($s['active']))continue; ?>
                  <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Ghi chú</label>
              <textarea id="noteInput" class="form-control" rows="2"></textarea>
            </div>
            <hr>
            <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
              <span>Tổng tiền:</span> <strong id="totalLabel">0đ</strong>
            </div>
            <div class="form-group">
              <label>Đã trả NCC</label>
              <input type="number" id="paidInput" class="form-control" value="0" min="0">
            </div>
            <div style="display:flex;justify-content:space-between;color:#dc2626;font-size:14px;margin-bottom:12px;">
              <span>Còn nợ:</span> <strong id="debtLabel">0đ</strong>
            </div>
            <button type="button" id="saveBtn" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i> Lưu phiếu nhập</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(function(){
  var items = [];
  function fmt(n){ return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + 'đ'; }

  function render(){
    var $tb = $('#itemsTable tbody');
    if (!items.length) {
      $tb.html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted">Chưa có sản phẩm</td></tr>');
      updateTotals(); return;
    }
    var html = '';
    items.forEach(function(it, i){
      var amt = it.qty * it.cost;
      html += '<tr>'
        + '<td>' + escapeHtml(it.name) + ' <small class="text-muted">' + escapeHtml(it.sku) + '</small></td>'
        + '<td><input type="number" min="1" value="' + it.qty + '" data-i="'+i+'" data-f="qty" class="form-control input-sm"></td>'
        + '<td><input type="number" min="0" value="' + it.cost + '" data-i="'+i+'" data-f="cost" class="form-control input-sm"></td>'
        + '<td>' + fmt(amt) + '</td>'
        + '<td><button class="btn btn-danger btn-xs" data-rm="'+i+'"><i class="fa fa-times"></i></button></td>'
        + '</tr>';
    });
    $tb.html(html);
    updateTotals();
  }
  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }

  function updateTotals(){
    var total = items.reduce(function(s,it){ return s + it.qty * it.cost; }, 0);
    $('#totalLabel').text(fmt(total));
    var paid = parseFloat($('#paidInput').val()) || 0;
    $('#debtLabel').text(fmt(Math.max(0, total - paid)));
  }

  $('#addItemBtn').on('click', function(){
    var $opt = $('#productSelect option:selected');
    var id = parseInt($opt.val(), 10);
    if (!id) return;
    var existing = items.find(function(x){ return x.id === id; });
    if (existing) { existing.qty += 1; render(); return; }
    items.push({
      id: id,
      name: $opt.data('name'),
      sku: $opt.data('sku'),
      qty: 1,
      cost: parseFloat($opt.data('price')) || 0,
    });
    $('#productSelect').val('');
    render();
  });

  $('#itemsTable').on('input', 'input[data-i]', function(){
    var i = parseInt($(this).data('i'),10);
    var f = $(this).data('f');
    var v = parseFloat($(this).val()) || 0;
    if (f === 'qty' && v < 1) v = 1;
    items[i][f] = v;
    updateTotals();
    $(this).closest('tr').find('td').eq(3).text(fmt(items[i].qty * items[i].cost));
  });
  $('#itemsTable').on('click', '[data-rm]', function(){
    items.splice(parseInt($(this).data('rm'),10), 1); render();
  });
  $('#paidInput').on('input', updateTotals);

  $('#saveBtn').on('click', function(){
    if (!$('#supplierSelect').val()) { alert('Chọn nhà cung cấp'); return; }
    if (!items.length) { alert('Thêm ít nhất 1 sản phẩm'); return; }
    var $btn = $(this); $btn.prop('disabled', true).text('Đang lưu...');
    $.ajax({
      url: '<?= base_url('purchases/save') ?>',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        supplier_id: parseInt($('#supplierSelect').val(),10),
        store_id: parseInt($('#storeSelect').val(),10),
        paid_amount: parseFloat($('#paidInput').val()) || 0,
        note: $('#noteInput').val(),
        items: items.map(function(it){ return { product_id: it.id, qty: it.qty, cost_price: it.cost }; }),
      }),
      success: function(res){
        var r = (typeof res === 'string') ? JSON.parse(res) : res;
        if (r.ok) location.href = r.redirect;
        else { alert(r.error || 'Lỗi'); $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Lưu phiếu nhập'); }
      },
      error: function(){ alert('Lỗi kết nối'); $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Lưu phiếu nhập'); }
    });
  });
});
</script>
