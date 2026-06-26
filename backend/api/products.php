<?php
require_once __DIR__ . '/config.php';

$pdo = db();

// Flat products list
$rows = $pdo->query("SELECT slug AS id, title, category_filter AS category, image, description, features FROM products WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
foreach ($rows as &$row) {
    $row['features'] = decode_json_field($row['features']);
    normalize_asset_fields($row);
}

// Filters
$filters = $pdo->query("SELECT label FROM product_filters ORDER BY sort_order ASC")->fetchAll();
$filterLabels = array_column($filters, 'label');

json_out([
    'products' => $rows,
    'filters'  => $filterLabels,
]);
