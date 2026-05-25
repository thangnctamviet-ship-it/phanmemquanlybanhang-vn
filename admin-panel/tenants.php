<?php $page_title='Tenants'; include __DIR__.'/includes/layout.php';
$pdo = master_pdo();
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action==='extend') {
        $months = max(1,(int)$_POST['months']);
        $pdo->prepare("UPDATE tenants SET expires_at=DATE_ADD(GREATEST(expires_at,NOW()), INTERVAL ? MONTH), status='active', plan=? WHERE id=?")
            ->execute([$months, $_POST['plan']??'monthly', $id]);
        $msg = "Đã gia hạn +$months tháng";
    } elseif ($action==='add_branch') {
        $n = max(1,(int)$_POST['branches']);
        $pdo->prepare("UPDATE tenants SET paid_branches=paid_branches+? WHERE id=?")->execute([$n,$id]);
        $msg = "Đã thêm $n chi nhánh";
    } elseif ($action==='suspend') {
        $pdo->prepare("UPDATE tenants SET status='suspended' WHERE id=?")->execute([$id]);
        $msg = "Đã suspend";
    } elseif ($action==='activate') {
        $pdo->prepare("UPDATE tenants SET status='active' WHERE id=?")->execute([$id]);
        $msg = "Đã kích hoạt";
    } elseif ($action==='reset_password') {
        // Reset password trong DB tenant
        $tenant = $pdo->query("SELECT * FROM tenants WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
        if ($tenant) {
            $newPass = bin2hex(random_bytes(4));
            $hash = password_hash($newPass, PASSWORD_BCRYPT);
            $env = env_load();
            try {
                $t = new PDO("mysql:host={$env['MASTER_DB_HOST']};dbname={$tenant['db_name']};charset=utf8mb4",
                    $env['MASTER_DB_USER'], $env['MASTER_DB_PASS']);
                $t->prepare("UPDATE users SET password=? WHERE username='admin'")->execute([$hash]);
                $msg = "Mật khẩu mới cho {$tenant['subdomain']}: <code>$newPass</code>";
            } catch (Exception $e) { $msg = 'Lỗi reset: '.$e->getMessage(); }
        }
    }
}

$tenants = $pdo->query("SELECT * FROM tenants ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h1 class="text-2xl font-bold mb-4">Danh sách tenant</h1>
<?php if($msg): ?><div class="bg-emerald-50 text-emerald-700 p-3 rounded mb-4"><?= $msg ?></div><?php endif; ?>
<div class="bg-white rounded-xl shadow overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-slate-50 text-slate-600"><tr>
  <th class="p-2 text-left">#</th><th class="p-2 text-left">Subdomain</th><th class="p-2 text-left">Cửa hàng</th>
  <th class="p-2 text-left">Email</th><th class="p-2">Status</th><th class="p-2">Plan</th>
  <th class="p-2">CN</th><th class="p-2">Hết hạn</th><th class="p-2">Hành động</th>
</tr></thead><tbody>
<?php foreach($tenants as $t): $expired = strtotime($t['expires_at'])<time(); ?>
<tr class="border-t">
  <td class="p-2"><?= $t['id'] ?></td>
  <td class="p-2 font-mono"><?= htmlspecialchars($t['subdomain']) ?></td>
  <td class="p-2"><?= htmlspecialchars($t['shop_name']) ?></td>
  <td class="p-2 text-xs"><?= htmlspecialchars($t['owner_email']) ?></td>
  <td class="p-2 text-center"><span class="px-2 py-1 rounded text-xs <?= $t['status']==='trial'?'bg-amber-100 text-amber-700':($t['status']==='active'?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700') ?>"><?= $t['status'] ?></span></td>
  <td class="p-2 text-center"><?= $t['plan'] ?></td>
  <td class="p-2 text-center"><?= $t['paid_branches'] ?></td>
  <td class="p-2 text-xs <?= $expired?'text-red-600':'' ?>"><?= $t['expires_at'] ?></td>
  <td class="p-2 space-x-1 whitespace-nowrap">
    <form method="POST" class="inline-block">
      <input type="hidden" name="id" value="<?= $t['id'] ?>">
      <input type="hidden" name="action" value="extend">
      <select name="plan" class="border rounded text-xs"><option value="monthly">1th</option><option value="semiannual">6th</option><option value="annual">12th</option></select>
      <input type="number" name="months" value="1" min="1" class="w-12 border rounded text-xs">
      <button class="bg-indigo-600 text-white px-2 py-1 rounded text-xs">Gia hạn</button>
    </form>
    <form method="POST" class="inline-block">
      <input type="hidden" name="id" value="<?= $t['id'] ?>"><input type="hidden" name="action" value="add_branch">
      <input type="number" name="branches" value="1" min="1" class="w-10 border rounded text-xs">
      <button class="bg-cyan-600 text-white px-2 py-1 rounded text-xs">+CN</button>
    </form>
    <form method="POST" class="inline-block" onsubmit="return confirm('Reset password?')">
      <input type="hidden" name="id" value="<?= $t['id'] ?>"><input type="hidden" name="action" value="reset_password">
      <button class="bg-amber-600 text-white px-2 py-1 rounded text-xs">Reset PW</button>
    </form>
    <form method="POST" class="inline-block">
      <input type="hidden" name="id" value="<?= $t['id'] ?>">
      <input type="hidden" name="action" value="<?= $t['status']==='suspended'?'activate':'suspend' ?>">
      <button class="bg-slate-700 text-white px-2 py-1 rounded text-xs"><?= $t['status']==='suspended'?'Bật':'Khoá' ?></button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</main></body></html>
