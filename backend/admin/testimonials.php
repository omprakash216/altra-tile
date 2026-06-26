<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
ensure_testimonials_table($pdo);

$msg = '';
$editItem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_testimonial'])) {
    $id = (int)($_POST['id'] ?? 0);
    $quote = trim($_POST['quote']);
    $author = trim($_POST['author']);
    $company = trim($_POST['company']);
    $stars = max(1, min(5, (int)($_POST['stars'] ?? 5)));
    $sort = (int)($_POST['sort_order']);
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE testimonials SET quote=?, author=?, company=?, stars=?, sort_order=?, is_active=? WHERE id=?")
            ->execute([$quote, $author, $company, $stars, $sort, $active, $id]);
        $msg = 'Testimonial updated!';
    } else {
        $pdo->prepare("INSERT INTO testimonials (quote, author, company, stars, sort_order, is_active) VALUES (?,?,?,?,?,?)")
            ->execute([$quote, $author, $company, $stars, $sort, $active]);
        $msg = 'Testimonial added!';
    }
    header('Location: testimonials.php?msg=' . urlencode($msg));
    exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM testimonials WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: testimonials.php?msg=Testimonial+deleted');
    exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM testimonials WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC")->fetchAll();

admin_head('Testimonials');
?>
<div class="admin-layout">
<?php admin_sidebar('testimonials'); ?>
<div class="main-content">
<?php admin_topbar('Testimonials', 'Manage customer quotes shown on the home page'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Testimonials (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Testimonial</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Testimonial' : 'Add New Testimonial' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-group">
      <label class="form-label">Quote *</label>
      <textarea name="quote" class="form-control" rows="4" required placeholder="Customer feedback..."><?= htmlspecialchars($editItem['quote'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Author *</label>
        <input name="author" class="form-control" required value="<?= htmlspecialchars($editItem['author'] ?? '') ?>" placeholder="Rahul Sharma">
      </div>
      <div class="form-group">
        <label class="form-label">Company / Role</label>
        <input name="company" class="form-control" value="<?= htmlspecialchars($editItem['company'] ?? '') ?>" placeholder="CEO, BuildTech India">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Stars</label>
        <input name="stars" type="number" min="1" max="5" class="form-control" value="<?= htmlspecialchars($editItem['stars'] ?? 5) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? 1) ?>">
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
      <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
      <label class="form-label" style="margin:0">Active</label>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_testimonial" class="btn btn-primary">Save Testimonial</button>
      <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Quote</th><th>Author</th><th>Company</th><th>Stars</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $item): ?>
      <tr>
        <td class="truncate" style="max-width:320px"><?= htmlspecialchars($item['quote']) ?></td>
        <td><strong><?= htmlspecialchars($item['author']) ?></strong></td>
        <td class="text-sm text-muted"><?= htmlspecialchars($item['company']) ?></td>
        <td><?= str_repeat('★', (int)$item['stars']) ?></td>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><span class="td-badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?edit=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?del=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
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
