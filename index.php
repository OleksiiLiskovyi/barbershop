<?php
$action = $_GET['action'] ?? 'main';

$view_path = "views/{$action}.php";
if (!file_exists($view_path)) {
    $view_path = 'views/main.php';
}

include 'layout/header.php';
include 'layout/left_menu.php';
include $view_path;
include 'layout/footer.php';
?>