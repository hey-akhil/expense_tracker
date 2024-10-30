<?php 
include 'header.php'; 
include 'db.php'; // Adjust path if necessary

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: user_dashboard.php");
    exit(); // Stop further code execution
}

// Get user_id and month from query parameters
if (isset($_GET['user_id']) && isset($_GET['month'])) {
    $userId = mysqli_real_escape_string($conn, $_GET['user_id']);
    $month = mysqli_real_escape_string($conn, $_GET['month']);
    
    // Fetch user details
    $userQuery = "SELECT * FROM users WHERE id = '$userId' AND approve = 1"; // Only approved users
    $userResult = mysqli_query($conn, $userQuery);
    
    if (mysqli_num_rows($userResult) === 0) {
        // User not found
        echo "<script>alert('User not found.');</script>";
        header("Location: view_all_expenses.php");
        exit();
    }
    
    $user = mysqli_fetch_assoc($userResult);
} else {
    header("Location: view_all_expenses.php");
    exit();
}

// Fetch all user's expenses for the selected month with full details
$expensesQuery = "
    SELECT id, user_id, type, amount, description, category, date 
    FROM expenses 
    WHERE user_id = '$userId' AND DATE_FORMAT(date, '%Y-%m') = '$month' 
    ORDER BY date DESC
";

$expensesResult = mysqli_query($conn, $expensesQuery);

// Check if the query was successful
if (!$expensesResult) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> - Monthly Expenses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class="mt-5 text-center"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>'s Monthly Expenses for <?php echo htmlspecialchars(date('F Y', strtotime($month . '-01'))); ?></h1>

        <div class="table-responsive">
            <table class="table mt-4 table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <!-- <th>User ID</th> -->
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Category</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($expensesResult) > 0): ?>
                        <?php while ($expense = mysqli_fetch_assoc($expensesResult)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['id']); ?></td>
                                <!-- <td>?php echo htmlspecialchars($expense['user_id']); ?></td> -->
                                <td><?php echo htmlspecialchars($expense['type']); ?></td>
                                <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                <td>₹<?php echo number_format($expense['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($expense['category']); ?></td>
                                <td><?php echo htmlspecialchars($expense['date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No expenses found for this month.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center">
            <a href="admin_user_expenses.php?user_id=<?php echo $user['id']; ?>" class="btn btn-secondary">Back to Monthly Summary</a>
        </div><br>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
