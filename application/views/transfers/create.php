<div class="content-wrapper">
  <section class="content-header">
    <h1>Tạo phiếu chuyển kho</h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Sản phẩm cần chuyển</h3></div>
          <div class="box-body">
            <div style="display:flex;gap:8px;margin-bottom:10px;">
              <select id="productSelect" class="form-control" style="flex:1;">
                <option value="">— Chọn sản phẩm —</option>
                <?php foreach ($products as $p): ?>
                  <option value="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" data-sku="<?= htmlspecialchars($p['sku']) ?>">
                    <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['sku']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <button type="button" id="addBtn" class="btn btn-default"><i class="fa fa-plus"></i></button>
            </div>
            <table class="table table-bordered" id="itemsTable">
              <thead><tr><th>Sản phẩm</th><th width="120">Tồn nguồn</th><th width="120">SL chuyển</th><th width="40"></th></tr></thead>
              <tbody><tr id="emptyRow"><td colspan="4" class="text-center text-muted">Chưa có sản phẩm</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="box">
          <div class="box-header"><h3 class="box-title">Thông tin phiếu</h3></div>
          <div class="box-body">
            <div class="form-group"><label>Từ kho *</label>
              <select id="fromStore" class="form-control">
                <option value="">— Chọn —</option>
                <?php foreach ($stores as $s): if(empty($s['active']))continue; ?>
                  <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Đến kho *</label>
              <select id="toStore" class="form-control">
                <option value="">— Chọn —</option>
                <?php foreach ($stores as $s): if(empty($s['active']))continue; ?>
                  <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label>Ghi chú</label><textarea id="note" class="form-control" rows="2"></textarea></div>
            <button type="button" id="saveBtn" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i> Lưu phiếu chuyển</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(function(){
  var items = [];
  function esc(s){ return String(s||'').replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];}); }
  function render(){
    var $tb = $('#itemsTable tbody');
    if (!items.length) { $tb.html('<tr><td colspan="4" class="text-center text-muted">Chưa có sản phẩm</td></tr>'); return; }
    var html = '';
    items.forEach(function(it,i){
      html += '<tr>'
        + '<td>' + esc(it.name) + ' <small class="text-muted">' + esc(it.sku) + '</small></td>'
        + '<td><span class="stock-cell" data-pid="'+it.id+'">—</span></td>'
        + '<td><input type="number" min="1" value="'+it.qty+'" data-i="'+i+'" class="form-control input-sm"></td>'
        + '<td><button class="btn btn-danger btn-xs" data-rm="'+i+'"><i class="fa fa-times"></i></button></td>'
        + '</tr>';
    });
    $tb.html(html);
    refreshStocks();
  }
  function refreshStocks(){
    var sid = parseInt($('#fromStore').val(),10);
    if (!sid) { $('.stock-cell').text('—'); return; }
    items.forEach(function(it){
      $.getJSON('<?= base_url('transfers/stock/') ?>'+it.id+'/'+sid, function(d){
        $('.stock-cell[data-pid="'+it.id+'"]').text(d.qty);
      });
    });
  }
  $('#fromStore').on('change', refreshStocks);
  $('#addBtn').on('click', function(){
    var $o = $('#productSelect option:selected'); var id = parseInt($o.val(),10);
    if (!id) return;
    if (items.find(function(x){return x.id===id;})) return;
    items.push({ id: id, name: $o.data('name'), sku: $o.data('sku'), qty: 1 });
    $('#productSelect').val('');
    render();
  });
  $('#itemsTable').on('input', 'input[data-i]', function(){
    var i = parseInt($(this).data('i'),10);
    items[i].qty = Math.max(1, parseInt($(this).val(),10) || 1);
  });
  $('#itemsTable').on('click', '[data-rm]', function(){
    items.splice(parseInt($(this).data('rm'),10),1); render();
  });
  $('#saveBtn').on('click', function(){
    var f = parseInt($('#fromStore').val(),10), t = parseInt($('#toStore').val(),10);
    if (!f || !t) { alert('Chọn cửa hàng nguồn và đích'); return; }
    if (f === t) { alert('Hai cửa hàng phải khác nhau'); return; }
    if (!items.length) { alert('Thêm ít nhất 1 sản phẩm'); return; }
    var $btn = $(this); $btn.prop('disabled', true).text('Đang lưu...');
    $.ajax({
      url: '<?= base_url('transfers/save') ?>', method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        from_store_id: f, to_store_id: t, note: $('#note').val(),
        items: items.map(function(it){ return {product_id: it.id, qty: it.qty}; })
      }),
      success: function(res){ var r = (typeof res==='string')?JSON.parse(res):res;
        if (r.ok) location.href = r.redirect; else { alert(r.error); $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Lưu phiếu chuyển'); } },
      error: function(){ alert('Lỗi'); $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Lưu phiếu chuyển'); }
    });
  });
});
</script>
