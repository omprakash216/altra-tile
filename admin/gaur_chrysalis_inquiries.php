<?php
session_start();
include 'DAL.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'delete') {
            $inquiry_id = $_POST['inquiry_id'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("DELETE FROM tbl_gaur_chrysalis_inquiry WHERE id = ?");
                $stmt->bind_param("i", $inquiry_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Inquiry deleted successfully!';
                } else {
                    $_SESSION['error'] = 'Error deleting inquiry!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error deleting inquiry: ' . $e->getMessage();
            }
        }
    }
    
    header('Location: gaur_chrysalis_inquiries.php');
    exit();
}

// Get filter parameters
$filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
$filter_email = isset($_GET['filter_email']) ? $_GET['filter_email'] : '';
$filter_mobile = isset($_GET['filter_mobile']) ? $_GET['filter_mobile'] : '';
$filter_date_from = isset($_GET['filter_date_from']) ? $_GET['filter_date_from'] : '';
$filter_date_to = isset($_GET['filter_date_to']) ? $_GET['filter_date_to'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($filter_name)) {
    $where_conditions[] = "name LIKE ?";
    $params[] = '%' . $filter_name . '%';
    $param_types .= 's';
}

if (!empty($filter_email)) {
    $where_conditions[] = "email LIKE ?";
    $params[] = '%' . $filter_email . '%';
    $param_types .= 's';
}

if (!empty($filter_mobile)) {
    $where_conditions[] = "mobile LIKE ?";
    $params[] = '%' . $filter_mobile . '%';
    $param_types .= 's';
}

