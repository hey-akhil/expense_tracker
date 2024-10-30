<?php 
include 'header.php'; 
include 'db.php'; // Adjust path if necessary

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: user_dashboard.php");
    exit(); // Stop further code execution
}

// Calculate total income
$totalIncomeQuery = "SELECT SUM(amount) AS total_income FROM expenses WHERE type = 'income'";
$totalIncomeResult = mysqli_query($conn, $totalIncomeQuery);
$totalIncome = mysqli_fetch_assoc($totalIncomeResult)['total_income'];

// Calculate total expenses
$totalExpensesQuery = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE type = 'expense'";
$totalExpensesResult = mysqli_query($conn, $totalExpensesQuery);
$totalExpenses = mysqli_fetch_assoc($totalExpensesResult)['total_expenses'];

// Calculate total number of users
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users WHERE approve = 1"; // Assuming only approved users are counted
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

// Expenses breakdown by category
$categoryBreakdownQuery = "
    SELECT category, SUM(amount) AS total_amount 
    FROM expenses 
    WHERE type = 'expense' 
    GROUP BY category 
    ORDER BY total_amount DESC
";
$categoryBreakdownResult = mysqli_query($conn, $categoryBreakdownQuery);

// Check if the query was successful
if (!$totalIncomeResult || !$totalExpensesResult || !$totalUsersResult || !$categoryBreakdownResult) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Statistics</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Statistics Overview</h1>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Total Users</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($totalUsers); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Total Income</div>
                    <div class="card-body">
                        <h5 class="card-title">₹<?php echo number_format($totalIncome, 2); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Total Expenses</div>
                    <div class="card-body">
                        <h5 class="card-title">₹<?php echo number_format($totalExpenses, 2); ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="mt-4">Expenses Breakdown by Category</h3>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>#</th> <!-- Added for auto-increment number -->
                    <th>Category</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($categoryBreakdownResult) > 0): ?>
                    <?php 
                    $counter = 1; // Initialize counter for auto-increment number
                    while ($category = mysqli_fetch_assoc($categoryBreakdownResult)): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td> <!-- Display counter and increment -->
                            <td><?php echo htmlspecialchars($category['category']); ?></td>
                            <td>₹<?php echo number_format($category['total_amount'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No expenses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a><br><br>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
