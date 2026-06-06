<?php
/**
 * SePay webhook nhận thông báo giao dịch ngân hàng.
 *
 * Setup tại sepay.vn: tạo Webhook
 *   URL:    https://quanlybanhang.shop/landing/sepay-webhook.php
 *   Method: POST (JSON)
 *   Authentication HTTP: Bearer / Header (xem .env: SEPAY_WEBHOOK_TOKEN)
 *
 * Logic match:
 *   - Chỉ xử lý CK "in" (transferType == "in") số tiền == amount.
 *   - Match payments.bank_ref bằng cách tìm chuỗi "<subdomain> <ref_suffix>"
 *     trong nội dung CK (case-insensitive, bỏ dấu cách thừa).
 *   - Nếu khớp 1 payment status=pending → chuyển sang confirmed + cộng tháng/chi nhánh
 *     + gửi mail xác nhận. Idempotent (ghi sepay_tx_id, không xử lý 2 lần).
 */

require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/mailer.php';

header('Content-Type: application/json; charset=utf-8');
$env = env_load();

function sw_log($msg) {
    @file_put_contents(__DIR__ . '/../sepay-webhook.log',
        '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

function sw_reply($status, $message = '', $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => $status === 'ok', 'message' => $message]);
    exit;
}

// 1) Authenticate token
$expected = $env['SEPAY_WEBHOOK_TOKEN'] ?? '';
if ($expected === '') {
    sw_log('Missing SEPAY_WEBHOOK_TOKEN in env');
    sw_reply('error', 'Server not configured', 500);
}
$auth = trim($_SERVER['HTTP_AUTHORIZATION'] ?? '');
// SePay dùng prefix "Apikey ", các provider khác dùng "Bearer ". Chấp nhận cả 2.
foreach (['Bearer ', 'Apikey ', 'ApiKey ', 'APIKey '] as $prefix) {
    if (stripos($auth, $prefix) === 0) { $auth = substr($auth, strlen($prefix)); break; }
}
$auth = trim($auth);
if (!hash_equals($expected, $auth)) {
    sw_log('Auth failed. Got: ' . substr($auth, 0, 12) . '...');
    sw_reply('error', 'Unauthorized', 401);
}

// 2) Parse payload
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    sw_log('Invalid JSON: ' . substr($raw, 0, 200));
    sw_reply('error', 'Invalid JSON', 400);
}

$txId    = (string)($data['id'] ?? $data['referenceCode'] ?? '');
$type    = strtolower((string)($data['transferType'] ?? ''));
$amount  = (float)($data['transferAmount'] ?? $data['amount'] ?? 0);
$content = (string)($data['content'] ?? $data['description'] ?? '');
$account = (string)($data['accountNumber'] ?? '');

if ($type !== 'in') {
    // Không log để giảm noise cho CK đi (anh chuyển tiền cho người khác).
    sw_reply('ok', 'Ignored (not incoming)');
}
if ($amount <= 0) {
    sw_reply('ok', 'Ignored (zero amount)');
}

// Chỉ xử lý CK vào đúng tài khoản business. Bỏ qua nếu khác (vd tài khoản
// cá nhân khác link vào SePay sau này) — tránh log nội dung CK lạ.
$bizAccount = (string)($env['BANK_ACCOUNT'] ?? '');
if ($bizAccount !== '' && $account !== '' && $account !== $bizAccount) {
    sw_reply('ok', 'Ignored (different account)');
}

// 3) Find matching pending payment
$pdo = master_pdo();

// Schema migration: thêm cột sepay_tx_id nếu chưa có (idempotent guard).
try {
    $pdo->exec("ALTER TABLE payments ADD COLUMN sepay_tx_id VARCHAR(64) NULL DEFAULT NULL");
    $pdo->exec("ALTER TABLE payments ADD UNIQUE KEY uq_sepay_tx (sepay_tx_id)");
} catch (Exception $e) { /* đã có rồi */ }

// Đã xử lý tx này chưa?
if ($txId !== '') {
    $st = $pdo->prepare("SELECT id FROM payments WHERE sepay_tx_id = ? LIMIT 1");
    $st->execute([$txId]);
    if ($st->fetch()) {
        sw_log("Tx $txId already processed");
        sw_reply('ok', 'Already processed');
    }
}

