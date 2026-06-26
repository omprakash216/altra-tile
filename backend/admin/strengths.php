<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$editItem = null;
$icons = ['Cog','Construction','Cpu','PackageCheck','Factory','Boxes','ShieldCheck','Headset'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_strength'])) {
    $id = (int)($_POST['id'] ?? 0);
    $value = trim($_POST['value_text']);
    $label = trim($_POST['label']);
    $icon = trim($_POST['icon_name']);
    $sort = (int)$_POST['sort_order'];

    if ($id) {
        $pdo->prepare("UPDATE strengths SET value_text=?, label=?, icon_name=?, sort_order=? WHERE id=?")
            ->execute([$value, $label, $icon, $sort, $id]);
        $msg = 'Strength updated!';
    } else {
        $pdo->prepare("INSERT INTO strengths (value_text, label, icon_name, sort_order) VALUES (?,?,?,?)")
            ->execute([$value, $label, $icon, $sort]);
        $msg = 'Strength added!';
    }
    header('Location: strengths.php?msg=' . urlencode($msg));
    exit();
}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM strengths WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: strengths.php?msg=Strength+deleted');
    exit();
}

if (isset($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM strengths WHERE id=?");
    $st->execute([(int)$_GET['edit']]);
    $editItem = $st->fetch();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];
$list = $pdo->query("SELECT * FROM strengths ORDER BY sort_order ASC")->fetchAll();

admin_head('Strengths');
?>
<div class="admin-layout">
<?php admin_sidebar('strengths'); ?>
<div class="main-content">
<?php admin_topbar('Strengths', 'Edit the feature cards shown in the Why Choose Us section'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="page-header">
  <div><h1>Strengths (<?= count($list) ?>)</h1></div>
  <a href="?add=1" class="btn btn-primary">+ Add Strength</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Strength' : 'Add New Strength' ?></div>
  <form method="POST" class="form-grid">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Value Text *</label>
        <input name="value_text" class="form-control" required value="<?= htmlspecialchars($editItem['value_text'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Label *</label>
        <input name="label" class="form-control" required value="<?= htmlspecialchars($editItem['label'] ?? '') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Icon</label>
        <select name="icon_name" class="form-control">
          <?php foreach ($icons as $icon): ?>
            <option <?= ($editItem['icon_name'] ?? '') === $icon ? 'selected' : '' ?>><?= $icon ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? 1) ?>">
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_strength" class="btn btn-primary">Save Strength</button>
      <a href="strengths.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Order</th><th>Value</th><th>Label</th><th>Icon</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($list as $item): ?>
      <tr>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><strong><?= htmlspecialchars($item['value_text']) ?></strong></td>
        <td><?= htmlspecialchars($item['label']) ?></td>
        <td><code style="font-size:0.8rem"><?= htmlspecialchars($item['icon_name']) ?></code></td>
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
