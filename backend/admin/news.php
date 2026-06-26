<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;

// SAVE (add or edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    $id       = (int)($_POST['id'] ?? 0);
    $date     = trim($_POST['date_text']);
    $category = trim($_POST['category']);
    $title    = trim($_POST['title']);
    $summary  = trim($_POST['summary']);
    $image    = trim($_POST['image']);
    $active   = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE news SET date_text=?,category=?,title=?,summary=?,image=?,is_active=?,updated_at=NOW() WHERE id=?")
            ->execute([$date,$category,$title,$summary,$image,$active,$id]);
        $msg = 'News article updated!';
    } else {
        $pdo->prepare("INSERT INTO news (date_text,category,title,summary,image,is_active) VALUES (?,?,?,?,?,?)")
            ->execute([$date,$category,$title,$summary,$image,$active]);
        $msg = 'News article added!';
    }
    header('Location: news.php?msg=' . urlencode($msg)); exit();
}

// DELETE
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM news WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: news.php?msg=Article+deleted'); exit();
}

// EDIT FETCH
if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM news WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$newsList = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();

admin_head('News');
?>
<div class="admin-layout">
<?php admin_sidebar('news'); ?>
<div class="main-content">
<?php admin_topbar('News Manager', 'Add, edit or delete news articles'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>News Articles (<?= count($newsList) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Article</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Article' : 'Add New Article' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Date (display text)</label>
        <input name="date_text" class="form-control" value="<?= htmlspecialchars($editItem['date_text'] ?? date('d M Y')) ?>" placeholder="20 May 2026">
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <input name="category" class="form-control" value="<?= htmlspecialchars($editItem['category'] ?? '') ?>" placeholder="Innovation / Projects / Exhibition">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Title *</label>
      <input name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="Article headline">
    </div>
    <div class="form-group">
      <label class="form-label">Summary</label>
      <textarea name="summary" class="form-control" rows="3"><?= htmlspecialchars($editItem['summary'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Image Path (optional)</label>
      <input name="image" class="form-control" value="<?= htmlspecialchars(normalize_asset_path($editItem['image'] ?? '')) ?>" placeholder="/assets/10.jpeg">
    </div>
    <div style="display:flex;align-items:center;gap:12px">
      <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
      <label class="form-label" style="margin:0">Published</label>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_news" class="btn btn-primary">Save Article</button>
      <a href="news.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Date</th><th>Category</th><th>Title</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($newsList as $n): ?>
      <tr>
        <td class="text-sm text-muted"><?= htmlspecialchars($n['date_text']) ?></td>
        <td><span class="td-badge" style="background:var(--accent-dim);color:var(--accent)"><?= htmlspecialchars($n['category']) ?></span></td>
        <td class="truncate"><strong><?= htmlspecialchars($n['title']) ?></strong></td>
        <td><span class="td-badge <?= $n['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $n['is_active'] ? 'Published' : 'Draft' ?></span></td>
        <td class="flex gap-2">
          <a href="?edit=<?= $n['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?del=<?= $n['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>
</div>
</div>
</body>
</html>
