<?php
require_once __DIR__ . '/../landing/blog/_helpers.php';
$page_title = 'Quản lý Blog';

$pdo = master_pdo();
$action = $_GET['action'] ?? 'list';
$msg = '';

// Ensure table exists (no-op if already there)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
      id INT AUTO_INCREMENT PRIMARY KEY,
      slug VARCHAR(200) UNIQUE NOT NULL,
      title VARCHAR(255) NOT NULL,
      excerpt VARCHAR(500),
      cover_image VARCHAR(500),
      content MEDIUMTEXT NOT NULL,
      tags VARCHAR(255),
      meta_title VARCHAR(255),
      meta_description VARCHAR(500),
      status ENUM('draft','published') DEFAULT 'draft',
      views INT DEFAULT 0,
      published_at DATETIME NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX idx_status(status), INDEX idx_published(published_at)
    ) DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/auth.php';
    require_login();
    $id = (int)($_POST['id'] ?? 0);
    if (!empty($_POST['delete']) && $id) {
        $pdo->prepare("DELETE FROM blog_posts WHERE id=:id")->execute([':id'=>$id]);
        header('Location: blog.php?msg=deleted'); exit;
    }
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (!$slug) $slug = blog_slugify($title);
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($slug));
    $excerpt = trim($_POST['excerpt'] ?? '');
    $cover = trim($_POST['cover_image'] ?? '');
    $content = $_POST['content'] ?? '';
    $tags = trim($_POST['tags'] ?? '');
    $mt = trim($_POST['meta_title'] ?? '');
    $md = trim($_POST['meta_description'] ?? '');
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';

    if (!$title || !$slug || !$content) {
        $msg = 'Thiếu title/slug/content'; $action = 'edit';
    } else {
        // Unique slug check
        $q = $pdo->prepare("SELECT id FROM blog_posts WHERE slug=:s AND id<>:id");
        $q->execute([':s'=>$slug, ':id'=>$id]);
        if ($q->fetch()) {
            $msg = 'Slug đã tồn tại'; $action = 'edit';
        } else {
            if ($id) {
                $pub = $_POST['published_at'] ?: null;
                if ($status === 'published' && !$pub) $pub = date('Y-m-d H:i:s');
                $stmt = $pdo->prepare("UPDATE blog_posts SET slug=:s,title=:t,excerpt=:e,cover_image=:c,content=:co,tags=:tg,meta_title=:mt,meta_description=:md,status=:st,published_at=:p WHERE id=:id");
                $stmt->execute([':s'=>$slug,':t'=>$title,':e'=>$excerpt,':c'=>$cover,':co'=>$content,':tg'=>$tags,':mt'=>$mt,':md'=>$md,':st'=>$status,':p'=>$pub,':id'=>$id]);
            } else {
                $pub = $status === 'published' ? date('Y-m-d H:i:s') : null;
                $stmt = $pdo->prepare("INSERT INTO blog_posts(slug,title,excerpt,cover_image,content,tags,meta_title,meta_description,status,published_at) VALUES(:s,:t,:e,:c,:co,:tg,:mt,:md,:st,:p)");
                $stmt->execute([':s'=>$slug,':t'=>$title,':e'=>$excerpt,':c'=>$cover,':co'=>$content,':tg'=>$tags,':mt'=>$mt,':md'=>$md,':st'=>$status,':p'=>$pub]);
                $id = $pdo->lastInsertId();
            }
            header('Location: blog.php?msg=saved'); exit;
        }
    }
}

include __DIR__ . '/includes/layout.php';

