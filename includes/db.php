<?php
// Load base path configuration
require_once __DIR__ . '/config.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "local_bus_monitoring";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
