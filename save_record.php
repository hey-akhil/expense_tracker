<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    // Update record in database
    $query = "UPDATE expenses SET category = ?, amount = ?, created_at = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdsi", $category, $amount, $date, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
