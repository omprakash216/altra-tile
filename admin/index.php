<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/superadmin_helpers.php';
include 'DAL.php';

require_admin_login('login.php');

if (empty($_SESSION['superadmin_csrf_token'])) {
    try {
        $_SESSION['superadmin_csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['superadmin_csrf_token'] = sha1(uniqid((string)mt_rand(), true));
    }
}

$superadminCsrfToken = $_SESSION['superadmin_csrf_token'];
$availableBackups = is_superadmin() ? superadmin_list_backups() : [];

// Get dashboard statistics
try {
    $dal = new DAL();
    $connection = $dal->connection;
    
    // Basic counts
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_projects WHERE status='active'");
    $total_projects = $result->fetch_assoc()['total'];
    
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_enquiries");
    $total_enquiries = $result->fetch_assoc()['total'];
    
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_blog WHERE status='active'");
    $total_blogs = $result->fetch_assoc()['total'];
    
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_applicants");
    $total_applicants = $result->fetch_assoc()['total'];
    
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_banners WHERE status='active'");
    $total_banners = $result->fetch_assoc()['total'];
    
    // Inquiries and Price Requests
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_inquiries");
    $total_inquiries = $result->fetch_assoc()['total'];
    
    $result = $connection->query("SELECT COUNT(*) as total FROM tbl_price_requests");
    $total_price_requests = $result->fetch_assoc()['total'];
    
    // Monthly data for charts (last 6 months)
    $monthly_inquiries = [];
    $monthly_price_requests = [];
    $monthly_enquiries = [];
    
    for ($i = 5; $i >= 0; $i--) {
        $month_start = date('Y-m-01', strtotime("-$i months"));
        $month_end = date('Y-m-t', strtotime("-$i months"));
        $month_label = date('M Y', strtotime("-$i months"));
        
        $result = $connection->query("SELECT COUNT(*) as count FROM tbl_inquiries WHERE DATE(created_at) BETWEEN '$month_start' AND '$month_end'");
        $monthly_inquiries[] = [
            'month' => $month_label,
            'count' => (int)$result->fetch_assoc()['count']
        ];
        
        $result = $connection->query("SELECT COUNT(*) as count FROM tbl_price_requests WHERE DATE(created_at) BETWEEN '$month_start' AND '$month_end'");
        $monthly_price_requests[] = [
            'month' => $month_label,
            'count' => (int)$result->fetch_assoc()['count']
        ];
        
        $result = $connection->query("SELECT COUNT(*) as count FROM tbl_enquiries WHERE DATE(date) BETWEEN '$month_start' AND '$month_end'");
        $monthly_enquiries[] = [
            'month' => $month_label,
            'count' => (int)$result->fetch_assoc()['count']
        ];
    }
    
    // Status breakdown for inquiries
    $result = $connection->query("SELECT status, COUNT(*) as count FROM tbl_inquiries GROUP BY status");
    $inquiry_status = [];
    while ($row = $result->fetch_assoc()) {
        $inquiry_status[$row['status']] = (int)$row['count'];
    }
    
} catch(Exception $e) {
    $total_projects = $total_enquiries = $total_blogs = $total_applicants = $total_banners = 0;
    $total_inquiries = $total_price_requests = 0;
    $monthly_inquiries = $monthly_price_requests = $monthly_enquiries = [];
    $inquiry_status = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vanaya Spaces</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Theme Colors */
        :root {
            --theme-primary: #350b01;
            --theme-secondary: #f6bd85;
            --theme-accent: #a76626;
            --theme-light: #f5ebdf;
            --theme-dark: #212529;
        }
        
        /* Apply IvyMode font to text elements, but preserve icon fonts */
        body, p, h1, h2, h3, h4, h5, h6, a:not([class*="fa"]):not([class*="flaticon"]), 
        span:not([class*="fa"]):not([class*="flaticon"]), div, button:not([class*="fa"]), 
        input, textarea, select, label, li, ul, td, th {
            font-family: "IvyMode", "Times New Roman", Times, serif !important;
        }
        
        /* Preserve Font Awesome icons */
        .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-light, .fa-brands,
        i[class*="fa-"], [class*="fa-"], i[class^="fa-"], span[class*="fa-"],
        span[class^="fa-"], [class^="fa-"], [class*=" fa-"] {
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
        [class*="flaticon-"], [class^="flaticon-"], .fi, [class*="flaticon"],
        i[class^="flaticon-"], i[class*=" flaticon-"], span[class^="flaticon-"],
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
        
        /* Theme Color Overrides */
        .bg-primary, .border-left-primary {
            background-color: var(--theme-primary) !important;
            border-left-color: var(--theme-primary) !important;
        }
        
        .bg-secondary, .border-left-secondary {
            background-color: var(--theme-secondary) !important;
            border-left-color: var(--theme-secondary) !important;
        }
        
        .bg-accent, .border-left-accent {
            background-color: var(--theme-accent) !important;
            border-left-color: var(--theme-accent) !important;
        }
        
        .text-primary {
            color: var(--theme-primary) !important;
        }
        
        .text-secondary {
            color: var(--theme-secondary) !important;
        }
        
        .text-accent {
            color: var(--theme-accent) !important;
        }
        
        /* Stats Cards */
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(53, 11, 1, 0.2) !important;
        }
        
        .stats-card-primary {
            border-left-color: var(--theme-primary);
        }
        
        .stats-card-secondary {
            border-left-color: var(--theme-secondary);
        }
        
        .stats-card-accent {
            border-left-color: var(--theme-accent);
        }
        
        .stats-icon {
            color: var(--theme-primary);
            opacity: 0.3;
        }
        
        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .chart-card canvas {
            flex: 1;
            min-height: 250px;
        }
        
        .chart-header {
            border-bottom: 2px solid var(--theme-light);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .chart-header h5 {
            color: var(--theme-primary);
            font-weight: 600;
            margin: 0;
        }
        
        /* Card Headers */
        .card-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            border: none;
        }
        
        .card-header h6 {
            color: white;
            font-weight: 600;
        }
        
        /* Sidebar Theme */
        .sidebar {
            background: var(--theme-primary) !important;
        }
        
        /* Navbar Theme */
        .navbar {
            background: var(--theme-primary) !important;
        }
        
        /* Body Background */
        body {
            background: var(--theme-light) !important;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2" style="color: var(--theme-primary);">Dashboard Overview</h1>
            <?php if (is_superadmin()): ?>
                <span class="badge bg-danger fs-6">Super Admin</span>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (is_superadmin()): ?>
            <div class="card shadow mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-user-shield me-2"></i>SUPER ADMIN ONLY</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <form method="POST" action="superadmin_actions.php">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($superadminCsrfToken); ?>">
                                <input type="hidden" name="action" value="backup_system">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-database me-2"></i>Backup System
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="POST" action="superadmin_actions.php" onsubmit="return confirmResetAction();">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($superadminCsrfToken); ?>">
                                <input type="hidden" name="action" value="reset_system">
                                <input type="hidden" name="confirm_text" id="resetConfirmText" value="">
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-eraser me-2"></i>Reset System
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="POST" action="superadmin_actions.php" onsubmit="return confirmRestoreAction();">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($superadminCsrfToken); ?>">
                                <input type="hidden" name="action" value="restore_data">
                                <input type="hidden" name="confirm_text" id="restoreConfirmText" value="">
                                <select class="form-select mb-2" name="backup_file">
                                    <option value="">Latest Backup</option>
                                    <?php foreach ($availableBackups as $backup): ?>
                                        <option value="<?php echo htmlspecialchars($backup['filename']); ?>">
                                            <?php echo htmlspecialchars($backup['filename']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-undo-alt me-2"></i>Restore Data
                                </button>
                            </form>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">
                        Total Backups: <?php echo (int)count($availableBackups); ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Projects</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-primary);"><?php echo $total_projects; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x stats-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-accent shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-accent text-uppercase mb-1">Property Inquiries</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-accent);"><?php echo $total_inquiries; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-question-circle fa-2x" style="color: var(--theme-accent); opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Price Requests</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-secondary);"><?php echo $total_price_requests; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x" style="color: var(--theme-secondary); opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Enquiries</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-primary);"><?php echo $total_enquiries; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-envelope fa-2x stats-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-accent shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-accent text-uppercase mb-1">Total Blogs</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-accent);"><?php echo $total_blogs; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-blog fa-2x" style="color: var(--theme-accent); opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Applicants</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-secondary);"><?php echo $total_applicants; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x" style="color: var(--theme-secondary); opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card stats-card-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Banners</div>
                                <div class="h5 mb-0 font-weight-bold" style="color: var(--theme-primary);"><?php echo $total_banners; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-image fa-2x stats-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Monthly Activity Chart -->
            <div class="col-lg-8">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5><i class="fas fa-chart-line me-2"></i>Monthly Activity Overview</h5>
                    </div>
                    <canvas id="monthlyActivityChart" height="250"></canvas>
                </div>
            </div>

            <!-- Inquiry Status Chart -->
            <div class="col-lg-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h5><i class="fas fa-chart-pie me-2"></i>Inquiry Status</h5>
                    </div>
                    <canvas id="inquiryStatusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-envelope me-2"></i>Recent Enquiries</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $dal = new DAL();
                            $connection = $dal->connection;
                            $result = $connection->query("SELECT * FROM tbl_enquiries ORDER BY date DESC LIMIT 5");
                            $enquiries = [];
                            while ($row = $result->fetch_assoc()) {
                                $enquiries[] = $row;
                            }
                            
                            if (empty($enquiries)) {
                                echo '<p class="text-muted">No enquiries yet.</p>';
                            } else {
                                foreach ($enquiries as $enquiry) {
                                    echo '<div class="mb-3 pb-3 border-bottom">';
                                    echo '<h6 class="mb-1" style="color: var(--theme-primary);">' . htmlspecialchars($enquiry['name']) . '</h6>';
                                    echo '<p class="text-muted mb-1">' . htmlspecialchars(substr($enquiry['message'], 0, 100)) . '...</p>';
                                    echo '<small class="text-muted"><i class="fas fa-calendar me-1"></i>' . date('M d, Y', strtotime($enquiry['date'])) . '</small>';
                                    echo '</div>';
                                }
                            }
                        } catch(Exception $e) {
                            echo '<p class="text-muted">Error loading enquiries.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include 'sidebar_end.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmResetAction() {
            const value = prompt('Type RESET to confirm system reset:');
            if (value === null) {
                return false;
            }
            document.getElementById('resetConfirmText').value = value.trim().toUpperCase();
            return true;
        }

        function confirmRestoreAction() {
            const value = prompt('Type RESTORE to confirm data restore:');
            if (value === null) {
                return false;
            }
            document.getElementById('restoreConfirmText').value = value.trim().toUpperCase();
            return true;
        }

        // Theme Colors
        const themeColors = {
            primary: '#350b01',
            secondary: '#f6bd85',
            accent: '#a76626',
            light: '#f5ebdf'
        };

        // Monthly Activity Chart
        const monthlyCtx = document.getElementById('monthlyActivityChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($monthly_inquiries, 'month')); ?>,
                    datasets: [
                        {
                            label: 'Property Inquiries',
                            data: <?php echo json_encode(array_column($monthly_inquiries, 'count')); ?>,
                            borderColor: themeColors.primary,
                            backgroundColor: themeColors.primary + '20',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Price Requests',
                            data: <?php echo json_encode(array_column($monthly_price_requests, 'count')); ?>,
                            borderColor: themeColors.accent,
                            backgroundColor: themeColors.accent + '20',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Contact Enquiries',
                            data: <?php echo json_encode(array_column($monthly_enquiries, 'count')); ?>,
                            borderColor: themeColors.secondary,
                            backgroundColor: themeColors.secondary + '20',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    family: 'IvyMode, Times New Roman, Times, serif'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: themeColors.primary,
                            padding: 12,
                            titleFont: {
                                family: 'IvyMode, Times New Roman, Times, serif'
                            },
                            bodyFont: {
                                family: 'IvyMode, Times New Roman, Times, serif'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    family: 'IvyMode, Times New Roman, Times, serif'
                                }
                            },
                            grid: {
                                color: themeColors.light
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    family: 'IvyMode, Times New Roman, Times, serif'
                                }
                            },
                            grid: {
                                color: themeColors.light
                            }
                        }
                    }
                }
            });
        }

        // Inquiry Status Chart
        const statusCtx = document.getElementById('inquiryStatusChart');
        if (statusCtx) {
            const statusData = {
                labels: ['New', 'Contacted', 'Closed'],
                datasets: [{
                    data: [
                        <?php echo isset($inquiry_status['new']) ? $inquiry_status['new'] : 0; ?>,
                        <?php echo isset($inquiry_status['contacted']) ? $inquiry_status['contacted'] : 0; ?>,
                        <?php echo isset($inquiry_status['closed']) ? $inquiry_status['closed'] : 0; ?>
                    ],
                    backgroundColor: [
                        themeColors.secondary,
                        themeColors.accent,
                        themeColors.primary
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            new Chart(statusCtx, {
                type: 'doughnut',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    family: 'IvyMode, Times New Roman, Times, serif'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: themeColors.primary,
                            padding: 12,
                            titleFont: {
                                family: 'IvyMode, Times New Roman, Times, serif'
                            },
                            bodyFont: {
                                family: 'IvyMode, Times New Roman, Times, serif'
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
