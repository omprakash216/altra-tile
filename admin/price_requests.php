<?php
session_start();
include 'DAL.php';

// Auth check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        try {
            $dal = new DAL();
            $connection = $dal->connection;
            $stmt = $connection->prepare("DELETE FROM tbl_price_requests WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Request deleted successfully!';
            } else {
                $_SESSION['error'] = 'Error deleting request!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        }
    }
    header('Location: price_requests.php');
    exit();
}

// Fetch requests
try {
    $dal = new DAL();
    $connection = $dal->connection;
    $result = $connection->query("SELECT * FROM tbl_price_requests ORDER BY created_at DESC");
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
} catch (Exception $e) {
    $requests = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Requests - Vanaya Spaces Admin</title>
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
        
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge.bg-success { background: #28a745 !important; }
        .badge.bg-secondary { background: #6c757d !important; }
        .badge.bg-primary { background: var(--theme-primary) !important; }
        .badge.bg-warning { background: var(--theme-secondary) !important; color: var(--theme-primary) !important; }
        
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
                            <i class="fas fa-rupee-sign me-2"></i>Price Requests
                        </h1>
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

                <!-- Price Requests List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Price Requests
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($requests)): ?>
                            <div class="empty-state">
                                <i class="fas fa-rupee-sign"></i>
                                <h4>No Price Requests Yet</h4>
                                <p>Price requests from customers will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Enquiry ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><strong>#<?php echo (int)$req['id']; ?></strong></td>
                                        <td>
                                            <span style="color: var(--theme-accent); font-weight: 600;">
                                                <?php echo htmlspecialchars($req['enquiry_id'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($req['first_name'] ?? ''); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($req['email']); ?>?subject=Regarding%20your%20price%20request&body=Hi%20<?php echo urlencode($req['first_name'] ?? ''); ?>," target="_blank" style="color: var(--theme-accent);">
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($req['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($req['phone']); ?>" style="color: var(--theme-accent);">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($req['phone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <i class="fas fa-building me-1 text-muted"></i>
                                            <?php echo htmlspecialchars($req['project_name'] ?? 'N/A'); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($req['status'] ?? 'new') == 'new' ? 'primary' : (($req['status'] == 'contacted' ? 'warning' : 'success')); ?>">
                                                <i class="fas fa-<?php echo ($req['status'] ?? 'new') == 'new' ? 'clock' : (($req['status'] == 'contacted' ? 'check' : 'check-circle')); ?> me-1"></i>
                                                <?php echo ucfirst($req['status'] ?? 'new'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($req['created_at']))); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-outline-success" href="https://wa.me/<?php echo preg_replace('/\D+/', '', $req['phone']); ?>?text=<?php echo urlencode('Hello ' . ($req['first_name'] ?? '') . ', regarding your property price request (ID ' . ($req['enquiry_id'] ?? '') . ').'); ?>" target="_blank" title="WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-primary" href="mailto:<?php echo htmlspecialchars($req['email']); ?>?subject=Regarding%20your%20price%20request%20(<?php echo urlencode($req['enquiry_id'] ?? ''); ?>)" title="Email">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <form method="POST" onsubmit="return confirm('Delete this request?');" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo (int)$req['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>

