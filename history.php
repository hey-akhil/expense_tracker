<?php
// Database connection
include 'db.php';
include 'header.php'; // session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Variables
$total_income = 0;
$total_expense = 0;
$transactions = [];
$month_year = $_GET['month'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Handle month-wise or filtered data retrieval
if ($month_year) {
    $query = "SELECT * FROM expenses 
              WHERE user_id = ? 
              AND DATE_FORMAT(created_at, '%Y-%m') = ? 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $month_year);
} elseif ($start_date && $end_date) {
    $query = "SELECT * FROM expenses 
              WHERE user_id = ? 
              AND DATE(created_at) BETWEEN ? AND ? 
              ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
} else {
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_year, 
                     SUM(CASE WHEN type = 'Income' THEN amount ELSE 0 END) AS total_income,
                     SUM(CASE WHEN type = 'Expense' THEN amount ELSE 0 END) AS total_expense 
              FROM expenses 
              WHERE user_id = ? 
              GROUP BY month_year 
              ORDER BY month_year DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($month_year || ($start_date && $end_date)) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
        if ($row['type'] === 'Income') {
            $total_income += $row['amount'];
        } else {
            $total_expense += $row['amount'];
        }
    }
} else {
    $months = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        #filter-form {
            display: none;
            margin-top: 20px;
        }
        @media (max-width: 576px) {
            #filter-form .form-row {
                flex-direction: column; /* Stack form elements vertically on small screens */
            }
            #filter-form .form-row .col-md-4 {
                width: 100%; /* Full width for small screens */
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Transaction History</h1>

    <!-- Show Filter Button -->
    <button id="show-filter-btn" class="btn btn-info" style="margin-top: 12px;">Show Filter</button>

    <!-- Hidden Filter Form -->
    <form id="filter-form" method="GET" action="history.php">
        <div class="form-row">
            <div class="col-md-4 mb-2">
                <label for="text">Select Start date:</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?php echo $start_date; ?>" placeholder="Select Start Date" required>
            </div>
            <div class="col-md-4 mb-2">
            <label for="text">Select End date:</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?php echo $end_date; ?>" required 
                       max="<?php echo date('Y-m-d'); ?>" placeholder="Select End Date">
            </div>
            <div class="col-md-4 mb-2">
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </div>
    </form>

    <hr>

    <?php if ($month_year || ($start_date && $end_date)): ?>
        <h3 class="mt-4">
            <?php
            if ($month_year) {
                echo "Details for " . date('F Y', strtotime($month_year . '-01'));
            } else {
                echo "Details from " . date('d-m-Y', strtotime($start_date)) . " to " . date('d-m-Y', strtotime($end_date));
            }
            ?>
        </h3>
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Total Income: ₹<?php echo number_format($total_income, 2); ?></h4>
            </div>
            <div class="col-md-6">
                <h4>Total Expense: ₹<?php echo number_format($total_expense, 2); ?></h4>
            </div>
        </div>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $index => $transaction): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['category']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                        <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo date('d-m-Y | g:i A', strtotime($transaction['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Download Report Button (Functionality Removed) -->
        <a href="download.php?month=<?php echo $month_year; ?>" class="btn btn-success mt-3">Download Report</a>

        <!-- Back Button -->
        <a href="history.php" class="btn btn-secondary mt-3">Back</a><br><br>

    <?php else: ?>
        <h3 class="mt-4">Month-wise Records</h3>
        <ul class="list-group">
            <?php foreach ($months as $month): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="history.php?month=<?php echo $month['month_year']; ?>">
                        <?php echo date('F Y', strtotime($month['month_year'] . '-01')); ?>
                    </a>
                    <span>
                        Income: ₹<?php echo number_format($month['total_income'], 2); ?> | 
                        Expense: ₹<?php echo number_format($month['total_expense'], 2); ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<script>
    // Ensure the filter form starts hidden
    document.getElementById('filter-form').style.display = 'none';

    document.getElementById('show-filter-btn').addEventListener('click', function () {
        const filterForm = document.getElementById('filter-form');
        // Toggle the visibility of the filter form
        filterForm.style.display = filterForm.style.display === 'none' ? 'block' : 'none';
    });
</script>

</body><br>
</html>
<?php include 'footer.php'; ?>