if (!empty($filter_date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $filter_date_from;
    $param_types .= 's';
}

if (!empty($filter_date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $filter_date_to;
    $param_types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get all inquiries with filters
try {
    $dal = new DAL();
    $connection = $dal->connection;
    
    $query = "SELECT * FROM tbl_gaur_chrysalis_inquiry $where_clause ORDER BY created_at DESC";
    
    if (!empty($params)) {
        $stmt = $connection->prepare($query);
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $connection->query($query);
    }
    
    $inquiries = [];
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
} catch(Exception $e) {
    $inquiries = [];
    $message = 'Error loading inquiries: ' . $e->getMessage();
}

// Get counts for statistics
try {
    $total_count = $connection->query("SELECT COUNT(*) as count FROM tbl_gaur_chrysalis_inquiry")->fetch_assoc()['count'];
    $today_count = $connection->query("SELECT COUNT(*) as count FROM tbl_gaur_chrysalis_inquiry WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
    $week_count = $connection->query("SELECT COUNT(*) as count FROM tbl_gaur_chrysalis_inquiry WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
    $month_count = $connection->query("SELECT COUNT(*) as count FROM tbl_gaur_chrysalis_inquiry WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
} catch(Exception $e) {
    $total_count = $today_count = $week_count = $month_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaur Chrysalis Inquiries - Vanaya Spaces Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --theme-primary: #350b01;
            --theme-secondary: #f6bd85;
            --theme-accent: #a76626;
            --theme-light: #f5ebdf;
        }
        
        body {
            background: var(--theme-light) !important;
            font-family: "IvyMode", "Times New Roman", Times, serif;
        }
        
        .fa, .fas, .far, .fal, .fab {
            font-family: "Font Awesome 6 Free" !important;
        }
        
        /* Modern Header */
        .page-header-modern {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 8px 24px rgba(53, 11, 1, 0.25);
        }
        
        .page-header-modern h1 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Modern Statistics Cards */
        .stats-card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            background: white;
        }
        
        .stats-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--theme-secondary) 0%, var(--theme-accent) 100%);
        }
        
        .stats-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
        }
        
        .stats-card-modern .card-body {
            padding: 25px;
        }
        
        .stats-card-modern h4 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--theme-primary);
        }
        
        .stats-card-modern p {
            color: #6c757d;
            font-weight: 500;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .stats-card-modern i {
            font-size: 3rem;
            opacity: 0.2;
            color: var(--theme-primary);
        }
        
        /* Modern Filter Card */
        .filter-card-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            background: white;
        }
        
        .filter-card-modern .card-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            border: none;
            padding: 18px 25px;
            border-radius: 12px 12px 0 0;
        }
        
        .filter-card-modern .card-header h5 {
            color: white;
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .filter-card-modern .card-body {
            padding: 25px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--theme-secondary);
            box-shadow: 0 0 0 0.2rem rgba(246, 189, 133, 0.25);
            outline: none;
        }
        
        .form-label {
            color: var(--theme-primary);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        /* Modern Buttons */
        .btn-modern {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--theme-accent) 0%, var(--theme-primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(53, 11, 1, 0.3);
        }
        
        .btn-outline-secondary {
            border: 2px solid #dee2e6;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background: #f8f9fa;
            border-color: var(--theme-secondary);
            color: var(--theme-primary);
        }
        
        /* Modern Table */
        .table-modern {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .table-modern thead {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
        }
        
        .table-modern thead th {
            border: none;
            padding: 18px 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: white;
        }
        
        .table-modern tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table-modern tbody tr:hover {
            background: var(--theme-light);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .table-modern tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            color: #495057;
            font-size: 0.95rem;
        }
        
        /* Modern Action Buttons */
        .btn-group .btn {
            border-radius: 6px;
            margin: 0 2px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--theme-primary);
            border-color: var(--theme-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(53, 11, 1, 0.3);
        }
        
        .btn-outline-success:hover {
            background: #28a745;
            border-color: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .btn-outline-danger:hover {
            background: #dc3545;
            border-color: #dc3545;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        /* Modern Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        /* Link Styling */
        .table-modern a {
            color: var(--theme-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .table-modern a:hover {
            color: var(--theme-accent);
            text-decoration: underline;
        }
        
        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--theme-secondary);
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .stats-card-modern h4 {
                font-size: 2rem;
            }
            
            .page-header-modern {
                padding: 20px;
            }
            
            .page-header-modern h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>
                <!-- Modern Header -->
                <div class="page-header-modern">
                    <h1><i class="fas fa-home me-2"></i>Gaur Chrysalis Inquiries</h1>
                </div>

                <!-- Modern Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card-modern">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?php echo $total_count; ?></h4>
                                        <p>Total Inquiries</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card-modern">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?php echo $today_count; ?></h4>
                                        <p>Today</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card-modern">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?php echo $week_count; ?></h4>
                                        <p>This Week</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card-modern">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4><?php echo $month_count; ?></h4>
                                        <p>This Month</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modern Filter Form -->
                <div class="card filter-card-modern">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label for="filter_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="filter_name" name="filter_name" 
                                       value="<?php echo htmlspecialchars($filter_name); ?>" 
                                       placeholder="Search by name...">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="filter_email" name="filter_email" 
                                       value="<?php echo htmlspecialchars($filter_email); ?>" 
                                       placeholder="Search by email...">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="filter_mobile" name="filter_mobile" 
                                       value="<?php echo htmlspecialchars($filter_mobile); ?>" 
                                       placeholder="Search by mobile...">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="filter_date_from" name="filter_date_from" 
                                       value="<?php echo htmlspecialchars($filter_date_from); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="filter_date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="filter_date_to" name="filter_date_to" 
                                       value="<?php echo htmlspecialchars($filter_date_to); ?>">
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-modern w-100">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="gaur_chrysalis_inquiries.php" class="btn btn-outline-secondary btn-modern">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Modern Table -->
                <div class="card filter-card-modern">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                                        <th><i class="fas fa-user me-2"></i>Name</th>
                                        <th><i class="fas fa-envelope me-2"></i>Email</th>
                                        <th><i class="fas fa-phone me-2"></i>Mobile</th>
                                        <th><i class="fas fa-map-marker-alt me-2"></i>City</th>
                                        <th><i class="fas fa-calendar me-2"></i>Date</th>
                                        <th><i class="fas fa-clock me-2"></i>Time</th>
                                        <th><i class="fas fa-cog me-2"></i>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inquiries)): ?>
                                        <?php foreach ($inquiries as $inquiry): ?>
                                        <tr>
                                            <td><strong>#<?php echo $inquiry['id']; ?></strong></td>
                                            <td><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></td>
                                            <td>
                                                <a href="mailto:<?php echo $inquiry['email']; ?>" class="text-decoration-none">
                                                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($inquiry['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo $inquiry['mobile']; ?>" class="text-decoration-none">
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($inquiry['mobile']); ?>
                                                </a>
                                            </td>
                                            <td><i class="fas fa-map-marker-alt me-1 text-muted"></i><?php echo htmlspecialchars($inquiry['city'] ?? 'Noida'); ?></td>
                                            <td><i class="fas fa-calendar-alt me-1 text-muted"></i><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></td>
                                            <td><i class="fas fa-clock me-1 text-muted"></i><?php echo date('h:i A', strtotime($inquiry['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="sendEmail('<?php echo $inquiry['email']; ?>', '<?php echo htmlspecialchars($inquiry['name']); ?>')" title="Send Email">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="callPhone('<?php echo $inquiry['mobile']; ?>')" title="Call">
                                                        <i class="fas fa-phone"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteInquiry(<?php echo $inquiry['id']; ?>)" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h4>No inquiries found</h4>
                                                <p>There are no inquiries matching your criteria.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    <?php include 'sidebar_end.php'; ?>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this inquiry? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="inquiry_id" id="delete_inquiry_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Send email function
        function sendEmail(email, name) {
            const subject = 'Regarding Your Gaur Chrysalis Inquiry';
            const body = `Dear ${name},\n\nThank you for your inquiry about Gaur Chrysalis. We will get back to you soon.\n\nBest regards,\nVanaya Spaces Team`;
            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }

        // Call phone function
        function callPhone(phone) {
            window.location.href = `tel:${phone}`;
        }

        // Delete inquiry function
        function deleteInquiry(inquiryId) {
            document.getElementById('delete_inquiry_id').value = inquiryId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>

