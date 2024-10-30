<?php 
include 'header.php'; 
include 'db.php'; // Adjust path if necessary

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: user_dashboard.php");
    exit(); // Stop further code execution
}

// Get user_id from query parameters
if (isset($_GET['user_id'])) {
    $userId = mysqli_real_escape_string($conn, $_GET['user_id']);
    
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

// Fetch user's expenses grouped by month and year
$expensesQuery = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') AS month, 
        DATE_FORMAT(date, '%M %Y') AS month_year, 
        SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) AS total_expense,
        SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) AS total_income
    FROM expenses 
    WHERE user_id = '$userId' 
    GROUP BY month 
    ORDER BY month DESC
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
    <title><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> - Expenses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class="mt-5 text-center"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>'s Monthly Income and Expenses</h1>
        <br>

        <div class="table-responsive">
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Income</th>
                        <th>Total Expense</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($expensesResult) > 0): ?>
                        <?php while ($expense = mysqli_fetch_assoc($expensesResult)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['month_year']); ?></td>
                                <td>₹<?php echo number_format($expense['total_income'], 2); ?></td>
                                <td>₹<?php echo number_format($expense['total_expense'], 2); ?></td>
                                <td>
                                    <a href="admin_detailed_expenses.php?user_id=<?php echo $user['id']; ?>&month=<?php echo $expense['month']; ?>" class="btn btn-info btn-sm">View Details</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No income or expenses found for this user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center">
            <a href="admin_track_expenses.php" class="btn btn-secondary">Back to All Users</a>
        </div>
        
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
