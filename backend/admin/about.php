<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {
    $stmt = $pdo->prepare("UPDATE about_content SET eyebrow=?, title=?, description=?, years_badge=?, years_label=?, image=?, bullet_points=? WHERE id=1");
    $bulletPoints = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $_POST['bullet_points'] ?? ''))));
    $stmt->execute([
        trim($_POST['eyebrow']),
        trim($_POST['title']),
        trim($_POST['description']),
        trim($_POST['years_badge']),
        trim($_POST['years_label']),
        trim($_POST['image']),
        json_encode($bulletPoints),
    ]);
    $msg = 'About content updated!';
}

$about = $pdo->query("SELECT * FROM about_content LIMIT 1")->fetch();
if (!$about) {
    $pdo->exec("INSERT INTO about_content (eyebrow, title, description, years_badge, years_label, image, bullet_points) VALUES ('About Company', 'Manufacturing strength with a future-focused mindset', '', '30+', 'Years of Engineering Excellence', '/assets/15.jpeg', '[\"Advanced Technology\",\"Global Support\",\"Sustainable Solutions\",\"Reliable Performance\"]')");
    $about = $pdo->query("SELECT * FROM about_content LIMIT 1")->fetch();
}
$about['bullet_points'] = decode_json_field($about['bullet_points'] ?? null);

admin_head('About');
?>
<div class="admin-layout">
<?php admin_sidebar('about'); ?>
<div class="main-content">
<?php admin_topbar('About Section', 'Edit the content shown on the About page and home section'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="card">
  <div class="card-title">About Content</div>
  <form method="POST" class="form-grid">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Eyebrow Text</label>
        <input name="eyebrow" class="form-control" value="<?= htmlspecialchars($about['eyebrow'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Years Badge</label>
        <input name="years_badge" class="form-control" value="<?= htmlspecialchars($about['years_badge'] ?? '') ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Main Title</label>
      <input name="title" class="form-control" value="<?= htmlspecialchars($about['title'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($about['description'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Years Label</label>
        <input name="years_label" class="form-control" value="<?= htmlspecialchars($about['years_label'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Image Path</label>
        <input name="image" class="form-control" value="<?= htmlspecialchars(normalize_asset_path($about['image'] ?? '')) ?>" placeholder="/assets/15.jpeg">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Bullet Points (one per line)</label>
      <textarea name="bullet_points" class="form-control" rows="4"><?= htmlspecialchars(implode("\n", $about['bullet_points'] ?? [])) ?></textarea>
    </div>
    <div>
      <button type="submit" name="save_about" class="btn btn-primary">Save About Content</button>
    </div>
  </form>
</div>

</div>
</div>
</div>
</body>
</html>
