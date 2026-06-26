<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_project'])) {
    $id     = (int)($_POST['id'] ?? 0);
    $label  = trim($_POST['label']);
    $title  = trim($_POST['title']);
    $image  = trim($_POST['image']);
    $isLarge= isset($_POST['is_large']) ? 1 : 0;
    $sort   = (int)$_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE projects SET label=?,title=?,image=?,is_large=?,sort_order=?,is_active=? WHERE id=?")
            ->execute([$label,$title,$image,$isLarge,$sort,$active,$id]);
        $msg = 'Project updated!';
    } else {
        $pdo->prepare("INSERT INTO projects (label,title,image,is_large,sort_order,is_active) VALUES (?,?,?,?,?,?)")
            ->execute([$label,$title,$image,$isLarge,$sort,$active]);
        $msg = 'Project added!';
    }
    header('Location: projects.php?msg=' . urlencode($msg)); exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: projects.php?msg=Project+deleted'); exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM projects WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$projectsList = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC")->fetchAll();

admin_head('Projects');
?>
<div class="admin-layout">
<?php admin_sidebar('projects'); ?>
<div class="main-content">
<?php admin_topbar('Projects / Cases', 'Manage project gallery'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Projects (<?= count($projectsList) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Project</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Project' : 'Add Project' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Label (small tag)</label>
        <input name="label" class="form-control" value="<?= htmlspecialchars($editItem['label'] ?? '') ?>" placeholder="Global Exhibition">
      </div>
      <div class="form-group">
        <label class="form-label">Title *</label>
        <input name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="Machinery Innovation Showcase">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Image Path</label>
      <input name="image" class="form-control" value="<?= htmlspecialchars(normalize_asset_path($editItem['image'] ?? '')) ?>" placeholder="/assets/14.jpeg">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= $editItem['sort_order'] ?? 1 ?>">
      </div>
      <div class="form-group" style="justify-content:flex-end;flex-direction:row;gap:14px;align-items:center;padding-top:22px">
        <span class="form-label" style="margin:0">Large Card</span>
        <label class="toggle"><input type="checkbox" name="is_large" <?= ($editItem['is_large'] ?? 0) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
        <span class="form-label" style="margin:0">Active</span>
        <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_project" class="btn btn-primary">Save Project</button>
      <a href="projects.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Image</th><th>Label</th><th>Title</th><th>Large</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($projectsList as $p): ?>
      <tr>
        <td><img class="td-img" src="../../public<?= htmlspecialchars($p['image']) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2238%22><rect fill=%22%231a2235%22 width=%22100%25%22 height=%22100%25%22/></svg>'"></td>
        <td class="text-sm text-muted"><?= htmlspecialchars($p['label']) ?></td>
        <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
        <td><?= $p['is_large'] ? '<span class="td-badge badge-active">Yes</span>' : '—' ?></td>
        <td><?= $p['sort_order'] ?></td>
        <td><span class="td-badge <?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?del=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
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
