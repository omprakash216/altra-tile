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
        
        if ($action == 'add') {
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $description = $_POST['description'] ?? '';
            $button_text = $_POST['button_text'] ?? '';
            $button_link = $_POST['button_link'] ?? '';
            $status = $_POST['status'] ?? 'active';
            $sort_order = $_POST['sort_order'] ?? 0;
            
            // Handle image upload - REQUIRED
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../uploads/banners/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'banner_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'uploads/banners/' . $new_filename;
                }
            } else {
                $_SESSION['error'] = 'Image is required!';
                header('Location: banners.php');
                exit();
            }
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("INSERT INTO tbl_banners (title, subtitle, description, image, button_text, button_link, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssi", $title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Banner added successfully!';
                } else {
                    $_SESSION['error'] = 'Error adding banner!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error adding banner: ' . $e->getMessage();
            }
        }
        
        if ($action == 'update') {
            $banner_id = $_POST['banner_id'];
            $title = $_POST['title'];
            $subtitle = $_POST['subtitle'];
            $description = $_POST['description'];
            $button_text = $_POST['button_text'];
            $button_link = $_POST['button_link'];
            $status = $_POST['status'];
            $sort_order = $_POST['sort_order'];
            
            // Handle image upload
            $image = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../uploads/banners/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'banner_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'uploads/banners/' . $new_filename;
                }
            }
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_banners SET title=?, subtitle=?, description=?, image=?, button_text=?, button_link=?, status=?, sort_order=? WHERE banner_id=?");
                $stmt->bind_param("sssssssii", $title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order, $banner_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Banner updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating banner!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating banner: ' . $e->getMessage();
            }
        }
        
        if ($action == 'delete') {
            $banner_id = $_POST['banner_id'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("DELETE FROM tbl_banners WHERE banner_id = ?");
                $stmt->bind_param("i", $banner_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Banner deleted successfully!';
                } else {
                    $_SESSION['error'] = 'Error deleting banner!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error deleting banner: ' . $e->getMessage();
            }
        }
        
        if ($action == 'toggle_status') {
            $banner_id = $_POST['banner_id'];
            $status = $_POST['status'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_banners SET status = ? WHERE banner_id = ?");
                $stmt->bind_param("si", $status, $banner_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Banner status updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating banner status!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating banner status: ' . $e->getMessage();
            }
        }
    }
    
    header('Location: banners.php');
    exit();
}

