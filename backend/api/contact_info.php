<?php
require_once __DIR__ . '/config.php';

$pdo = db();
$row = $pdo->query("SELECT * FROM contact_info LIMIT 1")->fetch();
json_out($row ?: []);
