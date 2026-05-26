<?php
require_once dirname(__DIR__) . '/includes/db.php';

function blog_slugify($s) {
    $s = mb_strtolower(trim($s), 'UTF-8');
    $map = ['à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a','è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e','ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i','ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o','ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o','ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u','ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d'];
    $s = strtr($s, $map);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

function blog_reading_time_minutes($html) {
    $text = trim(strip_tags($html));
    $words = preg_split('/\s+/', $text);
    $count = $words ? count($words) : 0;
    return max(1, (int) ceil($count / 200));
}

function blog_inject_internal_link($html) {
    // Replace first occurrence of "Quản Lý Bán Hàng" (outside tags) with link to "/"
    $pattern = '/(?<!<a[^>]{0,200})\bQuản Lý Bán Hàng\b/u';
    $count = 0;
    $out = preg_replace_callback('/Quản Lý Bán Hàng/u', function($m) use (&$count) {
        if ($count++ === 0) {
            return '<a href="/" class="text-indigo-600 hover:underline font-medium">Quản Lý Bán Hàng</a>';
        }
        return $m[0];
    }, $html, 1);
    return $out ?: $html;
}

function blog_fetch_published($pdo, $limit, $offset = 0, $search = '') {
    $where = "status='published'";
    $params = [];
    if ($search !== '') {
        $where .= " AND (title LIKE :q OR excerpt LIKE :q OR content LIKE :q)";
        $params[':q'] = '%' . $search . '%';
    }
    $sql = "SELECT id, slug, title, excerpt, cover_image, tags, published_at, views, content FROM blog_posts WHERE $where ORDER BY published_at DESC LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function blog_count_published($pdo, $search = '') {
    $where = "status='published'";
    $params = [];
    if ($search !== '') {
        $where .= " AND (title LIKE :q OR excerpt LIKE :q)";
        $params[':q'] = '%' . $search . '%';
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE $where");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function blog_format_date($dt) {
    if (!$dt) return '';
    $ts = strtotime($dt);
    return date('d/m/Y', $ts);
}

function blog_tag_list($csv) {
    if (!$csv) return [];
    return array_filter(array_map('trim', explode(',', $csv)));
}

function blog_canonical_url($slug) {
    return 'https://quanlybanhang.shop/blog/' . $slug;
}
