<div class="content-wrapper">
  <section class="content-header">
    <h1>Nhập sản phẩm từ Excel</h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li><a href="<?= base_url('products') ?>">Sản phẩm</a></li>
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
            <li><b>Điền hoặc copy-paste</b> dữ liệu sản phẩm vào (cột có dấu <b style="color:#dd4b39;">*</b> là bắt buộc). Xóa 2 dòng ví dụ.</li>
            <li><b>Chọn file</b> đã điền → xem trước → bấm <b>Xác nhận nhập</b>.</li>
          </ol>
        </div>

        <div class="row" style="margin-bottom:15px;">
          <div class="col-sm-4">
            <label>Bước 1 — Tải file mẫu</label><br>
            <a href="<?= base_url('products/importTemplate') ?>" class="btn btn-default"><i class="fa fa-download"></i> Tải file mẫu Excel</a>
          </div>
          <div class="col-sm-4">
            <label>Cửa hàng nhập vào</label>
            <select id="impStore" class="form-control">
              <?php foreach (($stores ?? array()) as $s): ?>
                <option value="<?= (int)$s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
              <?php endforeach; ?>
            </select>
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
              <tr>
                <th>#</th><th>Tên SP *</th><th>SKU *</th><th>Giá *</th><th>SL *</th>
                <th>Barcode</th><th>ĐVT</th><th>Giá vốn</th><th>Giá sỉ</th><th>Danh mục</th><th>Thương hiệu</th><th>Trạng thái</th>
              </tr>
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
  var validRows = [];
  var $file = document.getElementById('impFile');
  var $body = document.getElementById('impPreviewBody');
  var $wrap = document.getElementById('impPreviewWrap');
  var $sum  = document.getElementById('impSummary');
  var $btn  = document.getElementById('impConfirm');
  var $res  = document.getElementById('impResult');

  // map tiêu đề cột (chuẩn hoá: bỏ dấu *, lowercase, bỏ dấu tiếng Việt) -> field
  function norm(s){ return (s||'').toString().toLowerCase().replace(/\*/g,'').trim()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d'); }
  var HMAP = {
    'ten san pham':'name','ten':'name','san pham':'name',
    'sku / ma hang':'sku','sku':'sku','ma hang':'sku','ma':'sku',
    'gia ban':'price','gia':'price',
    'so luong ton':'qty','so luong':'qty','ton':'qty','sl':'qty','ton kho':'qty',
    'ma vach (barcode)':'barcode','ma vach':'barcode','barcode':'barcode',
    'don vi tinh':'unit','dvt':'unit','don vi':'unit',
    'gia von':'cost_price','gia si':'wholesale_price',
    'danh muc':'category','nhom':'category','loai':'category',
    'thuong hieu':'brand','hang':'brand',
    'ton toi thieu':'min_stock','mo ta':'description'
  };

  $file.addEventListener('change', function(e){
    var f = e.target.files[0]; if(!f) return;
    $res.textContent=''; 
    var reader = new FileReader();
    reader.onload = function(ev){
      var wb = XLSX.read(new Uint8Array(ev.target.result), {type:'array'});
      var ws = wb.Sheets[wb.SheetNames[0]];
      var aoa = XLSX.utils.sheet_to_json(ws, {header:1, defval:''});
      parseRows(aoa);
    };
    reader.readAsArrayBuffer(f);
  });

  function parseRows(aoa){
    if(!aoa.length){ alert('File rỗng'); return; }
    // tìm dòng header (dòng đầu có chữ)
    var header = aoa[0].map(norm);
    var colIdx = {};
    header.forEach(function(h,i){ if(HMAP[h]!==undefined && colIdx[HMAP[h]]===undefined) colIdx[HMAP[h]]=i; });
    if(colIdx.name===undefined || colIdx.sku===undefined){
      alert('Không nhận diện được cột "Tên sản phẩm" và "SKU". Hãy dùng đúng file mẫu.'); return;
    }
    validRows = []; var html=''; var okCount=0, errCount=0, seen={};
    for(var r=1;r<aoa.length;r++){
      var row=aoa[r]; if(!row || row.every(function(c){return (''+c).trim()==='';})) continue;
      function g(k){ return colIdx[k]!==undefined ? (''+row[colIdx[k]]).trim() : ''; }
      var o={name:g('name'),sku:g('sku'),price:g('price'),qty:g('qty'),barcode:g('barcode'),
             unit:g('unit'),cost_price:g('cost_price'),wholesale_price:g('wholesale_price'),
             category:g('category'),brand:g('brand'),min_stock:g('min_stock'),description:g('description')};
      var err=[];
      if(!o.name) err.push('thiếu Tên');
      if(!o.sku) err.push('thiếu SKU');
      if(o.price==='' || isNaN(parseFloat(o.price.replace(/[^\d.,\-]/g,'').replace(/\./g,'').replace(',','.')))) {
        if(o.price!=='') err.push('Giá sai');
      }
      var skl=o.sku.toLowerCase();
      if(o.sku && seen[skl]) err.push('trùng SKU trong file');
      if(o.sku) seen[skl]=true;

      var ok = err.length===0;
      if(ok){ validRows.push(o); okCount++; } else errCount++;
      var color = ok ? '' : 'background:#fee2e2;';
      html += '<tr style="'+color+'"><td>'+r+'</td><td>'+esc(o.name)+'</td><td>'+esc(o.sku)+'</td>'
        +'<td>'+esc(o.price)+'</td><td>'+esc(o.qty)+'</td><td>'+esc(o.barcode)+'</td><td>'+esc(o.unit)+'</td>'
        +'<td>'+esc(o.cost_price)+'</td><td>'+esc(o.wholesale_price)+'</td><td>'+esc(o.category)+'</td><td>'+esc(o.brand)+'</td>'
        +'<td>'+(ok?'<span class="label label-success">OK</span>':'<span class="label label-danger">'+err.join(', ')+'</span>')+'</td></tr>';
    }
    $body.innerHTML=html; $wrap.style.display='block';
    $sum.style.display='block';
    $sum.innerHTML='<div class="alert '+(okCount?'alert-success':'alert-warning')+'" style="padding:8px 12px;">'
      +'Sẵn sàng nhập: <b>'+okCount+'</b> sản phẩm. '+(errCount?('Bỏ qua/lỗi: <b>'+errCount+'</b> dòng (đỏ).'):'')+'</div>';
    $btn.disabled = okCount===0;
  }

  function esc(s){ var d=document.createElement('div'); d.textContent=(s==null?'':s); return d.innerHTML; }

  $btn.addEventListener('click', function(){
    if(!validRows.length) return;
    $btn.disabled=true; $res.textContent='Đang nhập...'; $res.style.color='#64748b';
    fetch('<?= base_url('products/importBulk') ?>', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({store_id: document.getElementById('impStore').value, rows: validRows})
    }).then(function(r){return r.json();}).then(function(d){
      if(!d.ok){ $res.textContent='Lỗi: '+(d.error||'?'); $res.style.color='#dd4b39'; $btn.disabled=false; return; }
      $res.style.color='#00a65a';
      $res.textContent='✓ Đã thêm '+d.added+' sản phẩm'+(d.skipped?(', bỏ qua '+d.skipped+' (trùng)'):'')+(d.errors&&d.errors.length?(', '+d.errors.length+' lỗi'):'')+'.';
      if(d.errors && d.errors.length) console.log('Import errors:', d.errors);
      setTimeout(function(){ window.location.href='<?= base_url('products') ?>'; }, 2000);
    }).catch(function(e){ $res.textContent='Lỗi kết nối: '+e; $res.style.color='#dd4b39'; $btn.disabled=false; });
  });
})();
</script>
