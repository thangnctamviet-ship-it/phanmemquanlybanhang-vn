<?php $fmt = function($n){ return number_format((float)$n,0,',','.').'đ'; }; ?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Quản lý <small>Công nợ &amp; Phiếu thu chi</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
      <li class="active">Công nợ</li>
    </ol>
  </section>

  <section class="content">
    <div id="messages"></div>

    <!-- Tổng quan -->
    <div class="row">
      <div class="col-md-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3 style="font-size:22px;"><?= $fmt($total_customer_debt) ?></h3>
            <p>Phải thu (KH nợ)</p>
          </div>
          <div class="icon"><i class="fa fa-user-o"></i></div>
        </div>
      </div>
      <div class="col-md-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3 style="font-size:22px;"><?= $fmt($total_supplier_debt) ?></h3>
            <p>Phải trả (Nợ NCC)</p>
          </div>
          <div class="icon"><i class="fa fa-truck"></i></div>
        </div>
      </div>
      <?php foreach ($cash_accounts as $i => $ca): if ($i >= 2) break; ?>
        <div class="col-md-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3 style="font-size:22px;"><?= $fmt($ca['balance']) ?></h3>
              <p><?= htmlspecialchars($ca['name']) ?></p>
            </div>
            <div class="icon"><i class="fa fa-money"></i></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="row">
      <!-- Khách hàng nợ -->
      <div class="col-md-6">
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user-o"></i> Khách hàng còn nợ</h3>
          </div>
          <div class="box-body no-padding">
            <?php if (empty($customers)): ?>
              <p style="padding:20px;text-align:center;color:#27ae60;">✓ Không có khách nào còn nợ.</p>
            <?php else: ?>
              <table class="table table-striped" style="margin-bottom:0;">
                <thead><tr><th>Tên</th><th>SĐT</th><th style="text-align:right;">Còn nợ</th><th width="100"></th></tr></thead>
                <tbody>
                  <?php foreach ($customers as $c): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                      <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                      <td style="text-align:right;color:#dc2626;"><strong><?= $fmt($c['debt']) ?></strong></td>
                      <td>
                        <button class="btn btn-success btn-xs" onclick="openCollect(<?= (int)$c['id'] ?>,'<?= htmlspecialchars(addslashes($c['name'])) ?>',<?= (float)$c['debt'] ?>)">
                          <i class="fa fa-money"></i> Thu nợ
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- NCC nợ -->
      <div class="col-md-6">
        <div class="box box-danger">
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-truck"></i> Phải trả nhà cung cấp</h3>
          </div>
          <div class="box-body no-padding">
            <?php if (empty($suppliers)): ?>
              <p style="padding:20px;text-align:center;color:#27ae60;">✓ Không nợ NCC nào.</p>
            <?php else: ?>
              <table class="table table-striped" style="margin-bottom:0;">
                <thead><tr><th>Tên NCC</th><th>SĐT</th><th style="text-align:right;">Phải trả</th><th width="100"></th></tr></thead>
                <tbody>
                  <?php foreach ($suppliers as $s): ?>
                    <tr>
                      <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                      <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
                      <td style="text-align:right;color:#dc2626;"><strong><?= $fmt($s['debt']) ?></strong></td>
                      <td>
                        <button class="btn btn-primary btn-xs" onclick="openPay(<?= (int)$s['id'] ?>,'<?= htmlspecialchars(addslashes($s['name'])) ?>',<?= (float)$s['debt'] ?>)">
                          <i class="fa fa-money"></i> Trả nợ
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Lịch sử phiếu thu/chi -->
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-history"></i> Lịch sử phiếu thu/chi (50 gần nhất)</h3>
        <div class="box-tools">
          <button class="btn btn-default btn-sm" onclick="openOther('in')"><i class="fa fa-plus text-success"></i> Phiếu thu khác</button>
          <button class="btn btn-default btn-sm" onclick="openOther('out')"><i class="fa fa-minus text-danger"></i> Phiếu chi khác</button>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered">
          <thead><tr><th>Thời gian</th><th>Loại</th><th>Đối tác</th><th>Số tiền</th><th>Ghi chú</th></tr></thead>
          <tbody>
            <?php if (empty($payments)): ?>
              <tr><td colspan="5" class="text-center text-muted">Chưa có phiếu nào.</td></tr>
            <?php else: foreach ($payments as $p):
              $kind_label = [
                'receive_customer' => '<span class="label label-success">Thu KH</span>',
                'pay_supplier'     => '<span class="label label-danger">Trả NCC</span>',
                'other_in'         => '<span class="label label-success">Thu khác</span>',
                'other_out'        => '<span class="label label-danger">Chi khác</span>',
              ][$p['kind']] ?? '?';
              $sign = in_array($p['kind'], ['receive_customer','other_in']) ? '+' : '−';
              $color = in_array($p['kind'], ['receive_customer','other_in']) ? '#059669' : '#dc2626';
            ?>
              <tr>
                <td><?= date('d/m H:i', strtotime($p['created_at'])) ?></td>
                <td><?= $kind_label ?></td>
                <td><?= htmlspecialchars($p['party_name'] ?: '—') ?></td>
                <td style="color:<?= $color ?>;font-weight:600;"><?= $sign ?><?= $fmt($p['amount']) ?></td>
                <td><?= htmlspecialchars($p['note']) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal ghi phiếu -->
