<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/provisioner.php';
require_once dirname(__DIR__) . '/tenant-shared/CpanelApi.php';

function deprovision_json($code, array $payload) {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$env = env_load();
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? ($_GET['token'] ?? '');
$expected = substr($env['OWNER_PASSWORD_HASH'] ?? '', 0, 16);
if (!$expected || !hash_equals($expected, $token)) {
    deprovision_json(403, ['status' => 'error', 'message' => 'Forbidden']);
}

$sub = provision_normalize_subdomain($_POST['subdomain'] ?? $_GET['subdomain'] ?? '');
if (!$sub) {
    deprovision_json(400, ['status' => 'error', 'message' => 'Missing subdomain']);
}

try {
    $pdo = master_pdo();
    $stmt = $pdo->prepare('SELECT * FROM tenants WHERE subdomain=? LIMIT 1');
    $stmt->execute([$sub]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tenant) {
        deprovision_json(404, ['status' => 'error', 'message' => 'Tenant not found']);
    }

    $cpanel = CpanelApi::fromEnv($env);
    try { $cpanel->deleteSubdomain($sub . '.' . $env['BASE_DOMAIN']); } catch (Exception $e) { provision_log('Ignore deleteSubdomain: ' . $e->getMessage()); }
    if (!empty($tenant['db_name'])) {
        try { $cpanel->deleteDatabase($tenant['db_name']); } catch (Exception $e) { provision_log('Ignore deleteDatabase: ' . $e->getMessage()); }
    }
    if (!empty($tenant['db_user'])) {
        try { $cpanel->deleteDbUser($tenant['db_user']); } catch (Exception $e) { provision_log('Ignore deleteDbUser: ' . $e->getMessage()); }
    }

    provision_rrmdir(dirname(__DIR__) . '/tenants/' . $sub);
    $note = 'deprovisioned at ' . date('Y-m-d H:i:s');
    $cols = $pdo->query('SHOW COLUMNS FROM tenants')->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('notes', $cols, true)) {
        $pdo->prepare("UPDATE tenants SET status='suspended', notes=CONCAT(COALESCE(notes,''), IF(COALESCE(notes,'')='', '', '\n'), ?) WHERE id=?")
            ->execute([$note, $tenant['id']]);
    } else {
        $pdo->prepare("UPDATE tenants SET status='suspended' WHERE id=?")->execute([$tenant['id']]);
    }

    echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    deprovision_json(500, ['status' => 'error', 'message' => $e->getMessage()]);
}
