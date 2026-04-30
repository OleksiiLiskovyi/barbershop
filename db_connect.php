<?php
$host     = 'localhost';
$dbname   = 'barbershop_db';
$username = 'root';
$password = '';

$link = mysqli_connect($host, $username, $password, $dbname);

if (!$link) {
    die("Помилка підключення: " . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8mb4");
?>
