<?php
$adminScriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin/index.php'));
$adminScriptDir = rtrim($adminScriptDir, '/');
if ($adminScriptDir === '' || $adminScriptDir === '.') {
    $adminScriptDir = '/admin';
}

$siteScriptDir = str_replace('\\', '/', dirname($adminScriptDir));
$siteScriptDir = rtrim($siteScriptDir, '/');
if ($siteScriptDir === '') {
    $siteScriptDir = '/';
}

$adminUrl = static function (string $path = '') use ($adminScriptDir): string {
    $base = rtrim($adminScriptDir, '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . '/' . $path;
};

$siteUrl = static function (string $path = '') use ($siteScriptDir): string {
    $base = rtrim($siteScriptDir, '/');
    if ($base === '') {
        $base = '/';
    }

    $path = ltrim($path, '/');
    if ($path === '') {
        return $base;
    }

    return $base === '/' ? '/' . $path : $base . '/' . $path;
};
?>
<style>
    /* Apply IvyMode font to text elements, but preserve icon fonts */
    /* Times New Roman commented - uncomment if IvyMode doesn't work well */
    body, p, h1, h2, h3, h4, h5, h6, a:not([class*="fa"]):not([class*="flaticon"]), 
    span:not([class*="fa"]):not([class*="flaticon"]), div, button:not([class*="fa"]), 
    input, textarea, select, label, li, ul, td, th {
        font-family: "IvyMode", "Times New Roman", Times, serif !important;
        /* font-family: "Times New Roman", Times, serif !important; */ /* Fallback - commented */
    }
    
    /* Ensure i elements with icon classes keep their icon fonts */
    i:not([class*="fa"]):not([class*="flaticon"]) {
        font-family: "IvyMode", "Times New Roman", Times, serif !important;
        /* font-family: "Times New Roman", Times, serif !important; */ /* Fallback - commented */
    }
    
    /* Preserve Font Awesome icons */
    .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-light, .fa-brands,
    i[class*="fa-"],
    [class*="fa-"],
    i[class^="fa-"],
    span[class*="fa-"],
    span[class^="fa-"],
    [class^="fa-"],
    [class*=" fa-"] {
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 5 Free", "FontAwesome" !important;
        font-style: normal !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
    }
    
    /* Preserve Flaticon icons */
    [class*="flaticon-"],
    [class^="flaticon-"],
    .fi,
    [class*="flaticon"],
    i[class^="flaticon-"],
    i[class*=" flaticon-"],
    span[class^="flaticon-"],
    span[class*=" flaticon-"] {
        font-family: flaticon_real_estate !important;
        font-style: normal !important;
        font-weight: normal !important;
        font-variant: normal !important;
        text-transform: none !important;
        line-height: 1 !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
    }
    
    .sidebar {
        background: #350b01 !important;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        z-index: 1000;
        transition: transform 0.3s ease, width 0.3s ease;
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    /* Custom Scrollbar for Sidebar */
    .sidebar::-webkit-scrollbar {
        width: 8px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(246, 189, 133, 0.5);
        border-radius: 4px;
        transition: background 0.3s ease;
    }
    
    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(246, 189, 133, 0.8);
    }
    
    /* Firefox Scrollbar */
    .sidebar {
        scrollbar-width: thin;
        scrollbar-color: rgba(246, 189, 133, 0.5) rgba(0, 0, 0, 0.1);
    }
    
    /* Sidebar Content */
    .sidebar-content {
        padding: 20px 0;
        min-height: 100%;
    }
    
    /* Ensure nav links are clickable */
    .sidebar .nav-link {
        cursor: pointer !important;
        pointer-events: auto !important;
        position: relative;
        z-index: 1;
        padding: 12px 20px !important;
        margin: 4px 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .sidebar .nav-link i {
        font-size: 18px;
        width: 24px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .sidebar .nav-link span {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .sidebar .nav-link:hover {
        background: rgba(246, 189, 133, 0.15);
        color: #f6bd85 !important;
        transform: translateX(5px);
    }
    
    .sidebar .nav-link:hover i {
        color: #f6bd85 !important;
        transform: scale(1.1);
    }
    
    .sidebar .nav-link.active {
        background: linear-gradient(135deg, rgba(246, 189, 133, 0.25) 0%, rgba(167, 102, 38, 0.2) 100%);
        color: #f6bd85 !important;
        border-left: 4px solid #f6bd85;
        box-shadow: 0 2px 8px rgba(246, 189, 133, 0.2);
    }
    
    .sidebar .nav-link.active i {
        color: #f6bd85 !important;
        transform: scale(1.15);
    }
    
    .sidebar .nav-link.active span {
        font-weight: 600;
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    /* Sidebar Toggle Button */
    .sidebar-toggle-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 1001;
        background: rgba(246, 189, 133, 0.2);
        border: 2px solid #f6bd85;
        color: #f6bd85;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .sidebar-toggle-btn:hover {
        background: #f6bd85;
        color: #350b01;
        transform: scale(1.1);
    }
    
    .sidebar-toggle-btn i {
        font-size: 18px;
    }
    
    /* Logo container with toggle button */
    .sidebar-header {
        position: relative;
        padding: 20px 60px 15px 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 80px;
    }
    
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
        pointer-events: none;
    }
    
    .sidebar-overlay.show {
        display: block;
        pointer-events: auto;
    }
    
    /* Ensure sidebar is above overlay when open */
    .sidebar:not(.collapsed) {
        z-index: 1001 !important;
    }
    
    .main-content {
        margin-left: 0;
        transition: margin-left 0.3s ease;
    }
    
    .main-content.sidebar-open {
        margin-left: 250px;
    }
    
    /* When sidebar is collapsed, adjust main content */
    body.sidebar-collapsed .main-content {
        margin-left: 0 !important;
    }
    
    @media (min-width: 768px) {
        .sidebar {
            /* Allow toggle on desktop too */
        }
        
        .sidebar.collapsed {
            transform: translateX(-100%);
        }
        
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        
        body.sidebar-collapsed .main-content {
            margin-left: 0 !important;
        }
        
        .sidebar-overlay {
            display: none !important;
        }
    }
    
    @media (max-width: 767px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .main-content.sidebar-open {
            margin-left: 0;
        }
    }
    
    /* Sidebar Logo Container - Match navbar height */
    .sidebar-logo-container {
        height: 60px; /* Same as navbar logo container */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 0; /* Same as navbar */
        margin: 0; /* Remove margin */
        /* Remove background and border radius */
    }
    
    /* Admin Logo Styles - Match navbar logo size */
    .admin-logo {
        height:178px; /* Same as navbar logo */
        width: auto;
        max-width: 200px; /* Same as navbar */
        object-fit: contain;
        /* Remove white filter - keep original colors */
        transition: all 0.3s ease;
    }
    
    .admin-logo:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }
    
    /* Mobile logo adjustments - Match navbar responsive sizes */
    @media (max-width: 768px) {
        .sidebar-logo-container {
            height: 50px; /* Match navbar mobile height */
        }
        .admin-logo {
            height: 40px; /* Match navbar mobile size */
            max-width: 160px;
        }
    }
    
    @media (max-width: 576px) {
        .sidebar-logo-container {
            height: 45px; /* Match navbar mobile height */
        }
        .admin-logo {
            height: 35px; /* Match navbar mobile size */
            max-width: 140px;
        }
    }
    
    @media (max-width: 480px) {
        .sidebar-logo-container {
            height: 40px; /* Match navbar mobile height */
        }
        .admin-logo {
            height: 30px; /* Match navbar mobile size */
            max-width: 120px;
        }
    }
    
    /* Navbar Logo Container */
    .navbar-logo-container {
        height: 60px;
        display: flex;
        align-items: center;
        padding: 5px 0;
    }
    
    /* Navbar Height Adjustment */
    .navbar {
        min-height: 70px;
        padding: 10px 0;
    }
    
    .navbar .container-fluid {
        align-items: center;
        min-height: 60px;
    }
    
    /* Navbar Logo Styles */
    .navbar-logo {
        height: 50px;
        width: auto;
        max-width: 200px;
        object-fit: contain;
        /* Remove white filter - keep original colors */
        transition: all 0.3s ease;
    }
    
    .navbar-logo:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }
    
    /* Navbar logo responsive */
    @media (max-width: 768px) {
        .navbar {
            min-height: 60px;
            padding: 8px 0;
        }
        .navbar-logo-container {
            height: 50px;
        }
        .navbar-logo {
            height: 40px;
            max-width: 160px;
        }
    }
    
    @media (max-width: 576px) {
        .navbar {
            min-height: 55px;
            padding: 6px 0;
        }
        .navbar-logo-container {
            height: 45px;
        }
        .navbar-logo {
            height: 35px;
            max-width: 140px;
        }
    }
    
    @media (max-width: 480px) {
        .navbar {
            min-height: 50px;
            padding: 5px 0;
        }
        .navbar-logo-container {
            height: 40px;
        }
        .navbar-logo {
            height: 30px;
            max-width: 120px;
        }
    }
    
    /* Include responsive styles */
    @import url('admin_responsive.css');
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: #350b01 !important;">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-bars" id="navbarToggleIcon"></i>
        </button>
        <a class="navbar-brand d-flex align-items-center" href="<?php echo htmlspecialchars($adminUrl('index.php')); ?>">
            <div class="navbar-logo-container">
                <img src="../assets/img/vanya_logo2.png" alt="Vanaya Spaces" class="navbar-logo">
            </div>
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="<?php echo htmlspecialchars($siteUrl('index.php')); ?>" target="_blank">
                <i class="fas fa-external-link-alt me-1"></i>
                <span class="d-none d-md-inline">View Website</span>
            </a>
            <a class="nav-link" href="<?php echo htmlspecialchars($adminUrl('logout.php')); ?>">
                <i class="fas fa-sign-out-alt me-1"></i>
                <span class="d-none d-md-inline">Logout</span>
            </a>
        </div>
    </div>
</nav>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <!-- Sidebar Header with Toggle Button and Logo -->
        <div class="sidebar-header">
            <!-- Toggle Button -->
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn" type="button" title="Toggle Sidebar">
                <i class="fas fa-bars" id="sidebarToggleIcon"></i>
            </button>
            <!-- Logo Section -->
            <div class="sidebar-logo-container">
                <img src="../assets/img/vanya_logo2.png" alt="Vanaya Spaces" class="admin-logo">
            </div>
        </div>
<!--         
        <div class="d-flex justify-content-between align-items-center px-3 pb-3 pt-3">
            <h5 class="text-white mb-0">Menu</h5>
            <button class="btn btn-outline-light btn-sm d-md-none" id="sidebarClose">
                <i class="fas fa-times"></i>
            </button>
        </div> -->
        <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('index.php')); ?>">
                            <i class="fas fa-chart-line"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'projects.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('projects.php')); ?>">
                            <i class="fas fa-city"></i> <span>Projects</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'blogs.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('blogs.php')); ?>">
                            <i class="fas fa-newspaper"></i> <span>Blogs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'testimonials.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('testimonials.php')); ?>">
                            <i class="fas fa-star"></i> <span>Testimonials</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'video_testimonials.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('video_testimonials.php')); ?>">
                            <i class="fas fa-play-circle"></i> <span>Video Testimonials</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'inquiries.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('inquiries.php')); ?>">
                            <i class="fas fa-inbox"></i> <span>Property Inquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'm3m_inquiries.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('m3m_inquiries.php')); ?>">
                            <i class="fas fa-building"></i> <span>M3M Jacob & Co. Inquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'gaur_chrysalis_inquiries.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('gaur_chrysalis_inquiries.php')); ?>">
                            <i class="fas fa-home"></i> <span>Gaur Chrysalis Inquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'sobha_inquiries.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('sobha_inquiries.php')); ?>">
                            <i class="fas fa-building"></i> <span>SOBHA Inquiries</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'price_requests.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('price_requests.php')); ?>">
                            <i class="fas fa-tags"></i> <span>Price Requests</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'project_details_requests.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('project_details_requests.php')); ?>">
                            <i class="fas fa-file-alt"></i> <span>Project Details Requests</span>
                        </a>
                    </li>
                     <li class="nav-item">
                         <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'banners.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('banners.php')); ?>">
                             <i class="fas fa-images"></i> <span>Banners</span>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'event_banners.php') ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($adminUrl('event_banners.php')); ?>">
                             <i class="fas fa-calendar-check"></i> <span>Event Banners</span>
                         </a>
                     </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'cache_manager.php') ? 'active' : ''; ?>" href="cache_manager.php">
                            <i class="fas fa-database"></i> Cache Manager
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'responsive_test.php') ? 'active' : ''; ?>" href="responsive_test.php">
                            <i class="fas fa-mobile-alt"></i> Responsive Test
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'logo_test.php') ? 'active' : ''; ?>" href="logo_test.php">
                            <i class="fas fa-image"></i> Logo Test
                        </a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'performance.php') ? 'active' : ''; ?>" href="performance.php">
                            <i class="fas fa-tachometer-alt"></i> Performance
                        </a>
                    </li> -->
                </ul>
            </div>
        </nav>
        
        <!-- Main content area starts here -->
        <main class="main-content px-md-4">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle'); // Navbar toggle (mobile)
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn'); // Sidebar toggle button
    const sidebarToggleIcon = document.getElementById('sidebarToggleIcon');
    const sidebar = document.querySelector('.sidebar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const mainContent = document.querySelector('.main-content');

    // Function to toggle sidebar
    function toggleSidebar() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        const navbarToggleIcon = document.getElementById('navbarToggleIcon');
        
        if (isCollapsed) {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
            if (sidebarToggleIcon) {
                sidebarToggleIcon.classList.remove('fa-times');
                sidebarToggleIcon.classList.add('fa-bars');
            }
            if (navbarToggleIcon) {
                navbarToggleIcon.classList.remove('fa-times');
                navbarToggleIcon.classList.add('fa-bars');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
            if (mainContent) {
                mainContent.classList.remove('sidebar-open');
            }
        } else {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (sidebarToggleIcon) {
                sidebarToggleIcon.classList.remove('fa-bars');
                sidebarToggleIcon.classList.add('fa-times');
            }
            if (navbarToggleIcon) {
                navbarToggleIcon.classList.remove('fa-bars');
                navbarToggleIcon.classList.add('fa-times');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('show');
            }
            if (mainContent) {
                mainContent.classList.add('sidebar-open');
            }
        }
        
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', !isCollapsed);
    }

    // Sidebar toggle button (inside sidebar)
    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Navbar toggle button (mobile)
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            toggleSidebar();
        });
    }

    // Overlay click to close
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            const navbarToggleIcon = document.getElementById('navbarToggleIcon');
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (sidebarToggleIcon) {
                sidebarToggleIcon.classList.remove('fa-bars');
                sidebarToggleIcon.classList.add('fa-times');
            }
            if (navbarToggleIcon) {
                navbarToggleIcon.classList.remove('fa-bars');
                navbarToggleIcon.classList.add('fa-times');
            }
            sidebarOverlay.classList.remove('show');
            if (mainContent) {
                mainContent.classList.remove('sidebar-open');
            }
            localStorage.setItem('sidebarCollapsed', true);
        });
    }

    // Restore sidebar state from localStorage
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        const navbarToggleIcon = document.getElementById('navbarToggleIcon');
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        if (sidebarToggleIcon) {
            sidebarToggleIcon.classList.remove('fa-bars');
            sidebarToggleIcon.classList.add('fa-times');
        }
        if (navbarToggleIcon) {
            navbarToggleIcon.classList.remove('fa-bars');
            navbarToggleIcon.classList.add('fa-times');
        }
        if (mainContent) {
            mainContent.classList.add('sidebar-open');
        }
    }
    
    // Ensure all sidebar nav links are clickable
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    sidebarLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Allow default link behavior
            // Don't prevent default - let the link navigate normally
            console.log('Link clicked:', this.href);
        });
        
        // Ensure pointer events are enabled
        link.style.pointerEvents = 'auto';
        link.style.cursor = 'pointer';
    });
});
</script>
