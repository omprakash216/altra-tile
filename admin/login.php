<?php
require_once __DIR__ . '/auth.php';
include 'DbConfig.php';

$error = '';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        if (is_hardcoded_superadmin_login($username, $password)) {
            set_hardcoded_superadmin_session($username);
            header('Location: index.php');
            exit();
        }

        try {
            $db = new DbConfig();
            $connection = $db->connection;
            
            // Check connection
            if ($connection->connect_error) {
                throw new Exception('Database connection failed: ' . $connection->connect_error);
            }
            
            $hasRoleColumn = false;
            $columnResult = $connection->query("SHOW COLUMNS FROM tbl_admin LIKE 'role'");
            if ($columnResult && $columnResult->num_rows > 0) {
                $hasRoleColumn = true;
            }

            $sql = $hasRoleColumn
                ? "SELECT admin_id, username, password, role FROM tbl_admin WHERE username = ?"
                : "SELECT admin_id, username, password FROM tbl_admin WHERE username = ?";

            $stmt = $connection->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $connection->error);
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            
            $isValidPassword = false;
            if ($admin) {
                $storedPassword = $admin['password'];
                $isValidPassword = ($password === $storedPassword) || password_verify($password, $storedPassword);
            }

            if ($admin && $isValidPassword) {
                set_admin_session($admin);
                header('Location: index.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
            
            $stmt->close();
        } catch(Exception $e) {
            $error = 'Login error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vanaya Spaces</title>
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
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Vanaya Spaces</h2>
                            <p class="text-muted">Admin Login</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">Default: admin / admin123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
