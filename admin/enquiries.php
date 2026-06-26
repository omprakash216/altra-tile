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
            $inquiry_id = $_POST['inquiry_id'];
            $status = $_POST['status'];
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_inquiries SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $inquiry_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Inquiry status updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating inquiry status!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating inquiry status: ' . $e->getMessage();
            }
        }
        
        if ($action == 'delete') {
            $inquiry_id = $_POST['inquiry_id'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("DELETE FROM tbl_inquiries WHERE id = ?");
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
    
    header('Location: enquiries.php');
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

// Get all inquiries with filters
try {
    $dal = new DAL();
    $connection = $dal->connection;
    
    $query = "SELECT * FROM tbl_inquiries $where_clause ORDER BY created_at DESC";
    
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
}

// Get counts for statistics
try {
    $total_count = $connection->query("SELECT COUNT(*) as count FROM tbl_inquiries")->fetch_assoc()['count'];
    $new_count = $connection->query("SELECT COUNT(*) as count FROM tbl_inquiries WHERE status = 'new'")->fetch_assoc()['count'];
    $contacted_count = $connection->query("SELECT COUNT(*) as count FROM tbl_inquiries WHERE status = 'contacted'")->fetch_assoc()['count'];
    $closed_count = $connection->query("SELECT COUNT(*) as count FROM tbl_inquiries WHERE status = 'closed'")->fetch_assoc()['count'];
} catch(Exception $e) {
    $total_count = $new_count = $contacted_count = $closed_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inquiries Management - Vanaya Spaces Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Property Inquiries Management</h1>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo $total_count; ?></h4>
                                        <p class="mb-0">Total Inquiries</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
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
                        <div class="card text-white bg-info">
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
                        <div class="card text-white bg-success">
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
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Inquiries</h5>
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
                                    <a href="enquiries.php" class="btn btn-outline-secondary">
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

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Project Name</th>
                                        <th>Requirements</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($inquiries)): ?>
                                        <?php foreach ($inquiries as $inquiry): ?>
                                        <tr>
                                            <td><?php echo $inquiry['id']; ?></td>
                                            <td><?php echo htmlspecialchars($inquiry['first_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo $inquiry['email']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($inquiry['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="tel:<?php echo $inquiry['phone']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($inquiry['phone']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo !empty($inquiry['project_name']) ? htmlspecialchars($inquiry['project_name']) : '-'; ?></td>
                                            <td><?php echo !empty($inquiry['requirements']) ? htmlspecialchars(substr($inquiry['requirements'], 0, 50)) . (strlen($inquiry['requirements']) > 50 ? '...' : '') : '-'; ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;" onchange="this.submit()">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                                        <option value="new" <?php echo $inquiry['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                                        <option value="contacted" <?php echo $inquiry['status'] == 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                        <option value="closed" <?php echo $inquiry['status'] == 'closed' ? 'selected' : ''; ?>>Closed</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($inquiry['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="sendEmail('<?php echo $inquiry['email']; ?>', '<?php echo htmlspecialchars($inquiry['first_name'] ?? 'Customer'); ?>')">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" onclick="callPhone('<?php echo $inquiry['phone']; ?>')">
                                                        <i class="fas fa-phone"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteInquiry(<?php echo $inquiry['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No inquiries found</td>
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
            const subject = 'Regarding Your Property Inquiry';
            const body = `Dear ${name},\n\nThank you for your inquiry. We will get back to you soon.\n\nBest regards,\nVanaya Spaces Team`;
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