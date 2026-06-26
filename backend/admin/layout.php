<?php
// Shared admin layout helper
// Include this file at top of every admin page AFTER session check

function admin_sidebar(string $current = ''): void {
    $unread = 0;
    try {
        require_once __DIR__ . '/../api/config.php';
        $unread = (int) db()->query("SELECT COUNT(*) FROM inquiries WHERE is_read=0")->fetchColumn();
    } catch (Exception $e) {}
    ?>
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="../../public/assets/logo.jpeg" alt="ULTRA Tile Machine" onerror="this.style.display='none'">
            <span class="sidebar-logo-text">Admin Panel</span>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-section">Main</div>
            <a href="dashboard.php" class="sidebar-link <?= $current==='dashboard'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section">Catalog</div>
            <a href="hero.php" class="sidebar-link <?= $current==='hero'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                Hero Slider
            </a>
            <a href="products.php" class="sidebar-link <?= $current==='products'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Products
            </a>
            <a href="products.php?tab=subcategories" class="sidebar-link <?= $current==='product_subitems' || $current==='subcategories'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
                Sub Categories
            </a>
            <a href="product_filters.php" class="sidebar-link <?= $current==='product_filters'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                Product Filters
            </a>
            <a href="news.php" class="sidebar-link <?= $current==='news'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                News
            </a>
            <a href="projects.php" class="sidebar-link <?= $current==='projects'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Projects
            </a>

            <div class="sidebar-section">Content</div>
            <a href="about.php" class="sidebar-link <?= $current==='about'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                About Section
            </a>
            <a href="solutions.php" class="sidebar-link <?= $current==='solutions'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h10M4 18h16"/></svg>
                Solutions
            </a>
            <a href="services.php" class="sidebar-link <?= $current==='services'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Services
            </a>
            <a href="hot_sales.php" class="sidebar-link <?= $current==='hot_sales'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.9 6.2L21 9l-4.5 4.3 1.1 6.2L12 16.8 6.4 19.5 7.5 13.3 3 9l6.1-.8L12 2z"/></svg>
                Hot Sales
            </a>
            <a href="testimonials.php" class="sidebar-link <?= $current==='testimonials'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17h2l1-4V7H6v6h2z"/><path d="M15 17h2l1-4V7h-4v6h1z"/></svg>
                Testimonials
            </a>
            <a href="strengths.php" class="sidebar-link <?= $current==='strengths'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3 7h7l-5.5 4.1L18.5 20 12 15.8 5.5 20l2-6.9L2 9h7z"/></svg>
                Strengths
            </a>
            <a href="recognitions.php" class="sidebar-link <?= $current==='recognitions'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15l-4.5 2.4 1-5.1L4 8.9l5.3-.8L12 3l2.7 5.1 5.3.8-4.5 3.4 1 5.1z"/></svg>
                Recognitions
            </a>

            <div class="sidebar-section">Settings</div>
            <a href="stats.php" class="sidebar-link <?= $current==='stats'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Stats
            </a>
            <a href="contact_info.php" class="sidebar-link <?= $current==='contact_info'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Contact Info
            </a>
            <a href="inquiries.php" class="sidebar-link <?= $current==='inquiries'?'active':'' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Inquiries
                <?php if ($unread > 0): ?>
                    <span class="badge-dot"><?= $unread ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <div class="sidebar-bottom">
            <a href="logout.php" class="sidebar-link" style="color:var(--danger)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </a>
        </div>
    </aside>
    <?php
}

function admin_head(string $title): void {
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title) ?> — ULTRA Tile Machine Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<?php
}

function admin_topbar(string $title, string $subtitle = ''): void {
    ?>
    <div class="topbar">
        <div>
            <div class="topbar-title"><?= htmlspecialchars($title) ?></div>
            <?php if ($subtitle): ?><div class="text-sm text-muted mt-1"><?= htmlspecialchars($subtitle) ?></div><?php endif; ?>
        </div>
        <div class="topbar-right">
            <span class="text-sm text-muted"><?= date('d M Y') ?></span>
            <div class="topbar-avatar">A</div>
        </div>
    </div>
    <?php
}
