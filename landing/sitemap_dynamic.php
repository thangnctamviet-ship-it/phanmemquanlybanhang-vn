<?php
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/xml; charset=utf-8');

$base = 'https://quanlybanhang.shop';
$urls = [
    ['loc'=>"$base/", 'changefreq'=>'weekly', 'priority'=>'1.0'],
    ['loc'=>"$base/landing/pricing.php", 'changefreq'=>'monthly', 'priority'=>'0.8'],
    ['loc'=>"$base/landing/register.php", 'changefreq'=>'monthly', 'priority'=>'0.8'],
    ['loc'=>"$base/landing/login.php", 'changefreq'=>'monthly', 'priority'=>'0.5'],
    ['loc'=>"$base/landing/install.php", 'changefreq'=>'monthly', 'priority'=>'0.6'],
    ['loc'=>"$base/blog/", 'changefreq'=>'daily', 'priority'=>'0.9'],
];

try {
    $pdo = master_pdo();
    $rows = $pdo->query("SELECT slug, GREATEST(IFNULL(updated_at, published_at), IFNULL(published_at, updated_at)) AS lm FROM blog_posts WHERE status='published' ORDER BY published_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $urls[] = [
            'loc' => "$base/blog/" . $r['slug'],
            'lastmod' => $r['lm'] ? date('c', strtotime($r['lm'])) : date('c'),
            'changefreq' => 'weekly',
            'priority' => '0.7',
        ];
    }
} catch (Exception $e) {}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
    if (!empty($u['lastmod'])) echo "    <lastmod>{$u['lastmod']}</lastmod>\n";
    echo "    <changefreq>{$u['changefreq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n  </url>\n";
}
echo '</urlset>';
