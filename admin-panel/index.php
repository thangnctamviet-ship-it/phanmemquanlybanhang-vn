<?php $page_title='Dashboard'; include __DIR__.'/includes/layout.php';
$pdo = master_pdo();

$total = (int)$pdo->query("SELECT COUNT(*) FROM tenants")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM tenants WHERE status='active'")->fetchColumn();
$trial = (int)$pdo->query("SELECT COUNT(*) FROM tenants WHERE status='trial'")->fetchColumn();

// MRR — sum payments 'confirmed' in last 30d, normalize by plan
$mrr = 0.0;
try {
    $rows = $pdo->query("SELECT plan, SUM(amount) AS s FROM payments WHERE status='confirmed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY plan")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $div = 1;
        if ($r['plan'] === 'semiannual') $div = 6;
        elseif ($r['plan'] === 'annual') $div = 12;
        $mrr += ((float)$r['s']) / $div;
    }
} catch (Exception $e) { $mrr = 0; }

// Expiring within 7 days
$expiring = $pdo->query("SELECT subdomain, expires_at, plan, DATEDIFF(expires_at, NOW()) AS days_left FROM tenants WHERE status IN ('active','trial') AND expires_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) ORDER BY expires_at ASC")->fetchAll(PDO::FETCH_ASSOC);

// Growth last 30 days
$growth = $pdo->query("SELECT DATE(created_at) d, COUNT(*) c FROM tenants WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(created_at)")->fetchAll(PDO::FETCH_KEY_PAIR);
$labels = []; $values = [];
for ($i=29; $i>=0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $labels[] = date('d/m', strtotime($d));
    $values[] = (int)($growth[$d] ?? 0);
}

// Recent pending payments
$pending = [];
try {
    $pending = $pdo->query("SELECT p.*, t.subdomain FROM payments p LEFT JOIN tenants t ON t.id=p.tenant_id WHERE p.status='pending' ORDER BY p.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

function vnd($n){ return number_format((float)$n, 0, ',', '.') . 'đ'; }
?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-lg shadow p-5">
    <div class="text-slate-500 text-sm">Tổng tenant</div>
    <div class="text-3xl font-bold text-slate-800 mt-1"><?= $total ?></div>
  </div>
  <div class="bg-white rounded-lg shadow p-5">
    <div class="text-slate-500 text-sm">Tenant active</div>
    <div class="text-3xl font-bold text-emerald-600 mt-1"><?= $active ?></div>
  </div>
  <div class="bg-white rounded-lg shadow p-5">
    <div class="text-slate-500 text-sm">Tenant trial</div>
    <div class="text-3xl font-bold text-amber-500 mt-1"><?= $trial ?></div>
  </div>
  <div class="bg-white rounded-lg shadow p-5">
    <div class="text-slate-500 text-sm">MRR (30 ngày)</div>
    <div class="text-2xl font-bold text-indigo-600 mt-1"><?= vnd($mrr) ?></div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
  <div class="bg-white rounded-lg shadow p-5">
    <h2 class="font-semibold mb-3">Tenant sắp hết hạn (7 ngày)</h2>
    <table class="w-full text-sm">
      <thead><tr class="text-left text-slate-500 border-b"><th class="py-2">Subdomain</th><th>Hết hạn</th><th>Còn lại</th><th>Gói</th></tr></thead>
      <tbody>
      <?php if (!$expiring): ?>
        <tr><td colspan="4" class="py-4 text-slate-400">Không có</td></tr>
      <?php else: foreach ($expiring as $e): ?>
        <tr class="border-b">
          <td class="py-2"><?= htmlspecialchars($e['subdomain']) ?></td>
          <td><?= htmlspecialchars($e['expires_at']) ?></td>
          <td><?= (int)$e['days_left'] ?> ngày</td>
          <td><?= htmlspecialchars($e['plan']) ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="bg-white rounded-lg shadow p-5">
    <h2 class="font-semibold mb-3">Tăng trưởng 30 ngày</h2>
    <canvas id="growthChart" height="120"></canvas>
  </div>
</div>

<div class="bg-white rounded-lg shadow p-5">
  <h2 class="font-semibold mb-3">Thanh toán pending mới nhất</h2>
  <table class="w-full text-sm">
    <thead><tr class="text-left text-slate-500 border-b"><th class="py-2">Tenant</th><th>Gói</th><th>Số tiền</th><th>Ref</th><th>Tạo lúc</th></tr></thead>
    <tbody>
    <?php if (!$pending): ?>
      <tr><td colspan="5" class="py-4 text-slate-400">Không có</td></tr>
    <?php else: foreach ($pending as $p): ?>
      <tr class="border-b">
        <td class="py-2"><?= htmlspecialchars($p['subdomain'] ?? '-') ?></td>
        <td><?= htmlspecialchars($p['plan'] ?? '') ?></td>
        <td><?= vnd($p['amount'] ?? 0) ?></td>
        <td class="text-xs font-mono"><?= htmlspecialchars($p['ref'] ?? '') ?></td>
        <td><?= htmlspecialchars($p['created_at'] ?? '') ?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('growthChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
      label: 'Tenant mới',
      data: <?= json_encode($values) ?>,
      borderColor: '#6366f1',
      backgroundColor: 'rgba(99,102,241,0.15)',
      fill: true, tension: 0.3
    }]
  },
  options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});
</script>
</main></body></html>
