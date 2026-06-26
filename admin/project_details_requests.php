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
        
        if ($action == 'update_status') {
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_project_details_requests SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $request_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Request status updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating request status!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating request status: ' . $e->getMessage();
            }
        }
        
        if ($action == 'delete') {
            $request_id = $_POST['request_id'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("DELETE FROM tbl_project_details_requests WHERE id = ?");
                $stmt->bind_param("i", $request_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Request deleted successfully!';
                } else {
                    $_SESSION['error'] = 'Error deleting request!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error deleting request: ' . $e->getMessage();
            }
        }
    }
    
    header('Location: project_details_requests.php');
    exit();
}

// Get filter parameters
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
$filter_email = isset($_GET['filter_email']) ? $_GET['filter_email'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($filter_status)) {
    $where_conditions[] = "status = ?";
    $params[] = $filter_status;
    $param_types .= 's';
}

if (!empty($filter_name)) {
    $where_conditions[] = "first_name LIKE ?";
    $params[] = '%' . $filter_name . '%';
    $param_types .= 's';
}

if (!empty($filter_email)) {
    $where_conditions[] = "email LIKE ?";
    $params[] = '%' . $filter_email . '%';
    $param_types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get all requests with filters
try {
    $dal = new DAL();
    $connection = $dal->connection;
    
    $query = "SELECT * FROM tbl_project_details_requests $where_clause ORDER BY created_at DESC";
    
    if (!empty($params)) {
        $stmt = $connection->prepare($query);
        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $connection->query($query);
    }
    
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
} catch(Exception $e) {
    $requests = [];
}

// Get counts for statistics
try {
    $total_count = $connection->query("SELECT COUNT(*) as count FROM tbl_project_details_requests")->fetch_assoc()['count'];
    $new_count = $connection->query("SELECT COUNT(*) as count FROM tbl_project_details_requests WHERE status = 'new'")->fetch_assoc()['count'];
    $contacted_count = $connection->query("SELECT COUNT(*) as count FROM tbl_project_details_requests WHERE status = 'contacted'")->fetch_assoc()['count'];
    $closed_count = $connection->query("SELECT COUNT(*) as count FROM tbl_project_details_requests WHERE status = 'closed'")->fetch_assoc()['count'];
} catch(Exception $e) {
    $total_count = $new_count = $contacted_count = $closed_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details Requests Management - Vanaya Spaces Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --theme-primary: #350b01;
            --theme-secondary: #f6bd85;
            --theme-accent: #a76626;
            --theme-light: #f5ebdf;
        }
        
        body, p, h1, h2, h3, h4, h5, h6, a:not([class*="fa"]):not([class*="flaticon"]), 
        span:not([class*="fa"]):not([class*="flaticon"]), div, button:not([class*="fa"]), 
        input, textarea, select, label, li, ul, td, th {
            font-family: "IvyMode", "Times New Roman", Times, serif !important;
        }
        
        i:not([class*="fa"]):not([class*="flaticon"]) {
            font-family: "IvyMode", "Times New Roman", Times, serif !important;
        }
        
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
        
        body { background: var(--theme-light) !important; }
        
        .page-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(53, 11, 1, 0.2);
        }
        
        .page-header h1 { color: white; margin: 0; font-weight: 600; }
        
        .card { border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        .card-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
        
        .card-header h5 { color: white; margin: 0; font-weight: 600; }
        
        .stat-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        
        .stat-card.bg-primary { background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%) !important; }
        .stat-card.bg-warning { background: linear-gradient(135deg, var(--theme-secondary) 0%, #d4a574 100%) !important; color: var(--theme-primary) !important; }
        .stat-card.bg-info { background: linear-gradient(135deg, var(--theme-accent) 0%, #c88a4a 100%) !important; }
        .stat-card.bg-success { background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important; }
        
        .table { margin-bottom: 0; }
        
        .table thead { background: var(--theme-primary); color: white; }
        
        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr { transition: all 0.3s ease; }
        
        .table tbody tr:hover {
            background: var(--theme-light);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table tbody td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #e9ecef; }
        
        .btn-primary {
            background: var(--theme-primary);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: var(--theme-accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(53, 11, 1, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--theme-primary);
            color: var(--theme-primary);
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--theme-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline-success {
            border: 2px solid #28a745;
            color: #28a745;
            transition: all 0.3s ease;
        }
        
        .btn-outline-success:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            transition: all 0.3s ease;
        }
        
        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }
        
        .form-label {
            color: var(--theme-primary);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--theme-secondary);
            box-shadow: 0 0 0 0.2rem rgba(246, 189, 133, 0.25);
        }
        
        .form-select-sm {
            border: 1px solid var(--theme-secondary);
            border-radius: 6px;
            padding: 5px 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            color: var(--theme-secondary);
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: var(--theme-primary);
            margin-bottom: 10px;
        }
        
        .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            border: none;
            border-radius: 8px 8px 0 0;
            padding: 20px;
        }
        
        .modal-header .modal-title { color: white; font-weight: 600; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-body { padding: 25px; }
        .modal-footer { border: none; padding: 20px 25px; background: var(--theme-light); }
        
        @media (max-width: 768px) {
            .table { font-size: 0.9rem; }
            .table thead th, .table tbody td { padding: 10px 8px; }
            .page-header { padding: 15px; }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2 mb-0">
                            <i class="fas fa-file-alt me-2"></i>Project Details Requests Management
                        </h1>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_count; ?></h4>
                                        <p class="mb-0">Total Requests</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $new_count; ?></h4>
                                        <p class="mb-0">New</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $contacted_count; ?></h4>
                                        <p class="mb-0">Contacted</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-phone fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $closed_count; ?></h4>
                                        <p class="mb-0">Closed</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Requests</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="filter_status" class="form-label">Status</label>
                                <select class="form-select" id="filter_status" name="filter_status">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $filter_status === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="contacted" <?php echo $filter_status === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                    <option value="closed" <?php echo $filter_status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="filter_name" name="filter_name" 
                                       value="<?php echo htmlspecialchars($filter_name); ?>" 
                                       placeholder="Search by name...">
                            </div>
                            <div class="col-md-3">
                                <label for="filter_email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="filter_email" name="filter_email" 
                                       value="<?php echo htmlspecialchars($filter_email); ?>" 
                                       placeholder="Search by email...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filter
                                    </button>
                                    <a href="project_details_requests.php" class="btn btn-outline-secondary">
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

                <!-- Requests List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Project Details Requests
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($requests)): ?>
                            <div class="empty-state">
                                <i class="fas fa-file-alt"></i>
                                <h4>No Requests Yet</h4>
                                <p>Project details requests from customers will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Requirement</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><strong>#<?php echo $request['id']; ?></strong></td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($request['first_name'] ?? 'N/A'); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo $request['email']; ?>" style="color: var(--theme-accent); text-decoration: none;">
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($request['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo $request['phone']; ?>" style="color: var(--theme-accent); text-decoration: none;">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($request['phone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['address'])): ?>
                                                <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                                <span title="<?php echo htmlspecialchars($request['address']); ?>">
                                                    <?php echo htmlspecialchars(substr($request['address'], 0, 50)) . (strlen($request['address']) > 50 ? '...' : ''); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['requirement'])): ?>
                                                <span title="<?php echo htmlspecialchars($request['requirement']); ?>">
                                                    <?php echo htmlspecialchars(substr($request['requirement'], 0, 50)) . (strlen($request['requirement']) > 50 ? '...' : ''); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline;" onchange="this.submit()">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block; min-width: 120px;">
                                                    <option value="new" <?php echo $request['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                                    <option value="contacted" <?php echo $request['status'] == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                    <option value="closed" <?php echo $request['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            <?php echo date('d M Y', strtotime($request['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="sendEmail('<?php echo $request['email']; ?>', '<?php echo htmlspecialchars($request['first_name'] ?? 'Customer'); ?>')" title="Send Email">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" onclick="callPhone('<?php echo $request['phone']; ?>')" title="Call">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteRequest(<?php echo $request['id']; ?>)" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>
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
                    Are you sure you want to delete this request? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="request_id" id="delete_request_id">
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
            const subject = 'Regarding Your Project Details Request';
            const body = `Dear ${name},\n\nThank you for your request. We will get back to you soon.\n\nBest regards,\nVanaya Spaces Team`;
            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        }

        // Call phone function
        function callPhone(phone) {
            window.location.href = `tel:${phone}`;
        }

        // Delete request function
        function deleteRequest(requestId) {
            document.getElementById('delete_request_id').value = requestId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