<div class="modal fade" tabindex="-1" role="dialog" id="payModal"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="modalTitle">Phiếu</h4></div>
  <form id="payForm">
    <div class="modal-body">
      <input type="hidden" name="kind" id="f_kind">
      <input type="hidden" name="party_type" id="f_party_type">
      <input type="hidden" name="party_id" id="f_party_id">
      <div class="form-group" id="partyRow"><label>Đối tác</label><input type="text" id="f_party_name" class="form-control" readonly></div>
      <div class="form-group" id="debtRow"><label>Đang nợ</label><input type="text" id="f_current_debt" class="form-control" readonly></div>
      <div class="form-group"><label>Số tiền *</label><input type="number" name="amount" id="f_amount" class="form-control" min="0" required></div>
      <div class="form-group"><label>Vào quỹ</label>
        <select name="cash_account_id" class="form-control">
          <?php foreach ($cash_accounts as $ca): ?>
            <option value="<?= (int)$ca['id'] ?>"><?= htmlspecialchars($ca['name']) ?> (<?= $fmt($ca['balance']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Tham chiếu</label><input type="text" name="reference" class="form-control" placeholder="Mã đơn / chứng từ"></div>
      <div class="form-group"><label>Ghi chú</label><textarea name="note" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
      <button type="submit" class="btn btn-success" id="submitBtn">Lưu phiếu</button>
    </div>
  </form>
</div></div></div>

<script>
function openCollect(id, name, debt){
  $('#modalTitle').text('Thu nợ khách hàng');
  $('#f_kind').val('receive_customer'); $('#f_party_type').val('customer');
  $('#f_party_id').val(id); $('#f_party_name').val(name);
  $('#f_current_debt').val(new Intl.NumberFormat('vi-VN').format(debt) + 'đ');
  $('#f_amount').val(Math.round(debt));
  $('#partyRow,#debtRow').show();
  $('#payModal').modal('show');
}
function openPay(id, name, debt){
  $('#modalTitle').text('Trả nợ nhà cung cấp');
  $('#f_kind').val('pay_supplier'); $('#f_party_type').val('supplier');
  $('#f_party_id').val(id); $('#f_party_name').val(name);
  $('#f_current_debt').val(new Intl.NumberFormat('vi-VN').format(debt) + 'đ');
  $('#f_amount').val(Math.round(debt));
  $('#partyRow,#debtRow').show();
  $('#payModal').modal('show');
}
function openOther(dir){
  $('#modalTitle').text(dir === 'in' ? 'Phiếu thu khác' : 'Phiếu chi khác');
  $('#f_kind').val(dir === 'in' ? 'other_in' : 'other_out');
  $('#f_party_type').val('other'); $('#f_party_id').val(0);
  $('#f_amount').val(''); $('#partyRow,#debtRow').hide();
  $('#payModal').modal('show');
}

$('#payForm').on('submit', function(e){
  e.preventDefault();
  $('#submitBtn').prop('disabled', true).text('Đang lưu...');
  $.post('<?= base_url('debts/record') ?>', $(this).serialize(), function(res){
    var r = (typeof res === 'string') ? JSON.parse(res) : res;
    if (r.ok) location.reload();
    else { alert(r.error || 'Lỗi'); $('#submitBtn').prop('disabled', false).text('Lưu phiếu'); }
  }).fail(function(){
    alert('Lỗi kết nối'); $('#submitBtn').prop('disabled', false).text('Lưu phiếu');
  });
});
</script>
