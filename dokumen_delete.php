<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "UPDATE dokumen SET status = 'inactive' WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    header("Location: dokumen.php");
} else {
    echo "Error updating record: " . mysqli_error($conn);
}
?>
