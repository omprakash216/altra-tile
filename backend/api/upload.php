<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'POST required'], 405);
}

// Check admin session
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    json_out(['error' => 'Unauthorized'], 401);
}

$error = null;
$path = upload_asset_file('image', $error, null, 'img_');
if (!$path) {
    json_out(['error' => $error ?? 'No valid file uploaded'], 400);
}

json_out(['success' => true, 'path' => $path]);
