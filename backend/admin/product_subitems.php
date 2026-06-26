<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }

$params = ['tab' => 'subcategories'];
if (isset($_GET['add'])) {
    $params['add'] = 1;
}
if (isset($_GET['edit'])) {
    $params['edit_sub'] = (int)$_GET['edit'];
}
if (isset($_GET['del'])) {
    $params['del_sub'] = (int)$_GET['del'];
}
if (isset($_GET['msg'])) {
    $params['msg'] = $_GET['msg'];
}

header('Location: products.php?' . http_build_query($params));
exit();
