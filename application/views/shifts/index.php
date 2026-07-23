<?php $fmt = function($n){ return number_format((float)$n,0,',','.').'đ'; }; ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Chốt ca / Kết ca tiền mặt</h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Chốt ca</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-5">
        <div class="box box-primary">
          <div class="box-header with-border"><h3 class="box-title">Ca hiện tại</h3></div>
          <div class="box-body">
            <div class="form-group">
              <label>Cửa hàng</label>
              <select id="shStore" class="form-control">
                <?php foreach (($stores ?? array()) as $s): ?>
                  <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div id="shStatus" style="margin-top:10px;"></div>
          </div>
        </div>
      </div>

      <div class="col-md-7">
        <div class="box">
          <div class="box-header with-border"><h3 class="box-title">Lịch sử ca</h3></div>
          <div class="box-body no-padding" style="max-height:460px;overflow:auto;">
            <table class="table table-striped" style="font-size:13px;">
              <thead><tr>
                <th>Cửa hàng</th><th>NV</th><th>Mở ca</th><th>Đóng ca</th>
                <th style="text-align:right;">Đầu ca</th><th style="text-align:right;">Bán (mặt)</th>
                <th style="text-align:right;">Cuối ca</th><th style="text-align:right;">Chênh lệch</th>
              </tr></thead>
              <tbody>
                <?php if (empty($history)): ?>
                  <tr><td colspan="8" class="text-center text-muted" style="padding:18px;">Chưa có ca nào.</td></tr>
                <?php else: foreach ($history as $h):
                  $closed = !empty($h['check_out']);
                  $expected = (float)$h['opening_cash'] + (float)$h['total_sales'];
                  $diff = $closed ? ((float)$h['closing_cash'] - $expected) : null;
                ?>
                  <tr>
                    <td><?= htmlspecialchars($h['store_name'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($h['username'] ?: '-') ?></td>
                    <td><small><?= htmlspecialchars(date('d/m H:i', strtotime($h['check_in']))) ?></small></td>
                    <td><small><?= $closed ? htmlspecialchars(date('d/m H:i', strtotime($h['check_out']))) : '<span class="label label-success">Đang mở</span>' ?></small></td>
                    <td style="text-align:right;"><?= $fmt($h['opening_cash']) ?></td>
                    <td style="text-align:right;"><?= $closed ? $fmt($h['total_sales']) : '-' ?></td>
                    <td style="text-align:right;"><?= $closed ? $fmt($h['closing_cash']) : '-' ?></td>
                    <td style="text-align:right;font-weight:600;<?= $diff===null?'':($diff==0?'color:#059669;':($diff>0?'color:#2563eb;':'color:#dc2626;')) ?>">
                      <?= $diff===null ? '-' : (($diff>0?'+':'').$fmt($diff)) ?>
                    </td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal mở ca -->
<div class="modal fade" id="openShiftModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-play-circle"></i> Mở ca</h4></div>
  <div class="modal-body">
    <div class="form-group"><label>Tiền mặt đầu ca (đếm trong két lúc mở)</label>
      <input type="number" id="openCash" class="form-control" value="0" min="0" placeholder="Nhập số tiền đang có trong két"></div>
    <div class="form-group"><label>Ghi chú (không bắt buộc)</label>
      <input type="text" id="openNote" class="form-control" placeholder="Ví dụ: ca sáng, NV Lan"></div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
    <button type="button" id="btnOpenShift" class="btn btn-success"><i class="fa fa-play"></i> Mở ca</button>
  </div>
</div></div></div>

<!-- Modal đóng ca -->
<div class="modal fade" id="closeShiftModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title"><i class="fa fa-stop-circle"></i> Đóng ca</h4></div>
  <div class="modal-body">
    <div id="closeSummary" style="margin-bottom:12px;"></div>
    <div class="form-group"><label>Tiền mặt cuối ca (đếm thực tế trong két)</label>
      <input type="number" id="closeCash" class="form-control" value="0" min="0" placeholder="Đếm tiền thực tế rồi nhập vào"></div>
    <div id="diffPreview" style="margin-bottom:8px;"></div>
    <div class="form-group"><label>Ghi chú (không bắt buộc)</label>
      <input type="text" id="closeNote" class="form-control" placeholder="Ví dụ: lý do chênh lệch"></div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
    <button type="button" id="btnCloseShift" class="btn btn-danger"><i class="fa fa-stop"></i> Đóng ca & chốt</button>
  </div>
</div></div></div>

<script>
(function(){
  var base = '<?= base_url() ?>';
  var $store = document.getElementById('shStore');
  var $status = document.getElementById('shStatus');
  var current = null; // {shift_id, opening_cash, cash_sales, expected, ...}
  function fmt(n){ return (Math.round(n)||0).toLocaleString('vi-VN')+'đ'; }

  function loadStatus(){
    var sid = $store.value;
    $status.innerHTML = '<p class="text-muted">Đang tải...</p>';
    fetch(base+'shifts/status?store_id='+encodeURIComponent(sid))
      .then(function(r){return r.json();}).then(function(d){
        current = d.open ? d : null;
        if (!d.open) {
          $status.innerHTML = '<div class="alert alert-warning" style="margin:0;">Cửa hàng chưa mở ca.</div>'
            + '<button class="btn btn-success btn-block" style="margin-top:10px;" data-toggle="modal" data-target="#openShiftModal"><i class="fa fa-play"></i> Mở ca mới</button>';
        } else {
          $status.innerHTML =
            '<table class="table table-condensed" style="margin-bottom:8px;">'
            +'<tr><td>Mở lúc</td><td style="text-align:right;font-weight:600;">'+new Date(d.check_in.replace(' ','T')).toLocaleString('vi-VN')+'</td></tr>'
            +'<tr><td>Tiền đầu ca</td><td style="text-align:right;font-weight:600;">'+fmt(d.opening_cash)+'</td></tr>'
            +'<tr><td>Tiền mặt bán trong ca</td><td style="text-align:right;font-weight:600;color:#059669;">'+fmt(d.cash_sales)+' ('+d.order_count+' đơn)</td></tr>'
            +'<tr style="border-top:2px solid #e2e8f0;"><td><b>Dự kiến trong két</b></td><td style="text-align:right;font-weight:700;color:#2563eb;">'+fmt(d.expected)+'</td></tr>'
            +'</table>'
            +'<button class="btn btn-danger btn-block" id="openCloseModalBtn" data-toggle="modal" data-target="#closeShiftModal"><i class="fa fa-stop"></i> Đóng ca / Chốt ca</button>';
        }
      }).catch(function(){ $status.innerHTML='<div class="alert alert-danger">Lỗi tải trạng thái.</div>'; });
  }
  $store.addEventListener('change', loadStatus);
  loadStatus();

  document.getElementById('btnOpenShift').addEventListener('click', function(){
    var body = new URLSearchParams({store_id:$store.value, opening_cash:document.getElementById('openCash').value||0, note:document.getElementById('openNote').value||''});
    this.disabled=true;
    fetch(base+'shifts/open',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
      .then(function(r){return r.json();}).then(function(d){
        document.getElementById('btnOpenShift').disabled=false;
        if(!d.ok){ alert(d.error||'Lỗi'); return; }
        location.reload();
      });
  });

  // Khi mở modal đóng ca: hiện tóm tắt + tính chênh lệch realtime
  document.body.addEventListener('click', function(e){
    if(e.target.closest('#openCloseModalBtn') && current){
      document.getElementById('closeSummary').innerHTML =
        '<table class="table table-condensed" style="margin:0;">'
        +'<tr><td>Tiền đầu ca</td><td style="text-align:right;">'+fmt(current.opening_cash)+'</td></tr>'
        +'<tr><td>Tiền mặt bán trong ca</td><td style="text-align:right;color:#059669;">'+fmt(current.cash_sales)+'</td></tr>'
        +'<tr><td><b>Dự kiến trong két</b></td><td style="text-align:right;font-weight:700;">'+fmt(current.expected)+'</td></tr>'
        +'</table>';
      document.getElementById('closeCash').value = Math.round(current.expected);
      updateDiff();
    }
  });
  function updateDiff(){
    if(!current) return;
    var closing = parseFloat(document.getElementById('closeCash').value)||0;
    var diff = closing - current.expected;
    var color = diff===0?'#059669':(diff>0?'#2563eb':'#dc2626');
    var label = diff===0?'Khớp két ✓':(diff>0?'Thừa tiền':'Thiếu tiền');
    document.getElementById('diffPreview').innerHTML =
      '<div class="alert" style="margin:0;background:'+color+'12;border:1px solid '+color+';color:'+color+';">'
      +'<b>'+label+': '+(diff>0?'+':'')+fmt(diff)+'</b></div>';
  }
  document.getElementById('closeCash').addEventListener('input', updateDiff);

  document.getElementById('btnCloseShift').addEventListener('click', function(){
    if(!current) return;
    var body = new URLSearchParams({shift_id:current.shift_id, closing_cash:document.getElementById('closeCash').value||0, note:document.getElementById('closeNote').value||''});
    this.disabled=true;
    fetch(base+'shifts/close',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:body})
      .then(function(r){return r.json();}).then(function(d){
        document.getElementById('btnCloseShift').disabled=false;
        if(!d.ok){ alert(d.error||'Lỗi'); return; }
        var msg = 'Đã chốt ca!\n\nTiền đầu ca: '+fmt(d.opening_cash)
          +'\nTiền mặt bán: '+fmt(d.cash_sales)+' ('+d.order_count+' đơn)'
          +'\nDự kiến: '+fmt(d.expected)+'\nĐếm thực tế: '+fmt(d.closing_cash)
          +'\nChênh lệch: '+(d.difference>0?'+':'')+fmt(d.difference);
        alert(msg);
        location.reload();
      });
  });
})();
</script>
