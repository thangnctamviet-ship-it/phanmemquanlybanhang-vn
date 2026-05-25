<?php require_once __DIR__.'/auth.php'; require_login(); ?>
<!DOCTYPE html><html lang="vi"><head>
<meta charset="UTF-8"><title><?= $page_title ?? 'Admin' ?></title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-slate-100 text-slate-800">
<nav class="bg-slate-900 text-white">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <div class="font-bold">🛠 Admin · Quản lý bán hàng</div>
    <div class="space-x-4 text-sm">
      <a href="tenants.php" class="hover:text-indigo-300">Tenants</a>
      <a href="payments.php" class="hover:text-indigo-300">Thanh toán</a>
      <a href="logout.php" class="hover:text-red-300">Đăng xuất</a>
    </div>
  </div>
</nav>
<main class="max-w-7xl mx-auto px-4 py-6">
