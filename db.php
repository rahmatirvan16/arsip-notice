<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'arsip_digital';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
