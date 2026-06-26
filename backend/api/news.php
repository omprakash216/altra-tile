<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT id, date_text AS date, category, title, summary, image FROM news WHERE is_active=1 ORDER BY created_at DESC")->fetchAll();
json_out($rows);
