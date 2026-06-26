<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';

// Mark as read
if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE inquiries SET is_read=1 WHERE id=?")->execute([(int)$_GET['read']]);
    header('Location: inquiries.php'); exit();
}
// Delete
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM inquiries WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: inquiries.php?msg=Inquiry+deleted'); exit();
}
// Mark all read
if (isset($_GET['readall'])) {
    $pdo->query("UPDATE inquiries SET is_read=1");
    header('Location: inquiries.php'); exit();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];

$view = null;
if (isset($_GET['view'])) {
    $st = $pdo->prepare("SELECT * FROM inquiries WHERE id=?");
    $st->execute([(int)$_GET['view']]);
    $view = $st->fetch();
    if ($view && !$view['is_read']) {
        $pdo->prepare("UPDATE inquiries SET is_read=1 WHERE id=?")->execute([$view['id']]);
    }
}

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC")->fetchAll();
$unread = count(array_filter($inquiries, fn($i) => !$i['is_read']));

admin_head('Inquiries');
?>
<div class="admin-layout">
<?php admin_sidebar('inquiries'); ?>
<div class="main-content">
<?php admin_topbar('Inquiries Inbox', "$unread unread of " . count($inquiries) . " total"); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<?php if ($view): ?>
<!-- View Single Inquiry -->
<div class="card" style="margin-bottom:22px">
  <div class="flex items-center gap-3" style="margin-bottom:18px">
    <div class="card-title" style="margin:0">Inquiry Details</div>
    <a href="inquiries.php" class="btn btn-sm btn-secondary ms-auto">â† Back</a>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
    <div>
      <div class="form-label">Name</div>
      <div style="margin-top:4px;font-weight:600"><?= htmlspecialchars($view['name']) ?></div>
    </div>
    <div>
      <div class="form-label">Date</div>
      <div style="margin-top:4px"><?= date('d M Y, H:i', strtotime($view['created_at'])) ?></div>
    </div>
    <div>
      <div class="form-label">Email</div>
      <div style="margin-top:4px"><a href="mailto:<?= htmlspecialchars($view['email']) ?>" style="color:var(--accent)"><?= htmlspecialchars($view['email']) ?></a></div>
    </div>
    <div>
      <div class="form-label">Phone</div>
      <div style="margin-top:4px"><?= htmlspecialchars($view['phone'] ?: 'â€”') ?></div>
    </div>
    <div>
      <div class="form-label">Product Interest</div>
      <div style="margin-top:4px"><?= htmlspecialchars($view['product_interest'] ?: 'â€”') ?></div>
    </div>
  </div>
  <div>
    <div class="form-label">Message</div>
    <div style="margin-top:8px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px;font-size:0.9rem;line-height:1.7"><?= nl2br(htmlspecialchars($view['message'])) ?></div>
  </div>
  <div style="margin-top:16px;display:flex;gap:10px">
    <a href="mailto:<?= htmlspecialchars($view['email']) ?>?subject=Re: <?= urlencode($view['product_interest'] ?? 'Your Inquiry') ?>" class="btn btn-primary">Reply via Email</a>
    <a href="?del=<?= $view['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</a>
  </div>
</div>
<?php endif; ?>

<div class="page-header">
  <div><h1>Inquiries (<?= count($inquiries) ?>)</h1><?php if ($unread): ?><p><?= $unread ?> unread</p><?php endif; ?></div>
  <?php if ($unread): ?>
    <a href="?readall=1" class="btn btn-secondary btn-sm">Mark All Read</a>
  <?php endif; ?>
</div>

<?php if ($inquiries): ?>
<div class="table-wrap">
  <table>
    <thead><tr><th>Status</th><th>Name</th><th>Email</th><th>Product</th><th>Message</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($inquiries as $inq): ?>
      <tr style="<?= !$inq['is_read'] ? 'background:rgba(166,66,95,0.04)' : '' ?>">
        <td>
          <?php if (!$inq['is_read']): ?>
            <span class="td-badge badge-inactive">New</span>
          <?php else: ?>
            <span class="td-badge badge-active">Read</span>
          <?php endif; ?>
        </td>
        <td><strong><?= htmlspecialchars($inq['name']) ?></strong></td>
        <td class="text-sm"><?= htmlspecialchars($inq['email']) ?></td>
        <td class="text-sm text-muted truncate"><?= htmlspecialchars($inq['product_interest'] ?: 'â€”') ?></td>
        <td class="text-sm text-muted truncate"><?= htmlspecialchars(substr($inq['message'], 0, 60)) ?>...</td>
        <td class="text-sm text-muted"><?= date('d M Y', strtotime($inq['created_at'])) ?></td>
        <td class="flex gap-2">
          <a href="?view=<?= $inq['id'] ?>" class="btn btn-sm btn-secondary">View</a>
          <a href="?del=<?= $inq['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Del</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <div class="empty-state">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <p>No inquiries yet. They'll appear here when visitors submit the contact form.</p>
  </div>
<?php endif; ?>

</div>
</div>
</div>
</body>
</html>

