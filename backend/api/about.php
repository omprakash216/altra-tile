<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$row = $pdo->query("SELECT * FROM about_content LIMIT 1")->fetch();
if ($row) {
    $row['bullet_points'] = decode_json_field($row['bullet_points']);
    normalize_asset_fields($row);
}
json_out($row ?: []);
