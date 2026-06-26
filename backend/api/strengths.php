<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT value_text AS `value`, label, icon_name AS icon FROM strengths ORDER BY sort_order ASC")->fetchAll();
json_out($rows);
