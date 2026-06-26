<?php
session_start();
include 'DAL.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle AJAX request for getting project data
if (isset($_GET['ajax']) && $_GET['ajax'] == 'get_project') {
    $project_id = $_GET['id'] ?? 0;
    
    if ($project_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
        exit();
    }
    
    try {
        $dal = new DAL();
        $connection = $dal->connection;
        $stmt = $connection->prepare("SELECT * FROM tbl_projects WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $project = $result->fetch_assoc();
            echo json_encode(['success' => true, 'project' => $project]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Project not found']);
        }
        $stmt->close();
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add' || $action == 'edit') {
            $project_id = $_POST['project_id'] ?? 0;
            $name = $_POST['name'] ?? '';
            $location = $_POST['location'] ?? '';
            $price_range = $_POST['price_range'] ?? '';
            $description = $_POST['description'] ?? '';
            $rera_number = $_POST['rera_number'] ?? '';
            $property_type = $_POST['property_type'] ?? '';
            $bhk_config = $_POST['bhk_config'] ?? '';
            $area_range = $_POST['area_range'] ?? '';
            $possession_date = $_POST['possession_date'] ?? '';
            $builder_name = $_POST['builder_name'] ?? '';
            $key_features = $_POST['key_features'] ?? '';
            $amenities = $_POST['amenities'] ?? '';
            $address_details = $_POST['address_details'] ?? '';
            $floor_plans = $_POST['floor_plans'] ?? '';
            $disclaimer = $_POST['disclaimer'] ?? '';
            $status = $_POST['status'] ?? 'active';
            
            // Handle image upload
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../assets/img/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_name = time() . '_' . $_FILES['image']['name'];
                $upload_path = $upload_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'assets/img/' . $image_name;
                }
            }
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                
                if ($action == 'add' || $project_id == 0) {
                    // Insert new project
                    $stmt = $connection->prepare("INSERT INTO tbl_projects (name, location, price_range, description, image, rera_number, property_type, bhk_config, area_range, possession_date, builder_name, key_features, amenities, address_details, floor_plans, disclaimer, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssssssssssssss", $name, $location, $price_range, $description, $image, $rera_number, $property_type, $bhk_config, $area_range, $possession_date, $builder_name, $key_features, $amenities, $address_details, $floor_plans, $disclaimer, $status);
                    $success_msg = 'Project added successfully!';
                } else {
                    // Update existing project
                    if ($image) {
                        $stmt = $connection->prepare("UPDATE tbl_projects SET name=?, location=?, price_range=?, description=?, image=?, rera_number=?, property_type=?, bhk_config=?, area_range=?, possession_date=?, builder_name=?, key_features=?, amenities=?, address_details=?, floor_plans=?, disclaimer=?, status=? WHERE project_id=?");
                        $stmt->bind_param("sssssssssssssssssi", $name, $location, $price_range, $description, $image, $rera_number, $property_type, $bhk_config, $area_range, $possession_date, $builder_name, $key_features, $amenities, $address_details, $floor_plans, $disclaimer, $status, $project_id);
                    } else {
                        $stmt = $connection->prepare("UPDATE tbl_projects SET name=?, location=?, price_range=?, description=?, rera_number=?, property_type=?, bhk_config=?, area_range=?, possession_date=?, builder_name=?, key_features=?, amenities=?, address_details=?, floor_plans=?, disclaimer=?, status=? WHERE project_id=?");
                        $stmt->bind_param("ssssssssssssssssi", $name, $location, $price_range, $description, $rera_number, $property_type, $bhk_config, $area_range, $possession_date, $builder_name, $key_features, $amenities, $address_details, $floor_plans, $disclaimer, $status, $project_id);
                    }
                    $success_msg = 'Project updated successfully!';
                }
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = $success_msg;
                } else {
                    $_SESSION['error'] = 'Error saving project!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error adding project: ' . $e->getMessage();
            }
        }
        
        if ($action == 'update') {
            $project_id = $_POST['project_id'];
            $name = $_POST['name'];
            $location = $_POST['location'];
            $price_range = $_POST['price_range'];
            $description = $_POST['description'];
            $status = $_POST['status'];
            
            // Handle image upload
            $image = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../assets/img/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $image_name = time() . '_' . $_FILES['image']['name'];
                $upload_path = $upload_dir . $image_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'assets/img/' . $image_name;
                }
            }
            
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_projects SET name=?, location=?, price_range=?, description=?, image=?, status=? WHERE project_id=?");
                $stmt->bind_param("ssssssi", $name, $location, $price_range, $description, $image, $status, $project_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Project updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating project!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating project: ' . $e->getMessage();
            }
        }
        
        if ($action == 'delete') {
            $project_id = $_POST['project_id'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("DELETE FROM tbl_projects WHERE project_id = ?");
                $stmt->bind_param("i", $project_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Project deleted successfully!';
                } else {
                    $_SESSION['error'] = 'Error deleting project!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error deleting project: ' . $e->getMessage();
            }
        }
        
        if ($action == 'toggle_status') {
            $project_id = $_POST['project_id'];
            $status = $_POST['status'];
            try {
                $dal = new DAL();
                $connection = $dal->connection;
                $stmt = $connection->prepare("UPDATE tbl_projects SET status = ? WHERE project_id = ?");
                $stmt->bind_param("si", $status, $project_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Project status updated successfully!';
                } else {
                    $_SESSION['error'] = 'Error updating project status!';
                }
            } catch(Exception $e) {
                $_SESSION['error'] = 'Error updating project status: ' . $e->getMessage();
            }
        }
    }
    
    header('Location: projects.php');
    exit();
}

