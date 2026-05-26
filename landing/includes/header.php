<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $page_title ?? 'Quản lý bán hàng - SaaS' ?></title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#4f46e5">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="QLBH">
<link rel="apple-touch-icon" href="/assets/pwa/icon-192.png">
<link rel="icon" type="image/png" sizes="192x192" href="/assets/pwa/icon-192.png">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-50 text-slate-800">
<nav class="bg-white border-b sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="/landing/" class="text-lg md:text-xl font-bold text-indigo-600 flex items-center gap-1">
      <span>🛒</span><span>Quản lý bán hàng</span>
    </a>
    <!-- Desktop menu -->
    <div class="hidden md:flex items-center space-x-4 text-sm">
      <a href="/landing/" class="hover:text-indigo-600">Trang chủ</a>
      <a href="/landing/pricing.php" class="hover:text-indigo-600">Bảng giá</a>
      <a href="/landing/install.php" class="hover:text-indigo-600">📱 Tải app</a>
      <a href="/landing/login.php" class="hover:text-indigo-600 font-medium">Đăng nhập</a>
      <a href="/landing/register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Dùng thử miễn phí</a>
    </div>
    <!-- Mobile burger -->
    <button id="mobileMenuBtn" class="md:hidden p-2 -mr-2 text-slate-700" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" aria-label="Menu">
      <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
  <!-- Mobile menu (drop-down) -->
  <div id="mobileMenu" class="hidden md:hidden border-t bg-white">
    <div class="px-4 py-3 space-y-1">
      <a href="/landing/" class="block py-2 text-slate-700 hover:text-indigo-600">Trang chủ</a>
      <a href="/landing/pricing.php" class="block py-2 text-slate-700 hover:text-indigo-600">Bảng giá</a>
      <a href="/landing/install.php" class="block py-2 text-slate-700 hover:text-indigo-600">📱 Tải app</a>
      <a href="/landing/login.php" class="block py-2 text-slate-700 hover:text-indigo-600 font-medium">Đăng nhập</a>
      <a href="/landing/register.php" class="block py-2.5 mt-2 text-center bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Dùng thử miễn phí</a>
    </div>
  </div>
</nav>
