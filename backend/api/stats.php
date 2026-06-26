<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$rows = $pdo->query("SELECT value_text AS `value`, label FROM stats ORDER BY sort_order ASC")->fetchAll();
json_out($rows);
