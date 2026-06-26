<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = ''; $err = '';

// ---- SAVE STATS ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_stats'])) {
    foreach ($_POST['stat'] as $id => $data) {
        $pdo->prepare("UPDATE stats SET value_text=?, label=?, sort_order=? WHERE id=?")
            ->execute([trim($data['value']), trim($data['label']), (int)$data['sort'], (int)$id]);
    }
    $msg = 'Stats updated successfully!';
}

// ---- ADD STAT ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stat'])) {
    $sort = (int)$pdo->query("SELECT MAX(sort_order) FROM stats")->fetchColumn() + 1;
    $pdo->prepare("INSERT INTO stats (value_text, label, sort_order) VALUES (?, ?, ?)")
        ->execute([trim($_POST['new_value']), trim($_POST['new_label']), $sort]);
    $msg = 'Stat added!';
}

// ---- DELETE STAT ----
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM stats WHERE id=?")->execute([(int)$_GET['del']]);
    $msg = 'Stat deleted.';
}

$stats = $pdo->query("SELECT * FROM stats ORDER BY sort_order ASC")->fetchAll();

admin_head('Stats');
?>
<div class="admin-layout">
<?php admin_sidebar('stats'); ?>
<div class="main-content">
<?php admin_topbar('Stats / Counters', 'Edit the numbers shown on the homepage hero section'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="card">
  <div class="card-title">Homepage Stat Counters</div>
  <form method="POST">
    <div style="display:grid;gap:16px">
      <?php foreach ($stats as $stat): ?>
      <div style="display:grid;grid-template-columns:1fr 2fr 80px auto;gap:12px;align-items:center;padding:14px;background:var(--surface2);border-radius:10px;border:1px solid var(--border)">
        <div class="form-group">
          <label class="form-label">Value</label>
          <input name="stat[<?= $stat['id'] ?>][value]" class="form-control" value="<?= htmlspecialchars($stat['value_text']) ?>" placeholder="30+">
        </div>
        <div class="form-group">
          <label class="form-label">Label</label>
          <input name="stat[<?= $stat['id'] ?>][label]" class="form-control" value="<?= htmlspecialchars($stat['label']) ?>" placeholder="Years Experience">
        </div>
        <div class="form-group">
          <label class="form-label">Order</label>
          <input name="stat[<?= $stat['id'] ?>][sort]" type="number" class="form-control" value="<?= $stat['sort_order'] ?>">
        </div>
        <div style="padding-top:22px">
          <a href="?del=<?= $stat['id'] ?>" class="btn btn-sm btn-danger btn-icon" title="Delete" onclick="return confirm('Delete this stat?')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:18px">
      <button type="submit" name="save_stats" class="btn btn-primary">Save All Stats</button>
    </div>
  </form>
</div>

<!-- Add New Stat -->
<div class="card" style="margin-top:20px">
  <div class="card-title">Add New Stat</div>
  <form method="POST" class="form-row" style="align-items:flex-end">
    <div class="form-group">
      <label class="form-label">Value (e.g. 500+)</label>
      <input name="new_value" class="form-control" required placeholder="500+">
    </div>
    <div class="form-group">
      <label class="form-label">Label</label>
      <input name="new_label" class="form-control" required placeholder="Installations">
    </div>
    <div>
      <button type="submit" name="add_stat" class="btn btn-primary">Add Stat</button>
    </div>
  </form>
</div>

</div>
</div>
</div>
</body>
</html>
