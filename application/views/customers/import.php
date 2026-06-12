<div class="content-wrapper">
  <section class="content-header">
    <h1>Nhập khách hàng từ Excel</h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li><a href="<?= base_url('customers') ?>">Khách hàng</a></li>
      <li class="active">Nhập từ Excel</li>
    </ol>
  </section>

  <section class="content">
    <div class="box box-primary">
      <div class="box-body">

        <div class="callout callout-info" style="margin-bottom:15px;">
          <h4><i class="fa fa-info-circle"></i> Hướng dẫn 3 bước</h4>
          <ol style="margin:0;padding-left:18px;">
            <li><b>Tải file mẫu</b> → mở bằng Excel.</li>
            <li><b>Điền hoặc copy-paste</b> dữ liệu khách hàng (cột có dấu <b style="color:#dd4b39;">*</b> là bắt buộc). Xóa 2 dòng ví dụ.</li>
            <li><b>Chọn file</b> đã điền → xem trước → bấm <b>Xác nhận nhập</b>.</li>
          </ol>
        </div>

        <div class="row" style="margin-bottom:15px;">
          <div class="col-sm-4">
            <label>Bước 1 — Tải file mẫu</label><br>
            <a href="<?= base_url('customers/importTemplate') ?>" class="btn btn-default"><i class="fa fa-download"></i> Tải file mẫu Excel</a>
          </div>
          <div class="col-sm-4">
            <label>Bước 2 — Chọn file đã điền</label>
            <input type="file" id="impFile" accept=".xlsx,.xls,.csv" class="form-control">
          </div>
        </div>

        <div id="impSummary" style="display:none;margin-bottom:10px;"></div>
        <div id="impPreviewWrap" style="display:none;max-height:420px;overflow:auto;border:1px solid #e2e8f0;border-radius:6px;">
          <table class="table table-bordered table-condensed" style="margin:0;font-size:13px;">
            <thead style="position:sticky;top:0;background:#f8fafc;">
              <tr><th>#</th><th>Tên KH *</th><th>SĐT</th><th>Email</th><th>Địa chỉ</th><th>Ngày sinh</th><th>Ghi chú</th><th>Trạng thái</th></tr>
            </thead>
            <tbody id="impPreviewBody"></tbody>
          </table>
        </div>

        <div style="margin-top:15px;">
          <button id="impConfirm" class="btn btn-success" disabled><i class="fa fa-upload"></i> Xác nhận nhập</button>
          <span id="impResult" style="margin-left:12px;font-weight:600;"></span>
        </div>

      </div>
    </div>
  </section>
</div>

<script src="<?= base_url('assets/vendor/sheetjs/xlsx.full.min.js') ?>"></script>
<script>
(function(){
  var validRows=[];
  var $file=document.getElementById('impFile'), $body=document.getElementById('impPreviewBody'),
      $wrap=document.getElementById('impPreviewWrap'), $sum=document.getElementById('impSummary'),
      $btn=document.getElementById('impConfirm'), $res=document.getElementById('impResult');
  function norm(s){ return (s||'').toString().toLowerCase().replace(/\*/g,'').trim()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d'); }
  var HMAP={'ten khach hang':'name','ten':'name','khach hang':'name',
    'so dien thoai':'phone','sdt':'phone','dien thoai':'phone','phone':'phone',
    'email':'email','dia chi':'address',
    'ngay sinh (yyyy-mm-dd)':'birthday','ngay sinh':'birthday','birthday':'birthday',
    'ghi chu':'note','note':'note'};

  $file.addEventListener('change', function(e){
    var f=e.target.files[0]; if(!f) return; $res.textContent='';
    var reader=new FileReader();
    reader.onload=function(ev){
      var wb=XLSX.read(new Uint8Array(ev.target.result),{type:'array'});
      var aoa=XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]],{header:1,defval:''});
      parseRows(aoa);
    };
    reader.readAsArrayBuffer(f);
  });

  function parseRows(aoa){
    if(!aoa.length){ alert('File rỗng'); return; }
    var header=aoa[0].map(norm), colIdx={};
    header.forEach(function(h,i){ if(HMAP[h]!==undefined && colIdx[HMAP[h]]===undefined) colIdx[HMAP[h]]=i; });
    if(colIdx.name===undefined){ alert('Không nhận diện được cột "Tên khách hàng". Hãy dùng đúng file mẫu.'); return; }
    validRows=[]; var html='',ok=0,err=0,seen={};
    for(var r=1;r<aoa.length;r++){
      var row=aoa[r]; if(!row||row.every(function(c){return(''+c).trim()==='';})) continue;
      function g(k){ return colIdx[k]!==undefined?(''+row[colIdx[k]]).trim():''; }
      var o={name:g('name'),phone:g('phone'),email:g('email'),address:g('address'),birthday:g('birthday'),note:g('note')};
      var e=[];
      if(!o.name) e.push('thiếu Tên');
      if(o.phone && seen[o.phone]) e.push('trùng SĐT trong file');
      if(o.phone) seen[o.phone]=true;
      var good=e.length===0;
      if(good){ validRows.push(o); ok++; } else err++;
      html+='<tr style="'+(good?'':'background:#fee2e2;')+'"><td>'+r+'</td><td>'+esc(o.name)+'</td><td>'+esc(o.phone)+'</td><td>'+esc(o.email)+'</td><td>'+esc(o.address)+'</td><td>'+esc(o.birthday)+'</td><td>'+esc(o.note)+'</td><td>'+(good?'<span class="label label-success">OK</span>':'<span class="label label-danger">'+e.join(', ')+'</span>')+'</td></tr>';
    }
    $body.innerHTML=html; $wrap.style.display='block'; $sum.style.display='block';
    $sum.innerHTML='<div class="alert '+(ok?'alert-success':'alert-warning')+'" style="padding:8px 12px;">Sẵn sàng nhập: <b>'+ok+'</b> khách hàng. '+(err?('Bỏ qua/lỗi: <b>'+err+'</b> dòng (đỏ).'):'')+'</div>';
    $btn.disabled=ok===0;
  }
  function esc(s){ var d=document.createElement('div'); d.textContent=(s==null?'':s); return d.innerHTML; }

  $btn.addEventListener('click', function(){
    if(!validRows.length) return;
    $btn.disabled=true; $res.textContent='Đang nhập...'; $res.style.color='#64748b';
    fetch('<?= base_url('customers/importBulk') ?>',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({rows:validRows})})
      .then(function(r){return r.json();}).then(function(d){
        if(!d.ok){ $res.textContent='Lỗi: '+(d.error||'?'); $res.style.color='#dd4b39'; $btn.disabled=false; return; }
        $res.style.color='#00a65a';
        $res.textContent='✓ Đã thêm '+d.added+' khách hàng'+(d.skipped?(', bỏ qua '+d.skipped+' (trùng)'):'')+(d.errors&&d.errors.length?(', '+d.errors.length+' lỗi'):'')+'.';
        if(d.errors&&d.errors.length) console.log('Import errors:',d.errors);
        setTimeout(function(){ window.location.href='<?= base_url('customers') ?>'; },2000);
      }).catch(function(e){ $res.textContent='Lỗi kết nối: '+e; $res.style.color='#dd4b39'; $btn.disabled=false; });
  });
})();
</script>
