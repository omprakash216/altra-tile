<?php
require_once __DIR__ . '/config.php';

$pdo = db();

// Hero content
$hero = $pdo->query("SELECT * FROM hero_content LIMIT 1")->fetch() ?: [];

// Hero slides
$slides = $pdo->query("SELECT image FROM hero_slides WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
$slides = array_map(function ($slide) {
    $slide['image'] = normalize_asset_path($slide['image'] ?? null);
    return $slide;
}, $slides);
$hero['slides'] = array_column($slides, 'image');
normalize_asset_fields($hero);

json_out($hero);
