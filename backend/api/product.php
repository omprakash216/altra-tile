<?php
require_once __DIR__ . '/config.php';

$pdo = db();

$slug = $_GET['slug'] ?? null;
if ($slug) {
    $stmt = $pdo->prepare("SELECT slug AS id, category_slug, name, image, description, specs, features FROM product_subitems WHERE slug=? AND is_active=1 LIMIT 1");
    $stmt->execute([$slug]);
    $item = $stmt->fetch();
    
    if ($item) {
        $item['specs']    = decode_json_field($item['specs']);
        $item['features'] = decode_json_field($item['features']);
        normalize_asset_fields($item);

        // Fetch parent category for breadcrumb
        $cat = $pdo->prepare("SELECT slug AS id, name FROM product_categories WHERE slug=? LIMIT 1");
        $cat->execute([$item['category_slug']]);
        $item['category'] = $cat->fetch();

        json_out($item);
    }

    // Fallback: Check 'products' table
    $stmt2 = $pdo->prepare("SELECT slug AS id, title AS name, category_filter AS category_name, image, description, features FROM products WHERE slug=? AND is_active=1 LIMIT 1");
    $stmt2->execute([$slug]);
    $item2 = $stmt2->fetch();

    if ($item2) {
        $item2['features'] = decode_json_field($item2['features']);
        $item2['specs'] = []; 
        normalize_asset_fields($item2);
        $item2['category'] = ['id' => strtolower(str_replace(' ', '-', $item2['category_name'])), 'name' => $item2['category_name']];
        json_out($item2);
    }

    json_out(['error' => 'Product not found'], 404);
} else {
    json_out(['error' => 'slug required'], 400);
}
