<?php
require_once __DIR__ . '/_helpers.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? ''));
if (!$slug) { http_response_code(404); echo 'Not found'; exit; }

$pdo = master_pdo();
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = :s AND status='published' LIMIT 1");
$stmt->execute([':s' => $slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { http_response_code(404); echo '<h1>Bài viết không tồn tại</h1><a href="/blog/">Quay lại blog</a>'; exit; }

// Increment views
try { $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id")->execute([':id' => $post['id']]); } catch (Exception $e) {}

$tags = blog_tag_list($post['tags']);
$rt = blog_reading_time_minutes($post['content']);
$canonical = blog_canonical_url($post['slug']);
$title_meta = $post['meta_title'] ?: $post['title'];
$desc_meta = $post['meta_description'] ?: $post['excerpt'];
$cover = $post['cover_image'] ?: 'https://quanlybanhang.shop/assets/pwa/icon-512.png';
$published_iso = $post['published_at'] ? date('c', strtotime($post['published_at'])) : date('c');
$updated_iso = $post['updated_at'] ? date('c', strtotime($post['updated_at'])) : $published_iso;

// Related posts
$rel = [];
if ($tags) {
    $like = '%' . $tags[0] . '%';
    $st = $pdo->prepare("SELECT slug,title,cover_image,excerpt FROM blog_posts WHERE status='published' AND id <> :id AND tags LIKE :t ORDER BY published_at DESC LIMIT 3");
    $st->execute([':id'=>$post['id'], ':t'=>$like]);
    $rel = $st->fetchAll(PDO::FETCH_ASSOC);
}
if (count($rel) < 3) {
    $need = 3 - count($rel);
    $exclude = array_map(fn($r)=>$r['slug'], $rel);
    $exclude[] = $post['slug'];
    $in = implode(',', array_fill(0, count($exclude), '?'));
    $st = $pdo->prepare("SELECT slug,title,cover_image,excerpt FROM blog_posts WHERE status='published' AND slug NOT IN ($in) ORDER BY published_at DESC LIMIT $need");
    $st->execute($exclude);
    $rel = array_merge($rel, $st->fetchAll(PDO::FETCH_ASSOC));
}

$content_html = blog_inject_internal_link($post['content']);

$breadcrumbsJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type'=>'ListItem','position'=>1,'name'=>'Trang chủ','item'=>'https://quanlybanhang.shop/'],
        ['@type'=>'ListItem','position'=>2,'name'=>'Blog','item'=>'https://quanlybanhang.shop/blog/'],
        ['@type'=>'ListItem','position'=>3,'name'=>$post['title'],'item'=>$canonical],
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$articleJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post['title'],
    'description' => $desc_meta,
    'image' => [$cover],
    'datePublished' => $published_iso,
    'dateModified' => $updated_iso,
    'author' => ['@type'=>'Organization','name'=>'Quản Lý Bán Hàng','url'=>'https://quanlybanhang.shop/'],
    'publisher' => [
        '@type'=>'Organization','name'=>'Quản Lý Bán Hàng',
        'logo' => ['@type'=>'ImageObject','url'=>'https://quanlybanhang.shop/assets/pwa/icon-192.png']
    ],
    'mainEntityOfPage' => ['@type'=>'WebPage','@id'=>$canonical],
    'keywords' => $post['tags'] ?? '',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title_meta) ?> - Quản Lý Bán Hàng</title>
<meta name="description" content="<?= htmlspecialchars($desc_meta) ?>">
<meta name="keywords" content="<?= htmlspecialchars($post['tags'] ?? '') ?>">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<link rel="alternate" hreflang="vi-VN" href="<?= htmlspecialchars($canonical) ?>">
<meta property="og:title" content="<?= htmlspecialchars($title_meta) ?>">
<meta property="og:description" content="<?= htmlspecialchars($desc_meta) ?>">
<meta property="og:image" content="<?= htmlspecialchars($cover) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:type" content="article">
<meta property="og:locale" content="vi_VN">
<meta property="og:site_name" content="Quản Lý Bán Hàng">
<meta property="article:published_time" content="<?= $published_iso ?>">
<meta property="article:modified_time" content="<?= $updated_iso ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($title_meta) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($desc_meta) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($cover) ?>">
<link rel="manifest" href="/manifest.json">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.tailwindcss.com?plugins=typography"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
<script type="application/ld+json"><?= $articleJson ?></script>
<script type="application/ld+json"><?= $breadcrumbsJson ?></script>
</head>
<body class="bg-slate-50 text-slate-800">
<nav class="bg-white border-b sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
    <a href="/landing/" class="text-lg md:text-xl font-bold text-indigo-600 flex items-center gap-1"><span>🛒</span><span>Quản Lý Bán Hàng</span></a>
    <div class="hidden md:flex items-center space-x-4 text-sm">
      <a href="/landing/" class="hover:text-indigo-600">Trang chủ</a>
      <a href="/landing/pricing.php" class="hover:text-indigo-600">Bảng giá</a>
      <a href="/blog/" class="text-indigo-600 font-semibold">Blog</a>
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
      <a href="/landing/register.php" class="block py-2.5 mt-2 text-center bg-indigo-600 text-white rounded-lg font-medium">Dùng thử miễn phí</a>
    </div>
  </div>
</nav>

<article class="max-w-3xl mx-auto px-4 py-10">
  <nav class="text-sm text-slate-500 mb-4">
    <a href="/" class="hover:text-indigo-600">Trang chủ</a> ›
    <a href="/blog/" class="hover:text-indigo-600">Blog</a> ›
    <span class="text-slate-700"><?= htmlspecialchars($post['title']) ?></span>
  </nav>

  <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 leading-tight"><?= htmlspecialchars($post['title']) ?></h1>
  <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-6">
    <span>✍️ Quản Lý Bán Hàng</span><span>·</span>
    <time datetime="<?= $published_iso ?>"><?= blog_format_date($post['published_at']) ?></time><span>·</span>
    <span><?= $rt ?> phút đọc</span><span>·</span>
    <span>👁 <?= number_format((int)$post['views']) ?> lượt xem</span>
  </div>

  <?php if ($post['cover_image']): ?>
    <img src="<?= htmlspecialchars($post['cover_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full rounded-xl mb-8 object-cover max-h-96">
  <?php endif; ?>

  <div class="prose prose-slate prose-lg max-w-none prose-headings:font-bold prose-a:text-indigo-600 prose-blockquote:border-amber-400 prose-blockquote:bg-amber-50 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:not-italic">
    <?= $content_html ?>
  </div>

  <!-- Mid CTA -->
  <div class="my-10 bg-indigo-50 border-l-4 border-indigo-500 p-5 rounded">
    <p class="font-semibold text-slate-900 mb-1">💡 Bạn đang tìm phần mềm quản lý bán hàng?</p>
    <p class="text-slate-700 mb-3">Dùng thử <strong>Quản Lý Bán Hàng</strong> miễn phí 7 ngày — gọn nhẹ, rẻ, in bill K80 sẵn.</p>
    <a href="/landing/register.php" class="inline-block bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-indigo-700">Dùng thử miễn phí →</a>
  </div>

  <!-- Tags + Share -->
  <div class="flex flex-wrap items-center justify-between gap-4 border-t border-b py-5 my-8">
    <div class="flex flex-wrap gap-2">
      <?php foreach ($tags as $t): ?>
        <a href="/blog/?q=<?= urlencode($t) ?>" class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded">#<?= htmlspecialchars($t) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="flex gap-2">
      <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonical) ?>" class="bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">Facebook</a>
      <a target="_blank" rel="noopener" href="https://zalo.me/share/link?url=<?= urlencode($canonical) ?>" class="bg-sky-500 text-white text-sm px-3 py-2 rounded hover:bg-sky-600">Zalo</a>
      <button onclick="navigator.clipboard.writeText('<?= htmlspecialchars($canonical) ?>');this.innerText='✓ Đã copy'" class="bg-slate-700 text-white text-sm px-3 py-2 rounded hover:bg-slate-800">Copy link</button>
    </div>
  </div>

  <!-- Final CTA -->
  <div class="bg-gradient-to-r from-amber-400 to-amber-300 rounded-2xl p-8 text-center">
    <h3 class="text-2xl font-bold text-slate-900 mb-2">Bắt đầu quản lý cửa hàng dễ hơn hôm nay</h3>
    <p class="text-slate-800 mb-4">Dùng thử miễn phí 7 ngày — không cần thẻ.</p>
    <a href="/landing/register.php" class="inline-block bg-slate-900 text-white px-6 py-3 rounded-lg font-semibold hover:bg-slate-800">Dùng thử ngay</a>
  </div>

  <!-- Related -->
  <?php if ($rel): ?>
    <section class="mt-12">
      <h2 class="text-xl font-bold mb-4">Bài viết liên quan</h2>
      <div class="grid md:grid-cols-3 gap-4">
        <?php foreach ($rel as $r): ?>
          <a href="/blog/<?= htmlspecialchars($r['slug']) ?>" class="block bg-white border rounded-lg p-4 hover:shadow-md">
            <?php if ($r['cover_image']): ?>
              <img src="<?= htmlspecialchars($r['cover_image']) ?>" loading="lazy" alt="" class="w-full h-28 object-cover rounded mb-3">
            <?php endif; ?>
            <h3 class="font-semibold text-sm mb-1 line-clamp-2"><?= htmlspecialchars($r['title']) ?></h3>
            <p class="text-xs text-slate-500 line-clamp-2"><?= htmlspecialchars($r['excerpt']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</article>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
