<?php
declare(strict_types=1);

$dbHost = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: '127.0.0.1';
$dbPort = (int) (getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: 3306);
$dbName = getenv('DB_DATABASE') ?: getenv('MYSQL_DATABASE') ?: 'homeproject';
$dbUser = getenv('DB_USERNAME') ?: getenv('MYSQL_USER') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: '';

$conn = mysqli_init();

if ($conn === false) {
    die('Failed to initialize MySQL connection.');
}

mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

if (!mysqli_real_connect($conn, $dbHost, $dbUser, $dbPassword, $dbName, $dbPort)) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
