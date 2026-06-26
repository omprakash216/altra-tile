<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    $pdo->prepare("UPDATE contact_info SET phone=?,phone_href=?,email=?,whatsapp=?,address=?,facebook=?,twitter=?,instagram=?,linkedin=?,youtube=? WHERE id=1")
        ->execute([
            trim($_POST['phone']), trim($_POST['phone_href']),
            trim($_POST['email']), trim($_POST['whatsapp']),
            trim($_POST['address']), trim($_POST['facebook']),
            trim($_POST['twitter']), trim($_POST['instagram']),
            trim($_POST['linkedin']), trim($_POST['youtube'])
        ]);
    $msg = 'Contact info updated!';
}

$info = $pdo->query("SELECT * FROM contact_info LIMIT 1")->fetch();

admin_head('Contact Info');
?>
<div class="admin-layout">
<?php admin_sidebar('contact_info'); ?>
<div class="main-content">
<?php admin_topbar('Contact Information', 'Update phone, email, address and social links'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="card">
  <div class="card-title">Contact Details</div>
  <form method="POST" class="form-grid">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Phone (display)</label>
        <input name="phone" class="form-control" value="<?= htmlspecialchars($info['phone'] ?? '') ?>" placeholder="+91 98765 43210">
      </div>
      <div class="form-group">
        <label class="form-label">Phone href (tel: link)</label>
        <input name="phone_href" class="form-control" value="<?= htmlspecialchars($info['phone_href'] ?? '') ?>" placeholder="tel:+919876543210">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($info['email'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">WhatsApp Link</label>
        <input name="whatsapp" class="form-control" value="<?= htmlspecialchars($info['whatsapp'] ?? '') ?>" placeholder="https://wa.me/919876543210">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Address</label>
      <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($info['address'] ?? '') ?></textarea>
    </div>

    <div style="font-size:0.82rem;font-weight:700;color:var(--text-dim);padding-top:4px">Social Media Links</div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Facebook URL</label>
        <input name="facebook" class="form-control" value="<?= htmlspecialchars($info['facebook'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Twitter / X URL</label>
        <input name="twitter" class="form-control" value="<?= htmlspecialchars($info['twitter'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Instagram URL</label>
        <input name="instagram" class="form-control" value="<?= htmlspecialchars($info['instagram'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">LinkedIn URL</label>
        <input name="linkedin" class="form-control" value="<?= htmlspecialchars($info['linkedin'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">YouTube URL</label>
        <input name="youtube" class="form-control" value="<?= htmlspecialchars($info['youtube'] ?? '') ?>">
      </div>
    </div>

    <div>
      <button type="submit" name="save_contact" class="btn btn-primary">Save Contact Info</button>
    </div>
  </form>
</div>

</div>
</div>
</div>
</body>
</html>
