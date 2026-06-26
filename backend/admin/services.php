<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;

$icons = ['Settings2','ShieldCheck','Cog','Headset','Wrench','Factory','Globe2','Layers3','Construction','PackageCheck','Building2','Microscope'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service'])) {
    $id     = (int)($_POST['id'] ?? 0);
    $title  = trim($_POST['title']);
    $text   = trim($_POST['text']);
    $icon   = trim($_POST['icon_name']);
    $sort   = (int)$_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE services SET title=?,text=?,icon_name=?,sort_order=?,is_active=? WHERE id=?")
            ->execute([$title,$text,$icon,$sort,$active,$id]);
        $msg = 'Service updated!';
    } else {
        $pdo->prepare("INSERT INTO services (title,text,icon_name,sort_order,is_active) VALUES (?,?,?,?,?)")
            ->execute([$title,$text,$icon,$sort,$active]);
        $msg = 'Service added!';
    }
    header('Location: services.php?msg=' . urlencode($msg)); exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: services.php?msg=Service+deleted'); exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM services WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();

admin_head('Services');
?>
<div class="admin-layout">
<?php admin_sidebar('services'); ?>
<div class="main-content">
<?php admin_topbar('Services Manager', 'Manage service cards displayed on the website'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Services (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Service</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Service' : 'Add Service' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Title *</label>
        <input name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Icon Name</label>
        <select name="icon_name" class="form-control">
          <?php foreach ($icons as $icon): ?>
            <option <?= ($editItem['icon_name'] ?? '') === $icon ? 'selected' : '' ?>><?= $icon ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="text" class="form-control"><?= htmlspecialchars($editItem['text'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= $editItem['sort_order'] ?? 1 ?>">
      </div>
      <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;padding-top:24px">
        <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
        <label class="form-label" style="margin:0">Active</label>
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_service" class="btn btn-primary">Save Service</button>
      <a href="services.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>#</th><th>Title</th><th>Icon</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $s): ?>
      <tr>
        <td><?= $s['sort_order'] ?></td>
        <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
        <td><code style="font-size:0.8rem"><?= htmlspecialchars($s['icon_name']) ?></code></td>
        <td class="truncate text-sm text-muted"><?= htmlspecialchars($s['text']) ?></td>
        <td><span class="td-badge <?= $s['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $s['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?del=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
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
