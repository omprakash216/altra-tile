<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_filter'])) {
    $id = (int)($_POST['id'] ?? 0);
    $label = trim($_POST['label']);
    $sort = (int)$_POST['sort_order'];

    if ($id) {
        $pdo->prepare("UPDATE product_filters SET label=?, sort_order=? WHERE id=?")
            ->execute([$label, $sort, $id]);
        $msg = 'Filter updated!';
    } else {
        $pdo->prepare("INSERT INTO product_filters (label, sort_order) VALUES (?,?)")
            ->execute([$label, $sort]);
        $msg = 'Filter added!';
    }
    header('Location: product_filters.php?msg=' . urlencode($msg));
    exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM product_filters WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: product_filters.php?msg=Filter+deleted');
    exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM product_filters WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM product_filters ORDER BY sort_order ASC")->fetchAll();

admin_head('Product Filters');
?>
<div class="admin-layout">
<?php admin_sidebar('product_filters'); ?>
<div class="main-content">
<?php admin_topbar('Product Filters', 'Edit the filter chips shown on the products section'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Filters (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Filter</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Filter' : 'Add New Filter' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Label *</label>
        <input name="label" class="form-control" required value="<?= htmlspecialchars($editItem['label'] ?? '') ?>" placeholder="BLOCK MAKING">
      </div>
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? 1) ?>">
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_filter" class="btn btn-primary">Save Filter</button>
      <a href="product_filters.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Order</th><th>Label</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $item): ?>
      <tr>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><strong><?= htmlspecialchars($item['label']) ?></strong></td>
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