// Get all projects
try {
    $dal = new DAL();
    $connection = $dal->connection;
    $result = $connection->query("SELECT * FROM tbl_projects ORDER BY created_at ASC");
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
} catch(Exception $e) {
    $projects = [];
}

// Get project for editing
$edit_project = null;
if (isset($_GET['edit'])) {
    $project_id = $_GET['edit'];
    try {
        $dal = new DAL();
        $connection = $dal->connection;
        $stmt = $connection->prepare("SELECT * FROM tbl_projects WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $project_data = [];
        while ($row = $result->fetch_assoc()) {
            $project_data[] = $row;
        }
        if (!empty($project_data)) {
            $edit_project = $project_data[0];
        }
    } catch(Exception $e) {
        $edit_project = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management - Vanaya Spaces Admin</title>
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
                            <i class="fas fa-building me-2"></i>Projects Management
                        </h1>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                            <i class="fas fa-plus me-2"></i>Add New Project
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

                <!-- Projects List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Projects
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($projects)): ?>
                            <div class="empty-state">
                                <i class="fas fa-building"></i>
                                <h4>No Projects Yet</h4>
                                <p>Get started by adding your first project.</p>
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                                    <i class="fas fa-plus me-2"></i>Add First Project
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
                                        <th>Location</th>
                                        <th>Price Range</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><strong>#<?php echo $project['project_id']; ?></strong></td>
                                        <td>
                                            <?php if ($project['image']): ?>
                                                <img src="../<?php echo $project['image']; ?>" alt="Project Image" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 8px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-primary);">
                                                <?php echo htmlspecialchars($project['name']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                            <?php echo htmlspecialchars($project['location']); ?>
                                        </td>
                                        <td>
                                            <strong style="color: var(--theme-accent);">
                                                ₹ <?php echo htmlspecialchars($project['price_range']); ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $project['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                <i class="fas fa-<?php echo $project['status'] == 'active' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                <?php echo ucfirst($project['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-warning" onclick="editProject(<?php echo $project['project_id']; ?>)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-<?php echo $project['status'] == 'active' ? 'secondary' : 'success'; ?>" 
                                                        onclick="toggleStatus(<?php echo $project['project_id']; ?>, '<?php echo $project['status']; ?>')" 
                                                        title="<?php echo $project['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fas fa-<?php echo $project['status'] == 'active' ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteProject(<?php echo $project['project_id']; ?>)" title="Delete">
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

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="projectForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add" id="form_action">
                        <input type="hidden" name="project_id" value="0" id="project_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Project Name *</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Location *</label>
                                    <input type="text" class="form-control" name="location" id="location" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price Range *</label>
                                    <input type="text" class="form-control" name="price_range" id="price_range" placeholder="₹2 Cr - ₹4 Cr" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">RERA Number</label>
                                    <input type="text" class="form-control" name="rera_number" id="rera_number" placeholder="UPRERAPRJ459796/09/2025">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Property Type</label>
                                    <select class="form-select" name="property_type" id="property_type">
                                        <option value="">Select Type</option>
                                        <option value="apartment">Apartment</option>
                                        <option value="villa">Villa</option>
                                        <option value="plot">Plot</option>
                                        <option value="commercial">Commercial</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">BHK Configuration</label>
                                    <select class="form-select" name="bhk_config" id="bhk_config">
                                        <option value="">Select BHK</option>
                                        <option value="1bhk">1 BHK</option>
                                        <option value="2bhk">2 BHK</option>
                                        <option value="3bhk">3 BHK</option>
                                        <option value="4bhk">4 BHK</option>
                                        <option value="5bhk">5+ BHK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Area Range</label>
                                    <input type="text" class="form-control" name="area_range" id="area_range" placeholder="1785 - 4028 Sqft">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Possession Date</label>
                                    <input type="date" class="form-control" name="possession_date" id="possession_date">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Builder Name</label>
                                    <input type="text" class="form-control" name="builder_name" id="builder_name" placeholder="L&T Realty">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" id="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                        </div>
                        
                        <!-- Key Features -->
                        <div class="mb-3">
                            <label class="form-label">Key Features (One per line)</label>
                            <textarea class="form-control" name="key_features" id="key_features" rows="6" placeholder="Enter key features, one per line..."></textarea>
                        </div>
                        
                        <!-- Amenities -->
                        <div class="mb-3">
                            <label class="form-label">Amenities (One per line)</label>
                            <textarea class="form-control" name="amenities" id="amenities" rows="6" placeholder="Enter amenities, one per line..."></textarea>
                        </div>
                        
                        <!-- Address Details -->
                        <div class="mb-3">
                            <label class="form-label">Address Details (JSON format)</label>
                            <textarea class="form-control" name="address_details" id="address_details" rows="4" placeholder='{"address": "Sector 128, Noida", "city": "Noida", "state": "Uttar Pradesh", "pincode": "201304", "country": "India"}'></textarea>
                        </div>
                        
                        <!-- Floor Plans -->
                        <div class="mb-3">
                            <label class="form-label">Floor Plans (JSON format)</label>
                            <textarea class="form-control" name="floor_plans" id="floor_plans" rows="4" placeholder='[{"type": "3BHK", "area": "1785 Sqft"}, {"type": "4BHK", "area": "2501 Sqft"}]'></textarea>
                        </div>
                        
                        <!-- Disclaimer -->
                        <div class="mb-3">
                            <label class="form-label">Disclaimer (JSON format)</label>
                            <textarea class="form-control" name="disclaimer" id="disclaimer" rows="6" placeholder='{"facilities": "Disclaimer text...", "aerial_view": "Disclaimer text...", "floor_plans": "Disclaimer text...", "bookings": "Disclaimer text..."}'></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Project Image</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            <div id="current_image_preview" class="mt-2" style="display: none;">
                                <small class="text-muted">Current Image:</small><br>
                                <img id="current_image" src="" alt="Current Image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Project</button>
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
                    Are you sure you want to delete this project? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="project_id" id="delete_project_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit project function
        function editProject(projectId) {
            // Fetch project data via AJAX
            fetch('projects.php?ajax=get_project&id=' + projectId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate form with project data
                        document.getElementById('form_action').value = 'edit';
                        document.getElementById('project_id').value = data.project.project_id;
                        document.getElementById('name').value = data.project.name;
                        document.getElementById('location').value = data.project.location;
                        document.getElementById('price_range').value = data.project.price_range;
                        document.getElementById('rera_number').value = data.project.rera_number || '';
                        document.getElementById('property_type').value = data.project.property_type || '';
                        document.getElementById('bhk_config').value = data.project.bhk_config || '';
                        document.getElementById('area_range').value = data.project.area_range || '';
                        document.getElementById('possession_date').value = data.project.possession_date || '';
                        document.getElementById('builder_name').value = data.project.builder_name || '';
                        document.getElementById('key_features').value = data.project.key_features || '';
                        document.getElementById('amenities').value = data.project.amenities || '';
                        document.getElementById('address_details').value = data.project.address_details || '';
                        document.getElementById('floor_plans').value = data.project.floor_plans || '';
                        document.getElementById('disclaimer').value = data.project.disclaimer || '';
                        document.getElementById('status').value = data.project.status;
                        document.getElementById('description').value = data.project.description;
                        
                        // Show current image if exists
                        if (data.project.image) {
                            document.getElementById('current_image').src = '../' + data.project.image;
                            document.getElementById('current_image_preview').style.display = 'block';
                        } else {
                            document.getElementById('current_image_preview').style.display = 'none';
                        }
                        
                        // Update modal title and button
                        document.querySelector('#addProjectModal .modal-title').textContent = 'Edit Project';
                        document.querySelector('#addProjectModal .btn-primary').textContent = 'Update Project';
                        
                        // Show modal
                        new bootstrap.Modal(document.getElementById('addProjectModal')).show();
                    } else {
                        alert('Error loading project data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading project data');
                });
        }

        // Reset form when modal is closed
        document.getElementById('addProjectModal').addEventListener('hidden.bs.modal', function() {
            // Reset form
            document.getElementById('projectForm').reset();
            document.getElementById('form_action').value = 'add';
            document.getElementById('project_id').value = '0';
            document.getElementById('current_image_preview').style.display = 'none';
            
            // Reset modal title and button
            document.querySelector('#addProjectModal .modal-title').textContent = 'Add New Project';
            document.querySelector('#addProjectModal .btn-primary').textContent = 'Add Project';
        });

        // Delete project function
        function deleteProject(projectId) {
            document.getElementById('delete_project_id').value = projectId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Toggle status function
        function toggleStatus(projectId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="project_id" value="${projectId}">
                <input type="hidden" name="status" value="${newStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>

