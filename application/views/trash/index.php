<div class="content-wrapper"><section class="content" style="padding:20px;">
  <h2>🗑️ Thùng rác</h2>
  <p class="text-muted">Các record đã bị xoá mềm. Khôi phục hoặc xoá vĩnh viễn tại đây.</p>

  <?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
  <?php endif; ?>
  <?php if($this->session->flashdata('errors')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('errors') ?></div>
  <?php endif; ?>

  <!-- Tab chuyển giữa các bảng -->
  <ul class="nav nav-tabs" style="margin-bottom:14px;">
    <?php foreach($tables as $tbl => $meta): ?>
      <li class="<?= $table===$tbl?'active':'' ?>">
        <a href="<?= site_url('Trash/index/'.$tbl) ?>"><?= htmlspecialchars($meta['name']) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="box box-default">
    <div class="box-body table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th width="60">ID</th>
            <?php foreach($table_meta['cols'] as $col): ?>
              <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
            <th width="160">Thời gian xoá</th>
            <th width="200">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($rows)): ?>
            <tr><td colspan="<?= count($table_meta['cols'])+3 ?>" class="text-center text-muted" style="padding:30px;">
              Thùng rác trống.
            </td></tr>
          <?php else: foreach($rows as $r): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <?php foreach($table_meta['cols'] as $col): ?>
                <td><?= htmlspecialchars((string)($r[$col] ?? '—')) ?></td>
              <?php endforeach; ?>
              <td><small><?= htmlspecialchars($r['deleted_at']) ?></small></td>
              <td>
                <a href="<?= site_url('Trash/restore/'.$table.'/'.$r['id']) ?>"
                   class="btn btn-xs btn-success"
                   onclick="return confirm('Khôi phục record này?')">
                  ↩ Khôi phục
                </a>
                <a href="<?= site_url('Trash/forceDelete/'.$table.'/'.$r['id']) ?>"
                   class="btn btn-xs btn-danger"
                   onclick="return confirm('⚠ Xoá VĨNH VIỄN? Không thể khôi phục lại!')">
                  🗑 Xoá vĩnh viễn
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section></div>
