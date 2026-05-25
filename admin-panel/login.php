<?php require_once __DIR__.'/includes/auth.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (admin_login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        header('Location: tenants.php'); exit;
    }
    $err = 'Sai email hoặc mật khẩu.';
}
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Đăng nhập Admin</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
<form method="POST" class="bg-white p-8 rounded-xl shadow w-96 space-y-4">
  <h1 class="text-2xl font-bold">🛠 Admin Login</h1>
  <?php if($err): ?><div class="bg-red-50 text-red-700 p-2 rounded text-sm"><?= $err ?></div><?php endif; ?>
  <input name="email" type="email" placeholder="Email" required class="w-full border rounded px-3 py-2">
  <input name="password" type="password" placeholder="Mật khẩu" required class="w-full border rounded px-3 py-2">
  <button class="w-full bg-slate-900 text-white py-2 rounded hover:bg-slate-800">Đăng nhập</button>
  <p class="text-xs text-slate-500">Thiết lập OWNER_EMAIL & OWNER_PASSWORD_HASH trong .env</p>
</form></body></html>
