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
$connection = $dal->connection;

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_video'])) {
        $video_id = (int)$_POST['video_id'];
        $video_url = $dal->validation($_POST['video_url']);
        $title = $dal->validation($_POST['title']);
        $description = !empty($_POST['description']) ? $dal->validation($_POST['description']) : '';
        $testimonial_date = !empty($_POST['testimonial_date']) ? $dal->validation($_POST['testimonial_date']) : NULL;
        $status = $dal->validation($_POST['status']);
        
        try {
            if ($video_id > 0) {
                // Update existing video
                $stmt = $connection->prepare("UPDATE tbl_video_testimonials SET video_url=?, title=?, description=?, testimonial_date=?, status=? WHERE video_id=?");
                $stmt->bind_param("sssssi", $video_url, $title, $description, $testimonial_date, $status, $video_id);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Video testimonial updated successfully!</div>';
                    echo "<script>window.location.href='video_testimonials.php';</script>";
                } else {
                    $message = '<div class="alert alert-danger">Error updating video testimonial!</div>';
                }
            } else {
                // Insert new video
                $stmt = $connection->prepare("INSERT INTO tbl_video_testimonials (video_url, title, description, testimonial_date, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $video_url, $title, $description, $testimonial_date, $status);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Video testimonial added successfully!</div>';
                    echo "<script>window.location.href='video_testimonials.php';</script>";
                } else {
                    $message = '<div class="alert alert-danger">Error adding video testimonial!</div>';
                }
            }
        } catch(Exception $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $video_id = (int)$_GET['delete'];
    try {
        $stmt = $connection->prepare("DELETE FROM tbl_video_testimonials WHERE video_id = ?");
        $stmt->bind_param("i", $video_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Video testimonial deleted successfully!</div>';
            echo "<script>window.location.href='video_testimonials.php';</script>";
        } else {
            $message = '<div class="alert alert-danger">Error deleting video testimonial!</div>';
        }
    } catch(Exception $e) {
        $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    }
}

// Get all video testimonials using DAL
$videos = $dal->getData("SELECT * FROM tbl_video_testimonials ORDER BY created_at DESC");
if (!$videos) {
    $videos = [];
}

