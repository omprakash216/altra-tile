<?php
require_once __DIR__ . '/config.php';

$pdo = db();

$slug = $_GET['slug'] ?? null;

if ($slug) {
    // Single category with sub-items
    $cat = $pdo->prepare("SELECT slug AS id, name, image, description, features FROM product_categories WHERE slug=? AND is_active=1 LIMIT 1");
    $cat->execute([$slug]);
    $category = $cat->fetch();
    if (!$category) {
        json_out(['error' => 'Category not found'], 404);
    }
    $category['features'] = decode_json_field($category['features']);
    normalize_asset_fields($category);

    $subs = $pdo->prepare("SELECT slug AS id, name, image, description, specs, features FROM product_subitems WHERE category_slug=? AND is_active=1 ORDER BY sort_order ASC");
    $subs->execute([$slug]);
    $subItems = $subs->fetchAll();
    foreach ($subItems as &$sub) {
        $sub['specs']    = decode_json_field($sub['specs']);
        $sub['features'] = decode_json_field($sub['features']);
        normalize_asset_fields($sub);
    }
    $category['subItems'] = $subItems;
    $category['subCategories'] = $subItems;
    json_out($category);
} else {
    // All categories with sub-items
    $cats = $pdo->query("SELECT slug AS id, name, image, description, features FROM product_categories WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
    foreach ($cats as &$cat) {
        $cat['features'] = decode_json_field($cat['features']);
        normalize_asset_fields($cat);
        $subs = $pdo->prepare("SELECT slug AS id, name, image, description, specs, features FROM product_subitems WHERE category_slug=? AND is_active=1 ORDER BY sort_order ASC");
        $subs->execute([$cat['id']]);
        $subItems = $subs->fetchAll();
        foreach ($subItems as &$sub) {
            $sub['specs']    = decode_json_field($sub['specs']);
            $sub['features'] = decode_json_field($sub['features']);
            normalize_asset_fields($sub);
        }
        $cat['subItems'] = $subItems;
        $cat['subCategories'] = $subItems;
    }
    json_out($cats);
}
