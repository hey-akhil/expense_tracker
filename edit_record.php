<?php
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date']; // Capturing the date from the form

    // Update query
    $query = "UPDATE expenses SET description = ?, category = ?, amount = ?, date = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssisi", $description, $category, $amount, $date, $id);

    if ($stmt->execute()) {
        // Calculate the new total after the update
        $total_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
        $total_stmt = $conn->prepare($total_query);
        $total_stmt->bind_param("i", $_SESSION['user_id']);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_row = $total_result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'new_total' => $total_row['total']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
