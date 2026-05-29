<?php
$page_title = 'Migrations';
require_once __DIR__.'/includes/auth.php';
require_login();

$rootDir = dirname(__DIR__);
$migDir = $rootDir . '/migrations';
$files = glob($migDir . '/*.sql');
sort($files);

$action = $_POST['action'] ?? '';
$onlyTenant = trim($_POST['tenant'] ?? '');
$onlyFile = trim($_POST['file'] ?? '');
$logs = array();

if ($action === 'run') {
    require_once __DIR__.'/../landing/includes/db.php';
    $env = env_load();
    $mpdo = master_pdo();
    $tenants = $mpdo->query("SELECT subdomain, db_name, db_user, db_pass FROM tenants WHERE status IN ('trial','active','expired') ORDER BY subdomain")->fetchAll(PDO::FETCH_ASSOC);
    if ($onlyTenant !== '') {
        $tenants = array_values(array_filter($tenants, function($r) use($onlyTenant){ return $r['subdomain'] === $onlyTenant; }));
    }
    $filesToRun = $files;
    if ($onlyFile !== '') {
        $filesToRun = array_values(array_filter($files, function($f) use($onlyFile){ return strpos(basename($f), $onlyFile) === 0; }));
    }

    $logs[] = "Chạy " . count($filesToRun) . " migration trên " . count($tenants) . " tenant";
    foreach ($tenants as $t) {
        $logs[] = "→ {$t['subdomain']} (db={$t['db_name']})";
        try {
            $pdo = new PDO("mysql:host={$env['MASTER_DB_HOST']};dbname={$t['db_name']};charset=utf8mb4", $t['db_user'], $t['db_pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (Exception $e) {
            $logs[] = "   ❌ Connect fail: " . $e->getMessage();
            continue;
        }
        $pdo->exec("CREATE TABLE IF NOT EXISTS `_migrations` (`name` VARCHAR(128) NOT NULL PRIMARY KEY, `ran_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $done = array();
        foreach ($pdo->query("SELECT name FROM `_migrations`") as $r) $done[$r['name']] = 1;

        foreach ($filesToRun as $f) {
            $name = basename($f);
            if (isset($done[$name])) { $logs[] = "   ⊙ $name (đã chạy)"; continue; }
            $sql = file_get_contents($f);
            try {
                run_sql_with_delim($pdo, $sql);
                $pdo->prepare("INSERT INTO `_migrations` (name) VALUES (?)")->execute(array($name));
                $logs[] = "   ✓ $name";
            } catch (Exception $e) {
                $logs[] = "   ❌ $name: " . $e->getMessage();
            }
        }
    }
    $logs[] = "Xong.";
}

function run_sql_with_delim(PDO $pdo, $sql) {
    $stmts = array();
    $delim = ';';
    $buf = '';
    foreach (preg_split("/\r?\n/", $sql) as $line) {
        if (preg_match('/^\s*DELIMITER\s+(\S+)/i', $line, $m)) {
            if (trim($buf) !== '') $stmts[] = $buf;
            $buf = ''; $delim = $m[1]; continue;
        }
        $buf .= $line . "\n";
        if ($delim !== ';' && substr(rtrim($line), -strlen($delim)) === $delim) {
            $stmts[] = substr(rtrim($buf), 0, -strlen($delim));
            $buf = '';
        }
    }
    if (trim($buf) !== '') $stmts[] = $buf;

    $final = array();
    foreach ($stmts as $s) {
        if (preg_match('/CREATE\s+PROCEDURE|DROP\s+PROCEDURE|^\s*CALL\s+/im', $s)) {
            $final[] = $s;
        } else {
            foreach (explode(';', $s) as $p) if (trim($p) !== '') $final[] = $p;
        }
    }
    foreach ($final as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || preg_match('/^--/', $stmt)) continue;
        // Remove inline -- comments at start of lines
        $clean = preg_replace('/^\s*--.*$/m', '', $stmt);
        if (trim($clean) === '') continue;
        $pdo->exec($clean);
    }
}

include __DIR__.'/includes/layout.php';
?>

<h1 class="text-2xl font-bold mb-4">🗃 Database Migrations</h1>

<div class="bg-white rounded-lg shadow p-6 mb-6">
  <h2 class="text-lg font-semibold mb-3">Migration files</h2>
  <ul class="text-sm font-mono space-y-1">
    <?php foreach ($files as $f): ?>
      <li>📄 <?= htmlspecialchars(basename($f)) ?> <span class="text-slate-400">(<?= number_format(filesize($f)) ?> bytes)</span></li>
    <?php endforeach; ?>
    <?php if (empty($files)): ?><li class="text-slate-400">Chưa có migration nào.</li><?php endif; ?>
  </ul>
</div>

<form method="POST" class="bg-white rounded-lg shadow p-6 mb-6">
  <h2 class="text-lg font-semibold mb-3">Chạy migrations</h2>
  <div class="grid grid-cols-2 gap-4 mb-4">
    <div>
      <label class="block text-sm font-medium mb-1">Tenant (để trống = tất cả)</label>
      <input type="text" name="tenant" value="<?= htmlspecialchars($onlyTenant) ?>" placeholder="vd: baocaosukhonglo" class="w-full border rounded px-3 py-2">
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">File (prefix, để trống = tất cả)</label>
      <input type="text" name="file" value="<?= htmlspecialchars($onlyFile) ?>" placeholder="vd: 001" class="w-full border rounded px-3 py-2">
    </div>
  </div>
  <p class="text-xs text-slate-500 mb-3">Idempotent — chạy lại không hỏng dữ liệu. Lịch sử migration lưu trong bảng <code>_migrations</code> của mỗi tenant.</p>
  <button type="submit" name="action" value="run" class="bg-indigo-600 text-white px-5 py-2 rounded font-medium hover:bg-indigo-700">▶ Chạy migrations</button>
</form>

<?php if (!empty($logs)): ?>
<div class="bg-slate-900 text-green-300 rounded-lg p-4 font-mono text-xs">
  <?php foreach ($logs as $l) echo htmlspecialchars($l) . "<br>"; ?>
</div>
<?php endif; ?>

</main></body></html>
