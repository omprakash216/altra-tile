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

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_testimonial'])) {
        $name = $_POST['name'];
        $message_text = $_POST['message'];
        $status = $_POST['status'];
        
        // Handle file upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../assets/img/';
            $image_name = time() . '_' . $_FILES['image']['name'];
            $upload_path = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = 'assets/img/' . $image_name;
            }
        }
        
        try {
            $stmt = $dal->connection->prepare("INSERT INTO tbl_testimonials (name, message, image, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $message_text, $image, $status);
            $stmt->execute();
            $message = '<div class="alert alert-success">Testimonial added successfully!</div>';
        } catch(Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
    
    if (isset($_POST['edit_testimonial'])) {
        $testimonial_id = $_POST['testimonial_id'];
        $name = $_POST['name'];
        $message_text = $_POST['message'];
        $status = $_POST['status'];
        
        // Handle file upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../assets/img/';
            $image_name = time() . '_' . $_FILES['image']['name'];
            $upload_path = $upload_dir . $image_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = 'assets/img/' . $image_name;
            }
        }
        
        try {
            if ($image) {
                $stmt = $dal->connection->prepare("UPDATE tbl_testimonials SET name=?, message=?, image=?, status=? WHERE testimonial_id=?");
                $stmt->bind_param("ssssi", $name, $message_text, $image, $status, $testimonial_id);
                $stmt->execute();
            } else {
                $stmt = $dal->connection->prepare("UPDATE tbl_testimonials SET name=?, message=?, status=? WHERE testimonial_id=?");
                $stmt->bind_param("sssi", $name, $message_text, $status, $testimonial_id);
                $stmt->execute();
            }
            $message = '<div class="alert alert-success">Testimonial updated successfully!</div>';
        } catch(Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
    
    if (isset($_GET['delete'])) {
        try {
            $stmt = $dal->connection->prepare("DELETE FROM tbl_testimonials WHERE testimonial_id = ?");
            $stmt->bind_param("i", $_GET['delete']);
            $stmt->execute();
            $message = '<div class="alert alert-success">Testimonial deleted successfully!</div>';
        } catch(Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Get all testimonials
try {
    $testimonials = $dal->getData("SELECT * FROM tbl_testimonials ORDER BY created_at DESC");
} catch(Exception $e) {
    $testimonials = [];
}

// Get testimonial for editing
$edit_testimonial = null;
if (isset($_GET['edit'])) {
    try {
        $result = $dal->getData("SELECT * FROM tbl_testimonials WHERE testimonial_id = " . (int)$_GET['edit']);
        $edit_testimonial = !empty($result) ? $result[0] : null;
    } catch(Exception $e) {
        $edit_testimonial = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials Management - Vanaya Spaces Admin</title>
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
        
        .btn-warning {
            background: var(--theme-secondary);
            border: none;
            color: var(--theme-primary);
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background: var(--theme-accent);
            color: white;
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
        
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge.bg-success { background: #28a745 !important; }
        .badge.bg-secondary { background: #6c757d !important; }
        
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
        
        .img-thumbnail {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .img-thumbnail:hover { transform: scale(1.05); }
        
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
                            <i class="fas fa-quote-left me-2"></i>Testimonials Management
                        </h1>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                            <i class="fas fa-plus me-2"></i>Add New Testimonial
                        </button>
                    </div>
                </div>

                <?php echo $message; ?>

                <!-- Testimonials List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Testimonials
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($testimonials)): ?>
                            <div class="empty-state">
                                <i class="fas fa-quote-left"></i>
                                <h4>No Testimonials Yet</h4>
                                <p>Get started by adding your first testimonial.</p>
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                                    <i class="fas fa-plus me-2"></i>Add First Testimonial
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($testimonials as $testimonial): ?>
                                    <tr>
                                        <td><strong>#<?php echo $testimonial['testimonial_id']; ?></strong></td>
                                        <td>
                                            <?php if ($testimonial['image']): ?>
                                                <img src="../<?php echo $testimonial['image']; ?>" alt="Testimonial Image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 8px;">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($testimonial['name']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span title="<?php echo htmlspecialchars($testimonial['message']); ?>">
                                                <?php echo htmlspecialchars(substr($testimonial['message'], 0, 60)) . (strlen($testimonial['message']) > 60 ? '...' : ''); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $testimonial['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <i class="fas fa-<?php echo $testimonial['status'] == 'active' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                <?php echo ucfirst($testimonial['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            <?php echo date('d M Y', strtotime($testimonial['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?edit=<?php echo $testimonial['testimonial_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $testimonial['testimonial_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

    <!-- Add Testimonial Modal -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonial Message</label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_testimonial" class="btn btn-primary">Add Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Testimonial Modal -->
    <?php if ($edit_testimonial): ?>
    <div class="modal fade show" id="editTestimonialModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Testimonial</h5>
                    <a href="testimonials.php" class="btn-close"></a>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="testimonial_id" value="<?php echo $edit_testimonial['testimonial_id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($edit_testimonial['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Testimonial Message</label>
                            <textarea class="form-control" name="message" rows="4" required><?php echo htmlspecialchars($edit_testimonial['message']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <?php if ($edit_testimonial['image']): ?>
                                <img src="../<?php echo $edit_testimonial['image']; ?>" alt="Current Image" class="img-thumbnail d-block mb-2" style="width: 150px;">
                            <?php endif; ?>
                            <label class="form-label">Upload New Image (optional)</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?php echo $edit_testimonial['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $edit_testimonial['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="edit_testimonial" class="btn btn-primary">Update Testimonial</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
