<?php
$host     = 'sql106.infinityfree.com';
$dbname   = 'if0_41973675_barbershop';
$username = 'if0_41973675';
$password = 'Lamaphp2026';

$link = mysqli_connect($host, $username, $password, $dbname);

if (!$link) {
    die("Помилка підключення: " . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8mb4");
?>
