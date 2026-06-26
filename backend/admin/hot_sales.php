<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_hot_sale'])) {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $image = trim($_POST['image']);
    $output = trim($_POST['output_label']);
    $text = trim($_POST['description']);
    $tags = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $_POST['tags'] ?? ''))));
    $sort = (int)$_POST['sort_order'];
    $active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $pdo->prepare("UPDATE hot_sales SET name=?, image=?, output_label=?, description=?, tags=?, sort_order=?, is_active=? WHERE id=?")
            ->execute([$name, $image, $output, $text, json_encode($tags), $sort, $active, $id]);
        $msg = 'Hot sale updated!';
    } else {
        $pdo->prepare("INSERT INTO hot_sales (name, image, output_label, description, tags, sort_order, is_active) VALUES (?,?,?,?,?,?,?)")
            ->execute([$name, $image, $output, $text, json_encode($tags), $sort, $active]);
        $msg = 'Hot sale added!';
    }
    header('Location: hot_sales.php?msg=' . urlencode($msg));
    exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM hot_sales WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: hot_sales.php?msg=Hot+sale+deleted');
    exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM hot_sales WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
    if ($editItem) {
        $editItem['tags_text'] = implode("\n", decode_json_field($editItem['tags'] ?? null));
    }
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM hot_sales ORDER BY sort_order ASC")->fetchAll();

admin_head('Hot Sales');
?>
<div class="admin-layout">
<?php admin_sidebar('hot_sales'); ?>
<div class="main-content">
<?php admin_topbar('Hot Sales', 'Edit the featured highlight block on the home page'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Hot Sales (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Highlight</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Hot Sale' : 'Add New Hot Sale' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Name *</label>
        <input name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Output Label</label>
        <input name="output_label" class="form-control" value="<?= htmlspecialchars($editItem['output_label'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Image Path</label>
      <input name="image" class="form-control" value="<?= htmlspecialchars(normalize_asset_path($editItem['image'] ?? '')) ?>" placeholder="/assets/9.jpeg">
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Tags (one per line)</label>
      <textarea name="tags" class="form-control" rows="3"><?= htmlspecialchars($editItem['tags_text'] ?? '') ?></textarea>
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
      <button type="submit" name="save_hot_sale" class="btn btn-primary">Save Highlight</button>
      <a href="hot_sales.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Order</th><th>Name</th><th>Output</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $item): ?>
      <tr>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
        <td><?= htmlspecialchars($item['output_label']) ?></td>
        <td class="truncate"><?= htmlspecialchars($item['description']) ?></td>
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
