<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT title, text, icon_name AS icon FROM recognitions WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();
json_out($rows);