// Get all banners
try {
    $dal = new DAL();
    $connection = $dal->connection;
    $result = $connection->query("SELECT * FROM tbl_banners ORDER BY sort_order ASC, created_at DESC");
    $banners = [];
    while ($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
} catch(Exception $e) {
    $banners = [];
}

// Get banner for editing
$edit_banner = null;
if (isset($_GET['edit'])) {
    $banner_id = $_GET['edit'];
    try {
        $dal = new DAL();
        $connection = $dal->connection;
        $stmt = $connection->prepare("SELECT * FROM tbl_banners WHERE banner_id = ?");
        $stmt->bind_param("i", $banner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $banner_data = [];
        while ($row = $result->fetch_assoc()) {
            $banner_data[] = $row;
        }
        if (!empty($banner_data)) {
            $edit_banner = $banner_data[0];
        }
    } catch(Exception $e) {
        $edit_banner = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        
        :root {
            --theme-primary: #350b01;
            --theme-secondary: #f6bd85;
            --theme-accent: #a76626;
            --theme-light: #f5ebdf;
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
        
        .btn-outline-warning {
            border: 2px solid var(--theme-secondary);
            color: var(--theme-accent);
            transition: all 0.3s ease;
        }
        
        .btn-outline-warning:hover {
            background: var(--theme-secondary);
            color: var(--theme-primary);
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
        
        .btn-light {
            background: white;
            border: 1px solid #ddd;
            color: var(--theme-primary);
            transition: all 0.3s ease;
        }
        
        .btn-light:hover {
            background: var(--theme-light);
            border-color: var(--theme-secondary);
        }
        
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge.bg-success { background: #28a745 !important; }
        .badge.bg-secondary { background: #6c757d !important; }
        
        .banner-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .banner-image:hover {
            transform: scale(1.05);
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
        
        @media (max-width: 768px) {
            .table { font-size: 0.9rem; }
            .table thead th, .table tbody td { padding: 10px 8px; }
            .page-header { padding: 15px; }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2 mb-0">
                            <i class="fas fa-image me-2"></i>Banner Management
                        </h1>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                            <i class="fas fa-plus me-2"></i>Add New Banner
                        </button>
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

                <!-- Banners List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Banners
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($banners)): ?>
                            <div class="empty-state">
                                <i class="fas fa-image"></i>
                                <h4>No Banners Yet</h4>
                                <p>Get started by adding your first banner.</p>
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                                    <i class="fas fa-plus me-2"></i>Add First Banner
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Subtitle</th>
                                        <th>Button Text</th>
                                        <th>Status</th>
                                        <th>Sort Order</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($banners as $banner): ?>
                                    <tr>
                                        <td>
                                            <?php if ($banner['image']): ?>
                                                <img src="../<?php echo $banner['image']; ?>" alt="Banner" class="banner-image">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 60px; border-radius: 8px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($banner['title']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span title="<?php echo htmlspecialchars($banner['subtitle']); ?>">
                                                <?php echo htmlspecialchars(substr($banner['subtitle'], 0, 50)) . (strlen($banner['subtitle']) > 50 ? '...' : ''); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($banner['button_text']): ?>
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($banner['button_text']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $banner['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <i class="fas fa-<?php echo $banner['status'] == 'active' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                <?php echo ucfirst($banner['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-accent);">
                                                #<?php echo $banner['sort_order']; ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-1 text-muted"></i>
                                            <?php echo date('d M Y', strtotime($banner['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="editBanner(<?php echo $banner['banner_id']; ?>)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-<?php echo $banner['status'] == 'active' ? 'warning' : 'success'; ?>" 
                                                        onclick="toggleStatus(<?php echo $banner['banner_id']; ?>, '<?php echo $banner['status']; ?>')" 
                                                        title="<?php echo $banner['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fas fa-<?php echo $banner['status'] == 'active' ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBanner(<?php echo $banner['banner_id']; ?>)" title="Delete">
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
            </div>
        </div>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter banner title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">Subtitle</label>
                                    <input type="text" class="form-control" id="subtitle" name="subtitle" placeholder="Enter banner subtitle">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter banner description"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text" placeholder="e.g., Learn More">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_link" class="form-label">Button Link</label>
                                    <input type="text" class="form-control" id="button_link" name="button_link" placeholder="e.g., projects.php">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Banner Image *</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                    <small class="text-muted">Only image upload is required. All other fields are optional.</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Banner Modal -->
    <div class="modal fade" id="editBannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="banner_id" id="edit_banner_id">
                        <input type="hidden" name="current_image" id="edit_current_image">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" placeholder="Enter banner title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_subtitle" class="form-label">Subtitle</label>
                                    <input type="text" class="form-control" id="edit_subtitle" name="subtitle" placeholder="Enter banner subtitle">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Enter banner description"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="edit_button_text" name="button_text" placeholder="e.g., Learn More">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_button_link" class="form-label">Button Link</label>
                                    <input type="text" class="form-control" id="edit_button_link" name="button_link" placeholder="e.g., projects.php">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_image" class="form-label">Banner Image</label>
                                    <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                    <small class="text-muted">Leave empty to keep current image. Only image upload is required for new banners.</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-select" id="edit_status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="edit_sort_order" name="sort_order" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this banner? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="banner_id" id="delete_banner_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit banner function
        function editBanner(bannerId) {
            // You would typically fetch banner data via AJAX here
            // For now, we'll redirect to edit page
            window.location.href = 'banners.php?edit=' + bannerId;
        }

        // Delete banner function
        function deleteBanner(bannerId) {
            document.getElementById('delete_banner_id').value = bannerId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Toggle status function
        function toggleStatus(bannerId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="banner_id" value="${bannerId}">
                <input type="hidden" name="status" value="${newStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        // Auto-populate edit modal if editing
        <?php if ($edit_banner): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('edit_banner_id').value = '<?php echo $edit_banner['banner_id']; ?>';
            document.getElementById('edit_title').value = '<?php echo htmlspecialchars($edit_banner['title']); ?>';
            document.getElementById('edit_subtitle').value = '<?php echo htmlspecialchars($edit_banner['subtitle']); ?>';
            document.getElementById('edit_description').value = '<?php echo htmlspecialchars($edit_banner['description']); ?>';
            document.getElementById('edit_button_text').value = '<?php echo htmlspecialchars($edit_banner['button_text']); ?>';
            document.getElementById('edit_button_link').value = '<?php echo htmlspecialchars($edit_banner['button_link']); ?>';
            document.getElementById('edit_status').value = '<?php echo $edit_banner['status']; ?>';
            document.getElementById('edit_sort_order').value = '<?php echo $edit_banner['sort_order']; ?>';
            document.getElementById('edit_current_image').value = '<?php echo $edit_banner['image']; ?>';
            
            new bootstrap.Modal(document.getElementById('editBannerModal')).show();
        });
        <?php endif; ?>
    </script>
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
