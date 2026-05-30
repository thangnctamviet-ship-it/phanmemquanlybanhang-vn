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
$forceRerun = !empty($_POST['force']);
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
            if (isset($done[$name]) && !$forceRerun) { $logs[] = "   ⊙ $name (đã chạy)"; continue; }
            $sql = file_get_contents($f);
            try {
                $stats = run_sql_with_delim($pdo, $sql);
                if (!isset($done[$name])) {
                    $pdo->prepare("INSERT INTO `_migrations` (name) VALUES (?)")->execute(array($name));
                }
                $logs[] = "   ✓ $name ({$stats['ok']} stmt OK" . ($stats['skipped'] ? ", {$stats['skipped']} đã có" : "") . ")";
            } catch (Exception $e) {
                $logs[] = "   ❌ $name: " . $e->getMessage();
            }
        }

        // Verify schema 002 cần thiết
        $required = array('cash_accounts','customer_groups','product_units','product_prices','product_batches',
                          'product_combos','returns','purchase_returns','stock_checks','employee_shifts',
                          'commission_rules','promotions','vouchers','tags','audit_log','settings');
        $missing = array();
        foreach ($required as $t) {
            $r = $pdo->query("SHOW TABLES LIKE " . $pdo->quote($t))->fetch();
            if (!$r) $missing[] = $t;
        }
        if ($missing) $logs[] = "   ⚠️ Bảng còn THIẾU: " . implode(', ', $missing);
        else         $logs[] = "   ✅ Đủ 16 bảng schema sâu";
    }
    $logs[] = "Xong.";
}

/**
 * SQL splitter chuẩn — tách statement theo delimiter động (hỗ trợ DELIMITER $$).
 * Bỏ qua comment `-- ...` và `# ...`. Không tách `;` bên trong string literal.
 */
function run_sql_with_delim(PDO $pdo, $sql) {
    // 1. Bỏ comment dòng (-- ...) và (# ...) trước khi parse
    $clean_lines = array();
    foreach (preg_split("/\r?\n/", $sql) as $line) {
        $t = ltrim($line);
        if (preg_match('/^(--|#)/', $t)) continue;
        $clean_lines[] = $line;
    }
    $sql = implode("\n", $clean_lines);

    // 2. Tách theo DELIMITER block + state machine để skip ; trong string
    $stmts = array();
    $delim = ';';
    $buf = '';
    $i = 0; $n = strlen($sql);
    $in_str = false; $str_ch = '';
    while ($i < $n) {
        // Check DELIMITER directive (chỉ ở đầu dòng, ngoài string)
        if (!$in_str && ($i === 0 || $sql[$i-1] === "\n")) {
            if (preg_match('/\GDELIMITER\s+(\S+)\s*(\n|$)/i', $sql, $m, 0, $i)) {
                if (trim($buf) !== '') { $stmts[] = $buf; $buf = ''; }
                $delim = $m[1];
                $i += strlen($m[0]);
                continue;
            }
        }
        $c = $sql[$i];
        // String state
        if ($in_str) {
            $buf .= $c;
            if ($c === '\\' && $i + 1 < $n) { $buf .= $sql[$i+1]; $i += 2; continue; }
            if ($c === $str_ch) $in_str = false;
            $i++; continue;
        }
        if ($c === "'" || $c === '"' || $c === '`') {
            $in_str = true; $str_ch = $c; $buf .= $c; $i++; continue;
        }
        // Check delimiter match
        if (substr($sql, $i, strlen($delim)) === $delim) {
            $stmts[] = $buf;
            $buf = '';
            $i += strlen($delim);
            continue;
        }
        $buf .= $c; $i++;
    }
    if (trim($buf) !== '') $stmts[] = $buf;

    // 3. Execute với tolerance cho idempotent errors
    $ok = 0; $skipped = 0;
    foreach ($stmts as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
            $ok++;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            // Idempotent errors — skip silently
            if (preg_match('/already exists|Duplicate column|Duplicate key/i', $msg)) {
                $skipped++;
                continue;
            }
            throw new Exception("Stmt fail: " . substr($stmt, 0, 200) . "...\n→ " . $msg);
        }
    }
    return array('ok' => $ok, 'skipped' => $skipped);
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
  <label class="flex items-center gap-2 mb-3 text-sm">
    <input type="checkbox" name="force" value="1" <?= $forceRerun ? 'checked' : '' ?>>
    <span><strong>Force re-run</strong> — chạy lại migration kể cả đã có trong <code>_migrations</code> (dùng để sửa migration bị skip nhầm)</span>
  </label>
  <button type="submit" name="action" value="run" class="bg-indigo-600 text-white px-5 py-2 rounded font-medium hover:bg-indigo-700">▶ Chạy migrations</button>
</form>

<?php if (!empty($logs)): ?>
<div class="bg-slate-900 text-green-300 rounded-lg p-4 font-mono text-xs">
  <?php foreach ($logs as $l) echo htmlspecialchars($l) . "<br>"; ?>
</div>
<?php endif; ?>

</main></body></html>
