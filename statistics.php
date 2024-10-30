<?php
include 'header.php';
include 'db.php'; // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch expenses and incomes from the database
$query = "SELECT * FROM expenses WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$expenses = [];
$incomes = [];

// Separate expenses and incomes
while ($row = $result->fetch_assoc()) {
    if ($row['type'] == 'Expense') {
        $expenses[] = $row;
    } else {
        $incomes[] = $row;
    }
}

// Calculate total sums and sources
$total_expense = array_sum(array_column($expenses, 'amount'));
$total_income = array_sum(array_column($incomes, 'amount'));

// Calculate income sources
$income_sources = [];
foreach ($incomes as $income) {
    if (isset($income_sources[$income['category']])) {
        $income_sources[$income['category']] += $income['amount'];
    } else {
        $income_sources[$income['category']] = $income['amount'];
    }
}

// Calculate expense categories
$expense_sources = [];
foreach ($expenses as $expense) {
    if (isset($expense_sources[$expense['category']])) {
        $expense_sources[$expense['category']] += $expense['amount'];
    } else {
        $expense_sources[$expense['category']] = $expense['amount'];
    }
}

// Prepare data for charts
$income_labels = array_keys($income_sources);
$income_data = array_values($income_sources);
$expense_labels = array_keys($expense_sources);
$expense_data = array_values($expense_sources);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Optional: Reduce the size of the charts */
        canvas {
            max-width: 400px; /* Set maximum width for charts */
            max-height: 400px; /* Set maximum height for charts */
        }
        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center align charts */
        }
        .source-list {
            padding-left: 20px; /* Add padding for better readability */
        }
    </style>
</head>
<body>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Income & Expense Statistics</h2>
        
        <div class="row">
            <div class="col-md-6 chart-container">
                <h4>Total Income: <?php echo $total_income; ?></h4>
                <canvas id="incomeChart" width="300" height="300"></canvas>
            </div>
            <div class="col-md-6 chart-container">
                <h4>Total Expense: <?php echo $total_expense; ?></h4>
                <canvas id="expenseChart" width="300" height="300"></canvas>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <h4>Income Sources</h4>
                <ul class="source-list">
                    <?php foreach ($income_sources as $source => $amount): ?>
                        <li><?php echo $source; ?>: <?php echo $amount; ?> (<?php echo round(($amount / $total_income) * 100, 2); ?>%)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h4>Expense Sources</h4>
                <ul class="source-list">
                    <?php foreach ($expense_sources as $source => $amount): ?>
                        <li><?php echo $source; ?>: <?php echo $amount; ?> (<?php echo round(($amount / $total_expense) * 100, 2); ?>%)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<script>
    // Income Chart
    var ctxIncome = document.getElementById('incomeChart').getContext('2d');
    var incomeChart = new Chart(ctxIncome, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($income_labels); ?>,
            datasets: [{
                label: 'Income',
                data: <?php echo json_encode($income_data); ?>,
                backgroundColor: ['#36a2eb', '#ffcc00', '#ff6384', '#4bc0c0', '#9966ff'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw + ' (' + (context.raw / <?php echo $total_income; ?> * 100).toFixed(2) + '%)';
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Expense Chart
    var ctxExpense = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctxExpense, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($expense_labels); ?>,
            datasets: [{
                label: 'Expense',
                data: <?php echo json_encode($expense_data); ?>,
                backgroundColor: ['#ff6384', '#36a2eb', '#ffcc00', '#4bc0c0', '#9966ff'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.raw + ' (' + (context.raw / <?php echo $total_expense; ?> * 100).toFixed(2) + '%)';
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
