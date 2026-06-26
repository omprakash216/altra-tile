<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['error' => 'POST required'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$name    = trim($data['name']    ?? '');
$email   = trim($data['email']   ?? '');
$phone   = trim($data['phone']   ?? '');
$product = trim($data['product'] ?? '');
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$message) {
    json_out(['error' => 'Name, email and message are required'], 400);
}

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, product_interest, message) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $phone, $product, $message]);

json_out(['success' => true, 'message' => 'Inquiry submitted successfully']);
