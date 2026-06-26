<?php
session_start();
include 'DAL.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Create DAL instance
$dal = new DAL();

// Handle delete applicant
if (isset($_GET['delete'])) {
    try {
        $stmt = $dal->connection->prepare("DELETE FROM tbl_applicants WHERE id = ?");
        $stmt->bind_param("i", $_GET['delete']);
        $stmt->execute();
        $message = '<div class="alert alert-success">Application deleted successfully!</div>';
    } catch(Exception $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

// Get all applicants with job titles
try {
    $applicants = $dal->getData("SELECT a.*, c.title as job_title FROM tbl_applicants a LEFT JOIN tbl_careers c ON a.job_id = c.job_id ORDER BY a.date DESC");
} catch(Exception $e) {
    $applicants = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants Management - Vanaya Spaces Admin</title>
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
        
        .btn-info {
            background: var(--theme-accent);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-info:hover {
            background: var(--theme-primary);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: var(--theme-primary);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--theme-accent);
            transform: translateY(-2px);
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
                            <i class="fas fa-users me-2"></i>Job Applicants
                        </h1>
                    </div>
                </div>

                <?php if (isset($message)) echo $message; ?>

                <!-- Applicants List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Job Applicants
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($applicants)): ?>
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h4>No Applicants Yet</h4>
                                <p>Job applications will appear here when candidates apply.</p>
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
                                        <th>Applied For</th>
                                        <th>Date Applied</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applicants as $applicant): ?>
                                    <tr>
                                        <td><strong>#<?php echo $applicant['id']; ?></strong></td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($applicant['name']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($applicant['email']); ?>" style="color: var(--theme-accent);">
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($applicant['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($applicant['phone']); ?>" style="color: var(--theme-accent);">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($applicant['phone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <i class="fas fa-briefcase me-1 text-muted"></i>
                                            <?php echo htmlspecialchars($applicant['job_title']); ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            <?php echo date('d M Y H:i', strtotime($applicant['date'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewApplicantModal<?php echo $applicant['id']; ?>" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($applicant['resume']): ?>
                                                    <a href="../<?php echo $applicant['resume']; ?>" class="btn btn-sm btn-success" target="_blank" title="Download Resume">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="?delete=<?php echo $applicant['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- View Applicant Modal -->
                                    <div class="modal fade" id="viewApplicantModal<?php echo $applicant['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Applicant Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Name:</strong><br>
                                                            <?php echo htmlspecialchars($applicant['name']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Email:</strong><br>
                                                            <a href="mailto:<?php echo htmlspecialchars($applicant['email']); ?>">
                                                                <?php echo htmlspecialchars($applicant['email']); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Phone:</strong><br>
                                                            <a href="tel:<?php echo htmlspecialchars($applicant['phone']); ?>">
                                                                <?php echo htmlspecialchars($applicant['phone']); ?>
                                                            </a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Applied For:</strong><br>
                                                            <?php echo htmlspecialchars($applicant['job_title']); ?>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Date Applied:</strong><br>
                                                            <?php echo date('M d, Y H:i', strtotime($applicant['date'])); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Resume:</strong><br>
                                                            <?php if ($applicant['resume']): ?>
                                                                <a href="../<?php echo $applicant['resume']; ?>" class="btn btn-sm btn-success" target="_blank">
                                                                    <i class="fas fa-download"></i> Download Resume
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">No resume uploaded</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="mailto:<?php echo htmlspecialchars($applicant['email']); ?>" class="btn btn-primary">
                                                        <i class="fas fa-reply"></i> Contact
                                                    </a>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
