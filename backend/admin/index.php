<?php
session_start();
require_once __DIR__ . '/../api/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = db()->prepare("SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            header('Location: dashboard.php');
            exit();
        }
    }
    $error = 'Invalid username or password.';
}

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — ULTRA Tile Machine Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<meta name="robots" content="noindex,nofollow">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <img src="../../public/assets/logo.jpeg" alt="ULTRA Tile Machine" onerror="this.style.display='none'">
      <h1>ULTRA Tile Machine Admin</h1>
      <p>Sign in to manage your website content</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input id="username" name="username" class="form-control" placeholder="admin" required autocomplete="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:6px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Sign In
      </button>
    </form>
    <p style="text-align:center;margin-top:18px;font-size:0.75rem;color:var(--text-muted)">Default: admin / admin123</p>
  </div>
</div>
</body>
</html>
