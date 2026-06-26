<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT label, title, image, is_large AS size FROM projects WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
foreach ($rows as &$row) {
    $row['size'] = $row['size'] ? 'large' : null;
    normalize_asset_fields($row);
}
json_out($rows);
