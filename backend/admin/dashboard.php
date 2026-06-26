<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();

$totalProducts   = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$totalNews       = $pdo->query("SELECT COUNT(*) FROM news WHERE is_active=1")->fetchColumn();
$totalProjects   = $pdo->query("SELECT COUNT(*) FROM projects WHERE is_active=1")->fetchColumn();
$totalInquiries  = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$unreadInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE is_read=0")->fetchColumn();
$recentInquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 6")->fetchAll();

admin_head('Dashboard');
?>
<div class="admin-layout">
<?php admin_sidebar('dashboard'); ?>
<div class="main-content">
<?php admin_topbar('Dashboard', 'Welcome back, ' . ($_SESSION['admin_user'] ?? 'Admin')); ?>
<div class="page-body">

  <!-- Stat boxes -->
  <div class="stat-grid">
    <div class="stat-box">
      <div class="stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div>
        <div class="stat-box-value"><?= $totalProducts ?></div>
        <div class="stat-box-label">Products Active</div>
      </div>
    </div>
    <div class="stat-box">
      <div class="stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
      </div>
      <div>
        <div class="stat-box-value"><?= $totalNews ?></div>
        <div class="stat-box-label">News Articles</div>
      </div>
    </div>
    <div class="stat-box">
      <div class="stat-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
      </div>
      <div>
        <div class="stat-box-value"><?= $totalProjects ?></div>
        <div class="stat-box-label">Projects</div>
      </div>
    </div>
    <div class="stat-box">
      <div class="stat-icon" style="background:rgba(239,68,68,.1);color:var(--danger)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </div>
      <div>
        <div class="stat-box-value"><?= $totalInquiries ?></div>
        <div class="stat-box-label">Total Inquiries <?php if($unreadInquiries): ?><span style="color:var(--danger);font-size:0.7rem">(<?= $unreadInquiries ?> unread)</span><?php endif; ?></div>
      </div>
    </div>
  </div>

  <!-- Recent Inquiries -->
  <div class="card">
    <div class="flex items-center gap-3" style="margin-bottom:18px">
      <div class="card-title" style="margin:0">Recent Inquiries</div>
      <a href="inquiries.php" class="btn btn-sm btn-secondary ms-auto">View All</a>
    </div>
    <?php if ($recentInquiries): ?>
    <div class="table-wrap">
      <table>
        <thead><tr>
          <th>Name</th><th>Email</th><th>Product</th><th>Date</th><th>Status</th>
        </tr></thead>
        <tbody>
        <?php foreach ($recentInquiries as $inq): ?>
          <tr>
            <td><strong><?= htmlspecialchars($inq['name']) ?></strong></td>
            <td><?= htmlspecialchars($inq['email']) ?></td>
            <td><span class="truncate"><?= htmlspecialchars($inq['product_interest'] ?: '—') ?></span></td>
            <td class="text-muted text-sm"><?= date('d M Y', strtotime($inq['created_at'])) ?></td>
            <td><?php if (!$inq['is_read']): ?><span class="td-badge badge-inactive">New</span><?php else: ?><span class="td-badge badge-active">Read</span><?php endif; ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <div class="empty-state"><p>No inquiries yet.</p></div>
    <?php endif; ?>
  </div>

  <!-- Quick Links -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-top:20px">
    <?php foreach ([
      ['Hero Slider','hero.php','Edit hero images & text'],
      ['About','about.php','Edit company intro & bullets'],
      ['Solutions','solutions.php','Manage solution cards'],
      ['Products','products.php','Manage product catalogue'],
      ['Sub Categories','products.php?tab=subcategories','Edit category sub-items'],
      ['Product Filters','product_filters.php','Manage product chips'],
      ['Hot Sales','hot_sales.php','Edit featured highlight'],
      ['Testimonials','testimonials.php','Manage client quotes'],
      ['Strengths','strengths.php','Edit feature cards'],
      ['Recognitions','recognitions.php','Edit badges and certifications'],
      ['News','news.php','Add/Edit news articles'],
      ['Projects','projects.php','Manage project gallery'],
      ['Services','services.php','Manage service cards'],
      ['Stats','stats.php','Edit counter numbers'],
      ['Contact Info','contact_info.php','Update contact details'],
    ] as [$label,$href,$desc]): ?>
    <a href="<?= $href ?>" class="card" style="text-decoration:none;transition:border-color 0.2s">
      <div style="font-weight:700;font-size:0.92rem"><?= $label ?></div>
      <div style="font-size:0.78rem;color:var(--text-muted);margin-top:5px"><?= $desc ?></div>
    </a>
    <?php endforeach; ?>
  </div>

</div>
</div>
</div>
</body>
</html>
