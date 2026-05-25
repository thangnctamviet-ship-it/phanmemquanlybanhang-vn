<?php
header('Content-Type: application/json');
require __DIR__.'/includes/db.php';
$sub = strtolower(trim($_POST['subdomain'] ?? $_GET['subdomain'] ?? ''));
$err = validate_subdomain($sub);
if ($err) { echo json_encode(['available'=>false,'message'=>$err]); exit; }
try {
    $stmt = master_pdo()->prepare("SELECT id FROM tenants WHERE subdomain=?");
    $stmt->execute([$sub]);
    if ($stmt->fetch()) echo json_encode(['available'=>false,'message'=>'Subdomain đã được sử dụng.']);
    else echo json_encode(['available'=>true,'message'=>'Có thể dùng!']);
} catch (Exception $e) {
    echo json_encode(['available'=>false,'message'=>'Không kết nối được DB chủ.']);
}