// Normalize: lower + bỏ tất cả ký tự không phải [a-z0-9].
// MB/SePay strip dấu '-', space, dấu chấm → content "QLBH xinchao extra1cn-1th"
// sẽ bị MB ép thành "QLBHxinchaoextra1cn1th" hoặc các biến thể tương tự.
$normalize = function ($s) {
    return preg_replace('/[^a-z0-9]/i', '', mb_strtolower((string)$s, 'UTF-8'));
};
$normContent = $normalize($content);

// Tìm tất cả payment pending có cùng amount
$st = $pdo->prepare("SELECT p.*, t.subdomain, t.owner_email FROM payments p JOIN tenants t ON t.id=p.tenant_id
                     WHERE p.status='pending' AND p.amount = ? ORDER BY p.id DESC");
$st->execute([(int)$amount]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

$match = null;
foreach ($rows as $r) {
    $ref = $normalize($r['bank_ref']);
    if ($ref === '') continue;
    if (strpos($normContent, $ref) !== false) {
        $match = $r;
        break;
    }
}

if (!$match) {
    // Privacy: KHÔNG log nội dung CK khi không match (có thể là CK cá nhân).
    // Chỉ log số tiền + tx id để debug; admin tự đối soát tay nếu cần.
    sw_log("No match: amount=$amount tx=$txId (content not logged for privacy)");
    sw_reply('ok', 'No matching payment');
}

// 4) Confirm payment + extend tenant (giống logic admin-panel/payments.php)
try {
    $pdo->beginTransaction();
    $st = $pdo->prepare("UPDATE payments SET status='confirmed', confirmed_at=NOW(), sepay_tx_id=? WHERE id=? AND status='pending'");
    $st->execute([$txId !== '' ? $txId : null, $match['id']]);
    if ($st->rowCount() === 0) {
        $pdo->rollBack();
        sw_log("Race: payment {$match['id']} không còn pending");
        sw_reply('ok', 'Already updated by another process');
    }
    $months   = (int)$match['months_added'];
    $branches = (int)$match['branches_added'];
    $plan     = (string)$match['plan'];
    $pdo->prepare("UPDATE tenants
                   SET expires_at = DATE_ADD(GREATEST(expires_at, NOW()), INTERVAL {$months} MONTH),
                       paid_branches = paid_branches + ?,
                       status = 'active',
                       plan = ?
                   WHERE id = ?")
        ->execute([$branches, $plan, $match['tenant_id']]);
    $pdo->commit();

    sw_log("Confirmed payment #{$match['id']} for {$match['subdomain']} amount={$amount} tx={$txId}");

    // Gửi email xác nhận (best-effort)
    $st = $pdo->prepare("SELECT expires_at, paid_branches FROM tenants WHERE id=?");
    $st->execute([$match['tenant_id']]);
    $after = $st->fetch(PDO::FETCH_ASSOC);
    if ($after && !empty($match['owner_email'])) {
        $body = '<p>Hệ thống đã tự động xác nhận thanh toán của bạn qua chuyển khoản ngân hàng.</p>'
              . '<ul>'
              . '<li>Cửa hàng: <strong>' . htmlspecialchars($match['subdomain']) . '</strong></li>'
              . '<li>Số tiền: <strong>' . number_format($amount) . 'đ</strong></li>'
              . '<li>Hạn sử dụng mới: <strong>' . htmlspecialchars($after['expires_at']) . '</strong></li>'
              . '<li>Số chi nhánh: <strong>' . (int)$after['paid_branches'] . '</strong></li>'
              . '</ul>'
              . '<p>Cảm ơn bạn!</p>';
        try { send_mail($match['owner_email'], 'Đã xác nhận thanh toán', $body); }
        catch (Exception $e) { sw_log('Mail fail: ' . $e->getMessage()); }
    }

    sw_reply('ok', "Confirmed payment {$match['id']}");
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    sw_log('Confirm exception: ' . $e->getMessage());
    sw_reply('error', $e->getMessage(), 500);
}
