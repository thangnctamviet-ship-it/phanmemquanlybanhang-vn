<?php /* Quick-Buy Modal — include vào view nào cần. Yêu cầu jQuery + Bootstrap modal */ ?>
<div class="modal fade" tabindex="-1" role="dialog" id="quickBuyModal" data-backdrop="static">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="qbCloseBtn"><span>&times;</span></button>
      <h4 class="modal-title"><i class="fa fa-credit-card"></i> Thanh toán nhanh</h4>
    </div>

    <!-- BƯỚC 1: Chọn gói -->
    <div class="modal-body" id="qbStep1">
      <h5 style="margin-top:0;color:#4f46e5;"><i class="fa fa-plus-circle"></i> Bạn muốn mua gì?</h5>

      <div class="row" id="qbBranchSection">
        <div class="col-md-12">
          <div class="box box-warning" style="margin-bottom:14px;">
            <div class="box-header with-border"><h3 class="box-title">Mua thêm chi nhánh</h3></div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4 form-group">
                  <label>Số chi nhánh thêm</label>
                  <input type="number" id="qbQty" class="form-control" value="1" min="1" max="50">
                  <small class="text-muted">50.000đ / chi nhánh / tháng</small>
                </div>
                <div class="col-md-4 form-group">
                  <label>Thời gian</label>
                  <select id="qbDuration" class="form-control">
                    <option value="1">1 tháng (giá gốc)</option>
                    <option value="6">6 tháng (-17%)</option>
                    <option value="12">12 tháng (-25%)</option>
                  </select>
                </div>
                <div class="col-md-4 form-group">
                  <label>Tạm tính</label>
                  <input type="text" id="qbExtraTotal" class="form-control" readonly value="50.000đ">
                </div>
              </div>
              <button class="btn btn-warning btn-block" data-plan="extra_branch"><i class="fa fa-building"></i> Mua thêm chi nhánh</button>
            </div>
          </div>
        </div>
      </div>

      <h5 style="color:#64748b;"><i class="fa fa-calendar"></i> Hoặc gia hạn gói chính</h5>
      <div class="row">
        <div class="col-md-4">
          <div class="info-box bg-aqua" style="cursor:pointer;" data-plan="monthly">
            <span class="info-box-icon"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">1 tháng</span>
              <span class="info-box-number" style="font-size:18px;">120.000đ</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box bg-yellow" style="cursor:pointer;" data-plan="semiannual">
            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">6 tháng <small>(tiết kiệm 17%)</small></span>
              <span class="info-box-number" style="font-size:18px;">600.000đ</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box bg-green" style="cursor:pointer;" data-plan="annual">
            <span class="info-box-icon"><i class="fa fa-star"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">12 tháng <small>(tiết kiệm 25%)</small></span>
              <span class="info-box-number" style="font-size:18px;">1.100.000đ</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- BƯỚC 2: QR + chờ -->
    <div class="modal-body" id="qbStep2" style="display:none;">
      <div class="row">
        <div class="col-md-6 text-center">
          <h5 style="color:#4f46e5;" id="qbLabel"></h5>
          <div id="qbQrWrap">
            <img id="qbQrImg" src="" style="max-width:280px;border:1px solid #eee;padding:8px;background:white;border-radius:8px;">
            <p style="font-size:12px;color:#777;">Mở app ngân hàng → quét QR</p>
          </div>
        </div>
        <div class="col-md-6">
          <p><strong>Hoặc chuyển khoản thủ công:</strong></p>
          <table class="table table-condensed" style="background:#f8fafc;">
            <tr><td>Ngân hàng:</td><td><strong id="qbBankName"></strong></td></tr>
            <tr><td>Số TK:</td><td><code id="qbBankAcc" style="font-size:14px;"></code></td></tr>
            <tr><td>Chủ TK:</td><td><strong id="qbBankHolder"></strong></td></tr>
            <tr><td>Số tiền:</td><td style="color:#dc2626;font-size:18px;font-weight:bold;"><span id="qbAmount"></span></td></tr>
            <tr><td>Nội dung CK:</td><td><code id="qbRef" style="background:#fef3c7;padding:4px 8px;font-size:13px;display:inline-block;"></code></td></tr>
          </table>

          <div id="qbWaiting" class="alert alert-info">
            <i class="fa fa-spinner fa-spin"></i> Đang chờ xác nhận thanh toán...
            <p style="font-size:12px;margin:6px 0 0;">Hệ thống tự động kích hoạt ngay khi nhận được CK. Bạn có thể đóng popup, tài khoản sẽ được cộng tự động.</p>
          </div>
          <div id="qbConfirmed" class="alert alert-success" style="display:none;">
            <i class="fa fa-check-circle"></i> <strong>Thanh toán đã được xác nhận!</strong>
            <p style="margin:6px 0 0;">Trang sẽ tự reload để cập nhật...</p>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" id="qbBack" style="display:none;">← Chọn gói khác</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
    </div>
  </div></div>
