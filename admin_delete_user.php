<?php
session_start();
include 'db.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: ../user_dashboard.php");
    exit();
}

// Delete user
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

header("Location: admin_manage_users.php"); // Redirect after deletion
exit();
?>
