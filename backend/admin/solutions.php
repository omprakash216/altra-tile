<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;
$icons = ['Factory','Construction','Cog','Boxes','Layers3','PackageCheck','Building2','Cpu','Headset'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_solution'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title']);
    $text = trim($_POST['text']);
    $icon = trim($_POST['icon_name']);
    $sort = (int)$_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE solutions SET title=?, text=?, icon_name=?, sort_order=?, is_active=? WHERE id=?")
            ->execute([$title, $text, $icon, $sort, $active, $id]);
        $msg = 'Solution updated!';
    } else {
        $pdo->prepare("INSERT INTO solutions (title, text, icon_name, sort_order, is_active) VALUES (?,?,?,?,?)")
            ->execute([$title, $text, $icon, $sort, $active]);
        $msg = 'Solution added!';
    }
    header('Location: solutions.php?msg=' . urlencode($msg));
    exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM solutions WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: solutions.php?msg=Solution+deleted');
    exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM solutions WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM solutions ORDER BY sort_order ASC")->fetchAll();

admin_head('Solutions');
?>
<div class="admin-layout">
<?php admin_sidebar('solutions'); ?>
<div class="main-content">
<?php admin_topbar('Solutions', 'Manage the process/solution cards shown on the home page'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Solutions (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Solution</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Solution' : 'Add New Solution' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Title *</label>
        <input name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Icon</label>
        <select name="icon_name" class="form-control">
          <?php foreach ($icons as $icon): ?>
            <option <?= ($editItem['icon_name'] ?? '') === $icon ? 'selected' : '' ?>><?= $icon ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="text" class="form-control" rows="4"><?= htmlspecialchars($editItem['text'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? 1) ?>">
      </div>
      <div class="form-group" style="display:flex;flex-direction:row;align-items:center;gap:10px;padding-top:24px">
        <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
        <label class="form-label" style="margin:0">Active</label>
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_solution" class="btn btn-primary">Save Solution</button>
      <a href="solutions.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Order</th><th>Title</th><th>Icon</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $item): ?>
      <tr>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><strong><?= htmlspecialchars($item['title']) ?></strong></td>
        <td><code style="font-size:0.8rem"><?= htmlspecialchars($item['icon_name']) ?></code></td>
        <td class="truncate"><?= htmlspecialchars($item['text']) ?></td>
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
