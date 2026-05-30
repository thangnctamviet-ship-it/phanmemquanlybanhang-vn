<div class="content-wrapper"><section class="content" style="padding:20px;">
  <h2>📜 Nhật ký hệ thống (Audit Log)</h2>
  <p class="text-muted">Mọi hành động tạo/sửa/xoá quan trọng đều được ghi lại tại đây. Hiển thị 200 dòng gần nhất.</p>

  <!-- Filter -->
  <form method="GET" action="<?= site_url('AuditLog') ?>" class="box box-default" style="padding:14px;">
    <div class="row">
      <div class="col-md-2">
        <label>Hành động</label>
        <select name="action" class="form-control">
          <option value="">— Tất cả —</option>
          <?php foreach($actions as $a): if(!$a['action']) continue; ?>
          <option value="<?= htmlspecialchars($a['action']) ?>" <?= ($filters['action']??'')===$a['action']?'selected':'' ?>>
            <?= htmlspecialchars($a['action']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label>Đối tượng</label>
        <select name="entity" class="form-control">
          <option value="">— Tất cả —</option>
          <?php foreach($entities as $e): if(!$e['entity_type']) continue; ?>
          <option value="<?= htmlspecialchars($e['entity_type']) ?>" <?= ($filters['entity']??'')===$e['entity_type']?'selected':'' ?>>
            <?= htmlspecialchars($e['entity_type']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label>User ID</label>
        <input type="number" name="user_id" value="<?= htmlspecialchars($filters['user_id']?:'') ?>" class="form-control" placeholder="VD: 1">
      </div>
      <div class="col-md-2">
        <label>Từ ngày</label>
        <input type="date" name="from" value="<?= htmlspecialchars($filters['date_from']?:'') ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label>Đến ngày</label>
        <input type="date" name="to" value="<?= htmlspecialchars($filters['date_to']?:'') ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label>&nbsp;</label>
        <button class="btn btn-primary form-control">🔍 Lọc</button>
      </div>
    </div>
  </form>

  <div class="box box-default">
    <div class="box-body table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th width="60">ID</th>
            <th width="150">Thời gian</th>
            <th width="150">Người dùng</th>
            <th width="100">Hành động</th>
            <th width="100">Đối tượng</th>
            <th width="60">ID đối tượng</th>
            <th width="120">IP</th>
            <th>Dữ liệu</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="8" class="text-center text-muted" style="padding:30px;">Chưa có nhật ký nào khớp với bộ lọc.</td></tr>
          <?php else: foreach($rows as $r):
            $badge = 'default';
            $a = strtolower($r['action']);
            if (strpos($a,'create')!==false || $a==='pos_sale') $badge = 'success';
            elseif (strpos($a,'update')!==false) $badge = 'warning';
            elseif (strpos($a,'delete')!==false || strpos($a,'void')!==false) $badge = 'danger';
          ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><small><?= $r['created_at'] ?></small></td>
              <td>
                <?php if($r['user_id']): ?>
                  #<?= $r['user_id'] ?> <?= htmlspecialchars(trim(($r['firstname']??'').' '.($r['lastname']??''))) ?>
                  <?php if($r['username']): ?><br><small class="text-muted"><?= htmlspecialchars($r['username']) ?></small><?php endif; ?>
                <?php else: ?>
                  <span class="text-muted">guest</span>
                <?php endif; ?>
              </td>
              <td><span class="label label-<?= $badge ?>"><?= htmlspecialchars($r['action']) ?></span></td>
              <td><code><?= htmlspecialchars($r['entity_type']) ?></code></td>
              <td><?= $r['entity_id'] ?: '—' ?></td>
              <td><small><?= htmlspecialchars($r['ip'] ?: '') ?></small></td>
              <td>
                <?php if($r['old_data'] || $r['new_data']): ?>
                  <button type="button" class="btn btn-xs btn-default" onclick="toggleDiff(this)">Xem chi tiết</button>
                  <div class="audit-diff" style="display:none;margin-top:6px;">
                    <?php if($r['old_data']): ?>
                      <div><strong>Trước:</strong></div>
                      <pre style="max-height:200px;overflow:auto;background:#fef2f2;font-size:11px;"><?= htmlspecialchars($r['old_data']) ?></pre>
                    <?php endif; ?>
                    <?php if($r['new_data']): ?>
                      <div><strong>Sau:</strong></div>
                      <pre style="max-height:200px;overflow:auto;background:#f0fdf4;font-size:11px;"><?= htmlspecialchars($r['new_data']) ?></pre>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section></div>

<script>
function toggleDiff(btn){
  var d = btn.nextElementSibling;
  if(d.style.display==='none'){ d.style.display='block'; btn.textContent='Ẩn'; }
  else { d.style.display='none'; btn.textContent='Xem chi tiết'; }
}
</script>
