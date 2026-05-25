<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $page_title ?? 'Quản lý bán hàng - SaaS' ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-50 text-slate-800">
<nav class="bg-white border-b">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="/landing/" class="text-xl font-bold text-indigo-600">🛒 Quản lý bán hàng</a>
    <div class="space-x-4 text-sm">
      <a href="/landing/" class="hover:text-indigo-600">Trang chủ</a>
      <a href="/landing/pricing.php" class="hover:text-indigo-600">Bảng giá</a>
      <a href="/landing/register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Dùng thử miễn phí</a>
    </div>
  </div>
</nav>