</div>

<script>
(function(){
  var pollTimer = null;
  function fmt(n){ return new Intl.NumberFormat('vi-VN').format(n) + 'đ'; }

  function updateExtraTotal(){
    var qty = parseInt($('#qbQty').val(),10) || 1;
    var dur = parseInt($('#qbDuration').val(),10) || 1;
    var disc = dur === 6 ? 0.17 : (dur === 12 ? 0.25 : 0);
    var base = 50000 * qty * dur;
    var total = Math.round(base * (1 - disc) / 1000) * 1000;
    $('#qbExtraTotal').val(fmt(total));
  }
  $('#qbQty, #qbDuration').on('input change', updateExtraTotal);
  updateExtraTotal();

  $('#quickBuyModal').on('click', '[data-plan]', function(){
    var plan = $(this).data('plan');
    var payload = { plan: plan };
    if (plan === 'extra_branch') {
      payload.qty = parseInt($('#qbQty').val(),10) || 1;
      payload.duration = parseInt($('#qbDuration').val(),10) || 1;
    }
    $.post('<?= base_url('license/quickBuy') ?>', payload, function(res){
      var r = (typeof res === 'string') ? JSON.parse(res) : res;
      if (!r.ok) { alert(r.error || 'Lỗi'); return; }
      $('#qbLabel').text(r.label);
      $('#qbBankName').text(r.bank.name || '—');
      $('#qbBankAcc').text(r.bank.account || '—');
      $('#qbBankHolder').text(r.bank.holder || '—');
      $('#qbAmount').text(fmt(r.amount));
      $('#qbRef').text(r.ref);
      if (r.qr_url) { $('#qbQrImg').attr('src', r.qr_url); $('#qbQrWrap').show(); }
      else { $('#qbQrWrap').hide(); }
      $('#qbStep1').hide(); $('#qbStep2').show(); $('#qbBack').show();
      startPolling(r.payment_id);
    });
  });

  $('#qbBack').on('click', function(){
    $('#qbStep2').hide(); $('#qbStep1').show(); $(this).hide();
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
  });

  function startPolling(id){
    if (!id) return;
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(function(){
      $.getJSON('<?= base_url('license/checkStatus/') ?>'+id, function(d){
        if (d.status === 'confirmed') {
          clearInterval(pollTimer); pollTimer = null;
          $('#qbWaiting').hide();
          $('#qbConfirmed').show();
          setTimeout(function(){ location.reload(); }, 2200);
        }
      });
    }, 5000);
  }

  $('#quickBuyModal').on('hidden.bs.modal', function(){
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    $('#qbStep2').hide(); $('#qbStep1').show(); $('#qbBack').hide();
  });

  // Public helper
  window.openQuickBuy = function(presetPlan){
    if (presetPlan === 'extra_branch') {
      // scroll to branch section
      setTimeout(function(){ $('#qbQty').focus(); }, 300);
    }
    $('#quickBuyModal').modal('show');
  };
})();
</script>
