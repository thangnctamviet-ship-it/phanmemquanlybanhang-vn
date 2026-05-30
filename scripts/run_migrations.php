<?php
/**
 * Chạy migrations cho master DB + tất cả tenant DB.
 *
 * Usage:
 *   php scripts/run_migrations.php                # chạy file mới nhất chưa migrate
 *   php scripts/run_migrations.php --file=001     # chạy migration cụ thể
 *   php scripts/run_migrations.php --dry-run      # chỉ in ra DB nào sẽ chạy
 *   php scripts/run_migrations.php --tenant=xxx   # chỉ 1 tenant
 *
 * Idempotent: mỗi migration đã có CREATE IF NOT EXISTS / check column.
 * Track lịch sử trong bảng `_migrations` (mỗi tenant DB).
 */

$rootDir = dirname(__DIR__);
$envFile = $rootDir . '/.env';
$env = array();
if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        list($k,$v) = array_pad(explode('=', $line, 2), 2, '');
        $env[trim($k)] = trim($v, " \"'");
    }
}

$args = array();
foreach ($argv as $a) {
    if (preg_match('/^--([\w-]+)(?:=(.*))?$/', $a, $m)) $args[$m[1]] = $m[2] ?? true;
}
$dryRun = isset($args['dry-run']);
$onlyFile = isset($args['file']) ? (string)$args['file'] : null;
$onlyTenant = isset($args['tenant']) ? (string)$args['tenant'] : null;

$migDir = $rootDir . '/migrations';
$files = glob($migDir . '/*.sql');
sort($files);
if ($onlyFile) {
    $files = array_filter($files, function($f) use($onlyFile){ return strpos(basename($f), $onlyFile) === 0; });
}
if (empty($files)) { fwrite(STDERR, "Không có migration nào.\n"); exit(1); }

$masterHost = $env['MASTER_DB_HOST'] ?? 'localhost';
$masterUser = $env['MASTER_DB_USER'] ?? '';
$masterPass = $env['MASTER_DB_PASS'] ?? '';
$masterName = $env['MASTER_DB_NAME'] ?? 'master_quanlybanhang';

if (!$masterUser) { fwrite(STDERR, "Thiếu MASTER_DB_USER trong .env\n"); exit(2); }

// Connect master để lấy tenant list
$mpdo = new PDO("mysql:host=$masterHost;dbname=$masterName;charset=utf8mb4", $masterUser, $masterPass, array(PDO::ATTR_ERRMODE => PDO::ATTR_ERRMODE_EXCEPTION));
$rows = $mpdo->query("SELECT subdomain, db_name, db_user, db_pass FROM tenants WHERE status IN ('trial','active','expired')")->fetchAll(PDO::FETCH_ASSOC);

if ($onlyTenant) {
    $rows = array_values(array_filter($rows, function($r) use($onlyTenant){ return $r['subdomain'] === $onlyTenant; }));
    if (empty($rows)) { fwrite(STDERR, "Không tìm thấy tenant '$onlyTenant'\n"); exit(2); }
}

echo "Sẽ chạy " . count($files) . " migration trên " . count($rows) . " tenant" . ($dryRun ? " (DRY-RUN)" : "") . "\n\n";

foreach ($rows as $t) {
    echo "→ Tenant: {$t['subdomain']} (db={$t['db_name']})\n";
    if ($dryRun) continue;

    try {
        $pdo = new PDO("mysql:host=$masterHost;dbname={$t['db_name']};charset=utf8mb4", $t['db_user'], $t['db_pass'], array(PDO::ATTR_ERRMODE => PDO::ATTR_ERRMODE_EXCEPTION));
    } catch (Exception $e) {
        echo "   ❌ Kết nối thất bại: " . $e->getMessage() . "\n";
        continue;
    }

    // Bảng tracking
    $pdo->exec("CREATE TABLE IF NOT EXISTS `_migrations` (
        `name` VARCHAR(128) NOT NULL PRIMARY KEY,
        `ran_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $done = array();
    foreach ($pdo->query("SELECT name FROM `_migrations`") as $r) $done[$r['name']] = 1;

    foreach ($files as $f) {
        $name = basename($f);
        if (isset($done[$name])) {
            echo "   ⊙ $name (đã chạy, bỏ qua)\n";
            continue;
        }
        echo "   ▶ Chạy $name ... ";
        $sql = file_get_contents($f);
        try {
            // SQL có DELIMITER → cần tự split theo $$ block
            run_sql_with_delimiter($pdo, $sql);
            $pdo->prepare("INSERT INTO `_migrations` (name) VALUES (?)")->execute(array($name));
            echo "✓\n";
        } catch (Exception $e) {
            echo "❌\n     " . $e->getMessage() . "\n";
        }
    }
}

echo "\nXong.\n";

function run_sql_with_delimiter(PDO $pdo, $sql) {
    // Split: hỗ trợ DELIMITER $$ ... DELIMITER ;
    $stmts = array();
    $delim = ';';
    $buf = '';
    $lines = preg_split("/\r?\n/", $sql);
    foreach ($lines as $line) {
        $trim = ltrim($line);
        if (preg_match('/^DELIMITER\s+(\S+)/i', $trim, $m)) {
            if ($buf !== '' && trim($buf) !== '') $stmts[] = $buf;
            $buf = '';
            $delim = $m[1];
            continue;
        }
        $buf .= $line . "\n";
        // Nếu line kết thúc bằng delim → split
        if ($delim !== ';') {
            if (substr(rtrim($line), -strlen($delim)) === $delim) {
                $stmts[] = substr(rtrim($buf), 0, -strlen($delim));
                $buf = '';
            }
        }
    }
    if (trim($buf) !== '') $stmts[] = $buf;

    // Với block delim mặc định ; → tách thêm
    $final = array();
    foreach ($stmts as $s) {
        if (preg_match('/CREATE\s+PROCEDURE/i', $s) || preg_match('/DROP\s+PROCEDURE/i', $s)) {
            $final[] = $s;
        } else {
            foreach (explode(';', $s) as $part) {
                if (trim($part) !== '') $final[] = $part;
            }
        }
    }

    foreach ($final as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || preg_match('/^--/', $stmt)) continue;
        $pdo->exec($stmt);
    }
}