// Get video for editing
$edit_video = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $video_id = (int)$_GET['id'];
    $result = $dal->getData("SELECT * FROM tbl_video_testimonials WHERE video_id = $video_id");
    if ($result && count($result) > 0) {
        $edit_video = $result[0];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Testimonials Management - Vanaya Spaces Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --theme-primary: #350b01;
            --theme-secondary: #f6bd85;
            --theme-accent: #a76626;
            --theme-light: #f5ebdf;
        }
        
        /* Apply IvyMode font to text elements, but preserve icon fonts */
        body, p, h1, h2, h3, h4, h5, h6, a:not([class*="fa"]):not([class*="flaticon"]), 
        span:not([class*="fa"]):not([class*="flaticon"]), div, button:not([class*="fa"]), 
        input, textarea, select, label, li, ul, td, th {
            font-family: "IvyMode", "Times New Roman", Times, serif !important;
        }
        
        /* Ensure i elements with icon classes keep their icon fonts */
        i:not([class*="fa"]):not([class*="flaticon"]) {
            font-family: "IvyMode", "Times New Roman", Times, serif !important;
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
        
        /* Body Background */
        body {
            background: var(--theme-light) !important;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(53, 11, 1, 0.2);
        }
        
        .page-header h1 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-accent) 100%);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
        
        .card-header h5 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        /* Table Styling */
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: var(--theme-primary);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: var(--theme-light);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        /* Video Preview */
        .video-preview-container {
            width: 200px;
            height: 112px;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            background: #000;
            display: block;
        }
        
        .video-preview-container iframe {
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            display: block;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .video-preview-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }
        
        .video-play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(246, 189, 133, 0.9);
            color: var(--theme-primary);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .video-preview-container:hover .video-play-overlay {
            background: var(--theme-secondary);
            transform: translate(-50%, -50%) scale(1.1);
        }
        
        /* Buttons */
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
        
        /* Badge Styling */
        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge.bg-success {
            background: #28a745 !important;
        }
        
        .badge.bg-secondary {
            background: #6c757d !important;
        }
        
        /* Empty State */
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
        
        /* Modal Styling */
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
        
        .modal-header .modal-title {
            color: white;
            font-weight: 600;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .modal-footer {
            border: none;
            padding: 20px 25px;
            background: var(--theme-light);
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
        
        /* Video URL Preview */
        .video-url-preview {
            margin-top: 10px;
            padding: 15px;
            background: var(--theme-light);
            border-radius: 6px;
            border-left: 4px solid var(--theme-primary);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .video-preview-container {
                width: 100%;
                max-width: 200px;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                        <h1 class="h2 mb-0">
                            <i class="fas fa-video me-2"></i>Video Testimonials Management
                        </h1>
                        <a href="video_testimonials.php?action=add" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Add New Video Testimonial
                        </a>
                    </div>
                </div>

                <?php echo $message; ?>

                <!-- Video Testimonials List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>All Video Testimonials
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($videos)): ?>
                            <div class="empty-state">
                                <i class="fas fa-video"></i>
                                <h4>No Video Testimonials Yet</h4>
                                <p>Get started by adding your first video testimonial.</p>
                                <a href="video_testimonials.php?action=add" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-2"></i>Add First Video Testimonial
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Video Preview</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($videos as $video): 
                                            // Extract YouTube video ID if it's a YouTube URL
                                            $video_url = trim($video['video_url']);
                                            $embed_url = '';
                                            $video_id = '';
                                            
                                            // YouTube URL patterns
                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $video_url, $matches)) {
                                                if (!empty($matches[1])) {
                                                    $video_id = $matches[1];
                                                    $embed_url = 'https://www.youtube.com/embed/' . $video_id . '?rel=0';
                                                }
                                            } 
                                            // Vimeo URL patterns
                                            elseif (preg_match('/(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d+)/', $video_url, $matches)) {
                                                if (!empty($matches[1])) {
                                                    $video_id = $matches[1];
                                                    $embed_url = 'https://player.vimeo.com/video/' . $video_id;
                                                }
                                            }
                                            
                                            $video_url_escaped = htmlspecialchars($video_url);
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo $video['video_id']; ?></strong></td>
                                            <td>
                                                <?php if ($embed_url): ?>
                                                    <div class="video-preview-container" style="width: 200px; height: 112px;">
                                                        <iframe 
                                                            src="<?php echo htmlspecialchars($embed_url); ?>" 
                                                            frameborder="0" 
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                            allowfullscreen
                                                            style="width: 100%; height: 100%; border: none; border-radius: 8px;">
                                                        </iframe>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="width: 200px; height: 112px; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 8px;">
                                                        <a href="<?php echo $video_url_escaped; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-play me-1"></i>View Video
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong style="color: var(--theme-primary);">
                                                    <?php echo htmlspecialchars($video['title']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php if ($video['description']): ?>
                                                    <span title="<?php echo htmlspecialchars($video['description']); ?>">
                                                        <?php echo strlen($video['description']) > 60 ? substr(htmlspecialchars($video['description']), 0, 60) . '...' : htmlspecialchars($video['description']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($video['testimonial_date']): ?>
                                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                                    <?php echo date('d M Y', strtotime($video['testimonial_date'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $video['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                    <i class="fas fa-<?php echo $video['status'] == 'active' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                                                    <?php echo ucfirst($video['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="?action=edit&id=<?php echo $video['video_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $video['video_id']; ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this video testimonial?')" title="Delete">
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

    <!-- Add/Edit Video Modal -->
    <?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
    <div class="modal fade show" id="videoModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $edit_video ? 'Edit' : 'Add New'; ?> Video Testimonial</h5>
                    <a href="video_testimonials.php" class="btn-close"></a>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="video_id" value="<?php echo $edit_video ? $edit_video['video_id'] : 0; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Video URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="video_url" 
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['video_url']) : ''; ?>" 
                                   placeholder="https://www.youtube.com/watch?v=..." required>
                            <small class="text-muted">Enter YouTube, Vimeo, or any video URL</small>
                        </div>
                        
                        <?php if ($edit_video && $edit_video['video_url']): 
                            $current_video_url = htmlspecialchars($edit_video['video_url']);
                            $current_embed_url = '';
                            if (strpos($current_video_url, 'youtube.com/watch') !== false || strpos($current_video_url, 'youtu.be/') !== false) {
                                preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $current_video_url, $matches);
                                if (!empty($matches[1])) {
                                    $current_embed_url = 'https://www.youtube.com/embed/' . $matches[1];
                                }
                            } elseif (strpos($current_video_url, 'vimeo.com/') !== false) {
                                preg_match('/vimeo\.com\/(\d+)/', $current_video_url, $matches);
                                if (!empty($matches[1])) {
                                    $current_embed_url = 'https://player.vimeo.com/video/' . $matches[1];
                                }
                            }
                        ?>
                        <div class="mb-3">
                            <label class="form-label">Current Video Preview</label>
                            <div class="video-url-preview">
                                <?php if ($current_embed_url): ?>
                                    <div class="video-preview-container" style="width: 100%; max-width: 500px; height: 281px;">
                                        <iframe src="<?php echo $current_embed_url; ?>" allowfullscreen></iframe>
                                    </div>
                                <?php else: ?>
                                    <a href="<?php echo $current_video_url; ?>" target="_blank" class="btn btn-info">
                                        <i class="fas fa-play me-2"></i>View Current Video
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?php echo $edit_video ? htmlspecialchars($edit_video['title']) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea class="form-control" name="description" rows="4"><?php echo $edit_video ? htmlspecialchars($edit_video['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date (Optional)</label>
                                    <input type="date" class="form-control" name="testimonial_date" 
                                           value="<?php echo $edit_video ? $edit_video['testimonial_date'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?php echo ($edit_video && $edit_video['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($edit_video && $edit_video['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="video_testimonials.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="save_video" class="btn btn-primary">
                            <?php echo $edit_video ? 'Update' : 'Add'; ?> Video Testimonial
                        </button>
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

