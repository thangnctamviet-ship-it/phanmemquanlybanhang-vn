<?php
require_once __DIR__ . '/_helpers.php';
header('Cache-Control: public, max-age=300');

$pdo = master_pdo();
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$search = trim($_GET['q'] ?? '');

$total = blog_count_published($pdo, $search);
$totalPages = max(1, (int)ceil($total / $perPage));
$posts = blog_fetch_published($pdo, $perPage, ($page - 1) * $perPage, $search);

// Popular & tags
$popular = $pdo->query("SELECT slug, title, views FROM blog_posts WHERE status='published' ORDER BY views DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$tagRows = $pdo->query("SELECT tags FROM blog_posts WHERE status='published' AND tags IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
$tagCloud = [];
foreach ($tagRows as $row) {
    foreach (blog_tag_list($row) as $t) {
        $tagCloud[$t] = ($tagCloud[$t] ?? 0) + 1;
    }
}
arsort($tagCloud);
$tagCloud = array_slice($tagCloud, 0, 20, true);

$page_title = 'Blog - Mẹo quản lý cửa hàng, kho, doanh thu | Quản Lý Bán Hàng';
$meta_desc = 'Mẹo và hướng dẫn cho chủ shop, chủ cửa hàng bán lẻ Việt Nam: quản lý tồn kho, in bill, theo dõi doanh thu, mở chi nhánh.';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
<link rel="canonical" href="https://quanlybanhang.shop/blog/">
<meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="https://quanlybanhang.shop/blog/">
<meta property="og:locale" content="vi_VN">
<meta property="og:site_name" content="Quản Lý Bán Hàng">
<meta name="twitter:card" content="summary_large_image">
<link rel="manifest" href="/manifest.json">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-50 text-slate-800">
<?php
// Render shared nav (simplified copy from header.php so we control <title>)
?>
<nav class="bg-white border-b sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="/landing/" class="text-lg md:text-xl font-bold text-indigo-600 flex items-center gap-1"><span>🛒</span><span>Quản Lý Bán Hàng</span></a>
    <div class="hidden md:flex items-center space-x-4 text-sm">
      <a href="/landing/" class="hover:text-indigo-600">Trang chủ</a>
      <a href="/landing/pricing.php" class="hover:text-indigo-600">Bảng giá</a>
      <a href="/blog/" class="text-indigo-600 font-semibold">Blog</a>
      <a href="/landing/install.php" class="hover:text-indigo-600">📱 Tải app</a>
      <a href="/landing/login.php" class="hover:text-indigo-600 font-medium">Đăng nhập</a>
      <a href="/landing/register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Dùng thử miễn phí</a>
    </div>
    <button class="md:hidden p-2 -mr-2" onclick="document.getElementById('mm').classList.toggle('hidden')" aria-label="Menu">
      <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
  <div id="mm" class="hidden md:hidden border-t bg-white">
    <div class="px-4 py-3 space-y-1">
      <a href="/landing/" class="block py-2">Trang chủ</a>
      <a href="/landing/pricing.php" class="block py-2">Bảng giá</a>
      <a href="/blog/" class="block py-2 text-indigo-600 font-semibold">Blog</a>
      <a href="/landing/install.php" class="block py-2">📱 Tải app</a>
      <a href="/landing/login.php" class="block py-2 font-medium">Đăng nhập</a>
      <a href="/landing/register.php" class="block py-2.5 mt-2 text-center bg-indigo-600 text-white rounded-lg font-medium">Dùng thử miễn phí</a>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white">
  <div class="max-w-5xl mx-auto px-4 py-14 text-center">
    <h1 class="text-3xl md:text-5xl font-bold mb-4">Blog Quản Lý Bán Hàng</h1>
    <p class="text-indigo-100 md:text-lg max-w-2xl mx-auto mb-6">Mẹo, kinh nghiệm thực chiến cho chủ shop, chủ cửa hàng bán lẻ Việt Nam — từ tồn kho, in bill tới mở chi nhánh.</p>
    <form method="get" action="/blog/" class="max-w-xl mx-auto flex gap-2">
      <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm bài viết..." class="flex-1 px-4 py-3 rounded-lg text-slate-800 focus:outline-none">
      <button class="bg-amber-400 text-slate-900 font-semibold px-5 py-3 rounded-lg hover:bg-amber-300">Tìm</button>
    </form>
  </div>
</section>

<main class="max-w-6xl mx-auto px-4 py-10 grid lg:grid-cols-4 gap-8">
  <div class="lg:col-span-3">
    <?php if (!$posts): ?>
      <div class="bg-white rounded-xl p-10 text-center text-slate-500 border">Chưa có bài viết nào<?= $search ? ' khớp với từ khoá.' : '.' ?></div>
    <?php else: ?>
      <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($posts as $p):
          $tags = blog_tag_list($p['tags']);
          $rt = blog_reading_time_minutes($p['content']);
        ?>
          <article class="bg-white rounded-xl overflow-hidden border hover:shadow-md transition">
            <a href="/blog/<?= htmlspecialchars($p['slug']) ?>" class="block">
              <?php if ($p['cover_image']): ?>
                <img src="<?= htmlspecialchars($p['cover_image']) ?>" loading="lazy" alt="<?= htmlspecialchars($p['title']) ?>" class="w-full h-44 object-cover">
              <?php endif; ?>
              <div class="p-5">
                <h2 class="font-bold text-lg text-slate-900 mb-2 line-clamp-2"><?= htmlspecialchars($p['title']) ?></h2>
                <p class="text-sm text-slate-600 mb-3 line-clamp-3"><?= htmlspecialchars($p['excerpt']) ?></p>
                <div class="flex flex-wrap gap-1 mb-3">
                  <?php foreach (array_slice($tags,0,3) as $t): ?>
                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded"><?= htmlspecialchars($t) ?></span>
                  <?php endforeach; ?>
                </div>
                <div class="text-xs text-slate-400 flex justify-between"><span><?= blog_format_date($p['published_at']) ?></span><span><?= $rt ?> phút đọc</span></div>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-2 mt-10">
          <?php for ($i=1; $i<=$totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_filter(['q'=>$search,'page'=>$i])) ?>"
               class="px-4 py-2 rounded <?= $i===$page ? 'bg-indigo-600 text-white' : 'bg-white border hover:bg-slate-50' ?>"><?= $i ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

      <!-- CTA -->
      <div class="mt-12 bg-gradient-to-r from-amber-400 to-amber-300 rounded-2xl p-8 text-center">
        <h3 class="text-2xl font-bold text-slate-900 mb-2">Sẵn sàng quản lý cửa hàng nhẹ hơn?</h3>
        <p class="text-slate-800 mb-4">Dùng thử miễn phí 14 ngày — không cần thẻ tín dụng.</p>
        <a href="/landing/register.php" class="inline-block bg-slate-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-slate-800">Dùng thử ngay</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar -->
  <aside class="space-y-6">
    <div class="bg-white border rounded-xl p-5">
      <h3 class="font-semibold mb-3">📈 Bài phổ biến</h3>
      <ul class="space-y-2 text-sm">
        <?php foreach ($popular as $pp): ?>
          <li><a href="/blog/<?= htmlspecialchars($pp['slug']) ?>" class="text-slate-700 hover:text-indigo-600"><?= htmlspecialchars($pp['title']) ?></a></li>
        <?php endforeach; ?>
        <?php if (!$popular): ?><li class="text-slate-400">Chưa có dữ liệu</li><?php endif; ?>
      </ul>
    </div>

    <div class="bg-white border rounded-xl p-5">
      <h3 class="font-semibold mb-3">🏷 Tags</h3>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($tagCloud as $t => $c): ?>
          <a href="?q=<?= urlencode($t) ?>" class="text-xs bg-slate-100 hover:bg-indigo-100 hover:text-indigo-700 text-slate-700 px-2 py-1 rounded"><?= htmlspecialchars($t) ?> <span class="text-slate-400">(<?= $c ?>)</span></a>
        <?php endforeach; ?>
        <?php if (!$tagCloud): ?><span class="text-slate-400 text-sm">Chưa có</span><?php endif; ?>
      </div>
    </div>
  </aside>
</main>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
