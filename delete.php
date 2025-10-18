<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "UPDATE notices SET status = 'inactive' WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    // Log the delete action
    $user_id = $_SESSION['user_id'];
    $log_sql = "INSERT INTO logs (user_id, action, notice_id, details) VALUES ($user_id, 'delete', $id, 'Set notice to inactive status')";
    mysqli_query($conn, $log_sql);
    header("Location: index.php");
} else {
    echo "Error updating record: " . mysqli_error($conn);
}
?>
