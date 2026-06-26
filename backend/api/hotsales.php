<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT name, image, output_label AS output, description AS text, tags FROM hot_sales WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
foreach ($rows as &$row) {
    $row['tags'] = decode_json_field($row['tags']);
    normalize_asset_fields($row);
}
json_out($rows);
