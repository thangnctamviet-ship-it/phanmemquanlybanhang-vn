<?php $page_title='Thanh toán'; include __DIR__.'/includes/layout.php'; require_once __DIR__.'/../landing/includes/mailer.php';
$pdo = master_pdo();
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    $stmt = $pdo->prepare("SELECT p.*, t.subdomain, t.owner_email, t.expires_at FROM payments p JOIN tenants t ON t.id=p.tenant_id WHERE p.id=?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($p) {
        if ($action==='confirm') {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE payments SET status='confirmed', confirmed_at=NOW() WHERE id=?")->execute([$id]);
            $months   = (int)$p['months_added'];
            $branches = (int)$p['branches_added'];
            // Gói extra_branch CHỈ cộng số chi nhánh, không gia hạn license.
            // (months_added ở extra_branch là thời lượng thuê thêm chi nhánh,
            // không phải thời gian sử dụng phần mềm.)
            $isExtra = ($p['plan'] === 'extra_branch');
            if ($isExtra) {
                $pdo->prepare("UPDATE tenants SET paid_branches=paid_branches+?, status='active' WHERE id=?")
                    ->execute([$branches, $p['tenant_id']]);
            } else {
                $pdo->prepare("UPDATE tenants SET expires_at=DATE_ADD(GREATEST(expires_at,NOW()), INTERVAL {$months} MONTH), paid_branches=paid_branches+?, status='active', plan=? WHERE id=?")
                    ->execute([$branches, $p['plan'], $p['tenant_id']]);
            }
            $pdo->commit();
            $msg = "Đã xác nhận thanh toán #$id";
            $stmt = $pdo->prepare("SELECT expires_at, paid_branches FROM tenants WHERE id=?");
            $stmt->execute([$p['tenant_id']]);
            $tenant_after = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tenant_after) {
                $body = '<p>Thanh toán của bạn đã được xác nhận.</p>'
                    . '<p>Tenant: <strong>'.htmlspecialchars($p['subdomain'], ENT_QUOTES, 'UTF-8').'</strong></p>'
                    . '<p>Hạn mới: <strong>'.htmlspecialchars($tenant_after['expires_at'], ENT_QUOTES, 'UTF-8').'</strong><br>'
                    . 'Số chi nhánh: <strong>'.(int)$tenant_after['paid_branches'].'</strong></p>';
                send_mail($p['owner_email'], 'Đã xác nhận thanh toán', $body);
            }
        } elseif ($action==='reject') {
            $pdo->prepare("UPDATE payments SET status='rejected' WHERE id=?")->execute([$id]);
            $msg="Đã từ chối #$id";
        }
    }
}
$rows = $pdo->query("SELECT p.*, t.subdomain FROM payments p JOIN tenants t ON t.id=p.tenant_id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 class="text-2xl font-bold mb-4">Thanh toán</h1>
<?php if($msg): ?><div class="bg-emerald-50 text-emerald-700 p-3 rounded mb-4"><?= $msg ?></div><?php endif; ?>
<div class="bg-white rounded-xl shadow overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-slate-50"><tr>
<th class="p-2">#</th><th class="p-2 text-left">Tenant</th><th class="p-2">Plan</th><th class="p-2">Số tiền</th>
<th class="p-2">+Tháng</th><th class="p-2">+CN</th><th class="p-2">Bank ref</th><th class="p-2">Status</th><th class="p-2">Ngày</th><th></th>
</tr></thead><tbody>
<?php foreach($rows as $r): ?>
<tr class="border-t">
<td class="p-2 text-center"><?= $r['id'] ?></td>
<td class="p-2 font-mono"><?= htmlspecialchars($r['subdomain']) ?></td>
<td class="p-2 text-center"><?= $r['plan'] ?></td>
<td class="p-2 text-right"><?= number_format($r['amount']) ?>₫</td>
<td class="p-2 text-center"><?= $r['months_added'] ?></td>
<td class="p-2 text-center"><?= $r['branches_added'] ?></td>
<td class="p-2 text-xs"><?= htmlspecialchars($r['bank_ref'] ?? '') ?></td>
<td class="p-2 text-center"><span class="px-2 py-1 rounded text-xs <?= $r['status']==='pending'?'bg-amber-100 text-amber-700':($r['status']==='confirmed'?'bg-emerald-100 text-emerald-700':'bg-slate-200') ?>"><?= $r['status'] ?></span></td>
<td class="p-2 text-xs"><?= $r['created_at'] ?></td>
<td class="p-2 space-x-1">
  <?php if($r['status']==='pending'): ?>
  <form method="POST" class="inline-block">
    <input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="action" value="confirm">
    <button class="bg-emerald-600 text-white px-2 py-1 rounded text-xs">Xác nhận</button>
  </form>
  <form method="POST" class="inline-block">
    <input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="action" value="reject">
    <button class="bg-red-600 text-white px-2 py-1 rounded text-xs">Từ chối</button>
  </form>
  <?php endif; ?>
</td>
</tr>
<?php endforeach; if (!$rows): ?>
<tr><td colspan="10" class="p-6 text-center text-slate-500">Chưa có thanh toán nào</td></tr>
<?php endif; ?>
</tbody></table></div>
</main></body></html>
