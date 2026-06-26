<?php
// ============================================================
// DB Configuration & CORS Headers
// Edit the DB candidates, DB_USER, and DB_PASS to match your setup
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAMES', ['ultratech_cms', 'ultratech']);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function is_api_request(): bool {
    $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $script = str_replace('\\', '/', $script);
    return str_contains($script, '/backend/api/');
}

// Only API endpoints should emit JSON/CORS headers.
if (is_api_request()) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json; charset=utf-8');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $errors = [];
        foreach (DB_NAMES as $dbName) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . $dbName . ';charset=' . DB_CHARSET;
            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                break;
            } catch (PDOException $e) {
                $errors[] = $dbName . ': ' . $e->getMessage();
            }
        }

        if (!$pdo) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed: ' . implode(' | ', $errors)]);
            exit();
        }
    }
    return $pdo;
}

function json_out(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function decode_json_field(?string $val): array {
    if (!$val) return [];
    $decoded = json_decode($val, true);
    return is_array($decoded) ? $decoded : [];
}

function normalize_asset_path(?string $path): ?string {
    if ($path === null || $path === '') {
        return $path;
    }

    static $assetMap = [
        '/assets/1.jpeg' => '/assets/1.png',
        '/assets/2.jpeg' => '/assets/2.png',
        '/assets/3.jpeg' => '/assets/3.png',
        '/assets/4.jpeg' => '/assets/4.png',
        '/assets/5.jpeg' => '/assets/5.png',
        '/assets/6.jpeg' => '/assets/6.png',
        '/assets/7.jpeg' => '/assets/7.png',
        '/assets/8.jpeg' => '/assets/8.png',
        '/assets/9.jpeg' => '/assets/9.png',
        '/assets/block-machine.jpg' => '/assets/1.png',
        '/assets/hero-factory.jpg' => '/assets/10.jpeg',
        '/assets/aac-line.jpg' => '/assets/11.jpeg',
        '/assets/batching-plant.jpg' => '/assets/12.jpeg',
        '/assets/manufacturing-base.jpg' => '/assets/15.jpeg',
        '/assets/exhibition.jpg' => '/assets/16.jpeg',
    ];

    return $assetMap[$path] ?? $path;
}

function normalize_asset_fields(array &$row, array $fields = ['image']): void {
    foreach ($fields as $field) {
        if (array_key_exists($field, $row)) {
            $row[$field] = normalize_asset_path($row[$field]);
        }
    }
}

function upload_asset_file(string $field, ?string &$error = null, ?array $allowedMimeTypes = null, string $prefix = 'img_'): ?string {
    $error = null;

    $file = $_FILES[$field] ?? null;
    if (!$file || !is_array($file)) {
        return null;
    }

    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($uploadError !== UPLOAD_ERR_OK) {
        $error = 'Image upload failed.';
        return null;
    }

    $maxSizeBytes = 5 * 1024 * 1024;
    if (($file['size'] ?? 0) > $maxSizeBytes) {
        $error = 'Image size must be 5MB or smaller.';
        return null;
    }

    $uploadDir = realpath(__DIR__ . '/../../public/assets/');
    if (!$uploadDir) {
        $error = 'Upload directory not found.';
        return null;
    }

    if ($allowedMimeTypes === null) {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    }

    $mimeType = false;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
        }
    }
    if (!$mimeType && function_exists('mime_content_type')) {
        $mimeType = mime_content_type($file['tmp_name']);
    }

    if (!$mimeType || !in_array($mimeType, $allowedMimeTypes, true)) {
        $error = 'Only JPG, PNG, WebP, GIF allowed.';
        return null;
    }

    $extensionMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $ext = $extensionMap[$mimeType] ?? strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if ($ext === '') {
        $ext = 'img';
    }

    $safePrefix = preg_replace('/[^a-z0-9_-]+/i', '', $prefix);
    if ($safePrefix === '') {
        $safePrefix = 'img_';
    }

    $newFilename = $safePrefix . uniqid('', true) . '.' . $ext;
    $destination = $uploadDir . DIRECTORY_SEPARATOR . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $error = 'Failed to save file.';
        return null;
    }

    return '/assets/' . $newFilename;
}

function ensure_testimonials_table(PDO $pdo): void {
    $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote TEXT NOT NULL,
    author VARCHAR(200) NOT NULL,
    company VARCHAR(200) DEFAULT '',
    stars TINYINT UNSIGNED NOT NULL DEFAULT 5,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
SQL);
}
