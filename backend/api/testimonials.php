<?php
require_once __DIR__ . '/config.php';

$pdo = db();
ensure_testimonials_table($pdo);

$rows = $pdo->query("SELECT quote, author, company, stars FROM testimonials WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();
json_out($rows);