if ($action === 'edit' || $action === 'new') {
    $post = ['id'=>0,'slug'=>'','title'=>'','excerpt'=>'','cover_image'=>'','content'=>'','tags'=>'','meta_title'=>'','meta_description'=>'','status'=>'draft','published_at'=>null];
    if ($action === 'edit' && !empty($_GET['id'])) {
        $st = $pdo->prepare("SELECT * FROM blog_posts WHERE id=:id");
        $st->execute([':id'=>(int)$_GET['id']]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row) $post = $row;
    }
?>
  <div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4"><?= $post['id'] ? 'Sửa bài: ' . htmlspecialchars($post['title']) : 'Bài mới' ?></h1>
    <?php if ($msg): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post" class="space-y-4">
      <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
      <div>
        <label class="block text-sm font-medium mb-1">Tiêu đề *</label>
        <input id="title" name="title" required value="<?= htmlspecialchars($post['title']) ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Slug (URL)</label>
        <input id="slug" name="slug" value="<?= htmlspecialchars($post['slug']) ?>" placeholder="tu-dong-tu-tieu-de" class="w-full border rounded px-3 py-2 font-mono text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Cover image URL</label>
        <input name="cover_image" value="<?= htmlspecialchars($post['cover_image']) ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Excerpt (mô tả ngắn)</label>
        <textarea name="excerpt" rows="2" class="w-full border rounded px-3 py-2"><?= htmlspecialchars($post['excerpt']) ?></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Tags (CSV: huong-dan,kho)</label>
        <input name="tags" value="<?= htmlspecialchars($post['tags']) ?>" class="w-full border rounded px-3 py-2">
      </div>
      <div class="grid md:grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium mb-1">Meta title (SEO)</label>
          <input name="meta_title" value="<?= htmlspecialchars($post['meta_title']) ?>" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">Meta description (SEO)</label>
          <input name="meta_description" value="<?= htmlspecialchars($post['meta_description']) ?>" class="w-full border rounded px-3 py-2"></div>
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Nội dung *</label>
        <textarea id="content" name="content"><?= htmlspecialchars($post['content']) ?></textarea>
      </div>
      <div>
        <label class="inline-flex items-center gap-2 mr-4"><input type="radio" name="status" value="draft" <?= $post['status']==='draft'?'checked':'' ?>> Draft</label>
        <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="published" <?= $post['status']==='published'?'checked':'' ?>> Publish</label>
      </div>
      <?php if ($post['id']): ?>
        <div><label class="block text-sm font-medium mb-1">Published at</label>
          <input name="published_at" value="<?= htmlspecialchars($post['published_at'] ?? '') ?>" placeholder="YYYY-MM-DD HH:MM:SS" class="border rounded px-3 py-2"></div>
      <?php endif; ?>
      <div class="flex gap-2">
        <button class="bg-indigo-600 text-white px-5 py-2 rounded hover:bg-indigo-700">Lưu</button>
        <a href="blog.php" class="px-5 py-2 rounded border">Huỷ</a>
        <?php if ($post['id']): ?><a href="/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="px-5 py-2 rounded border">Preview</a><?php endif; ?>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector: '#content',
      height: 600,
      plugins: 'lists link image table code preview searchreplace fullscreen wordcount',
      toolbar: 'undo redo | h2 h3 | bold italic | bullist numlist | link image table | blockquote code | preview fullscreen',
      menubar: false,
      language: 'vi',
      language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs7/vi.js',
      relative_urls: false,
      convert_urls: false,
      branding: false,
    });
    // auto slug
    document.getElementById('title').addEventListener('blur', function(){
      var s = document.getElementById('slug');
      if (!s.value) {
        var v = this.value.toLowerCase()
          .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
          .replace(/đ/g,'d').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
        s.value = v;
      }
    });
  </script>
<?php
} else {
    $rows = $pdo->query("SELECT id,title,slug,status,views,published_at,created_at FROM blog_posts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Blog posts (<?= count($rows) ?>)</h1>
    <a href="blog.php?action=new" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Bài mới</a>
  </div>
  <?php if (!empty($_GET['msg'])): ?><div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($_GET['msg']) ?></div><?php endif; ?>
  <div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr class="text-left">
        <th class="px-4 py-3">Title</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Views</th><th class="px-4 py-3">Published</th><th class="px-4 py-3">Actions</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr class="border-b">
          <td class="px-4 py-3 font-medium"><?= htmlspecialchars($r['title']) ?><div class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($r['slug']) ?></div></td>
          <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs <?= $r['status']==='published'?'bg-green-100 text-green-700':'bg-slate-200 text-slate-600' ?>"><?= $r['status'] ?></span></td>
          <td class="px-4 py-3"><?= number_format((int)$r['views']) ?></td>
          <td class="px-4 py-3"><?= htmlspecialchars($r['published_at'] ?? '-') ?></td>
          <td class="px-4 py-3 space-x-2">
            <a href="blog.php?action=edit&id=<?= $r['id'] ?>" class="text-indigo-600 hover:underline">Edit</a>
            <a href="/blog/<?= htmlspecialchars($r['slug']) ?>" target="_blank" class="text-slate-600 hover:underline">Preview</a>
            <form method="post" class="inline" onsubmit="return confirm('Xoá bài này?')">
              <input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="delete" value="1">
              <button class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Chưa có bài viết</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
<?php } ?>
</main></body></html>
