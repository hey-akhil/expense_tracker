<?php 
include 'header.php';
include 'db.php'; // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the current month and year
$current_month = date('m');
$current_year = date('Y');

// Fetch the expenses and incomes for the current month
$query = "SELECT * FROM expenses WHERE user_id = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $current_month, $current_year);
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

// Calculate total sums for the current month
$total_expense = array_sum(array_column($expenses, 'amount'));
$total_income = array_sum(array_column($incomes, 'amount'));

// Prepare data for income and expense pie chart
$income_sources = [];
foreach ($incomes as $income) {
    $income_sources[$income['category']] = ($income_sources[$income['category']] ?? 0) + $income['amount'];
}

$expense_sources = [];
foreach ($expenses as $expense) {
    $expense_sources[$expense['category']] = ($expense_sources[$expense['category']] ?? 0) + $expense['amount'];
}

// Calculate percentages for income and expense categories
$income_percentages = [];
foreach ($income_sources as $category => $amount) {
    $income_percentages[$category] = ($amount / $total_income) * 100; // Percentage of total income
}

$expense_percentages = [];
foreach ($expense_sources as $category => $amount) {
    $expense_percentages[$category] = ($amount / $total_expense) * 100; // Percentage of total expenses
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
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Responsive design adjustments */
        .chart-container {
            display: flex;
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
        }

        canvas {
            padding: 0px; /* Adjust padding */
            width: 45% !important; /* Set width for the chart */
            height: auto !important; /* Maintain aspect ratio */
        }

        .legend {
            display: flex;
            flex-direction: column;
            margin-left: 8px; /* Space between chart and legend */
        }

        .card {
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .flipboard {
            margin-top: 20px;
        }

        .flipboard-item {
            flex: 1; /* Make items flexible */
            min-width: 200px; /* Minimum width */
        }

        .card.flipboard-item {
            flex: 1; /* Make items flexible */
            min-width: 200px; /* Minimum width for responsive design */
            margin: 25px; 
        }

        .last-transactions-card {
            min-height: 400px; /* Set a minimum height */
        }

        @media (max-width: 768px) {
            .userdashboard {
                flex-direction: column; /* Stack vertically */
            }

            .actionbtndiv {
                margin-bottom: 50px; /* Adjust space */
            }
            .flipboard {
                justify-content: center; /* Center flipboard on small screens */
            }
        }
        
        .expense-row {
            background-color: rgba(255, 0, 0, 0.2); /* Light red for expenses */
        }

        .income-row {
            background-color: rgba(0, 255, 0, 0.2); /* Light green for incomes */
        }

        .table th, .table td {
            vertical-align: middle; /* Center align cell content */
        }

        .table th {
            background-color: #f8f9fa; /* Optional: Header background color */
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="userdashboard d-flex flex-wrap justify-content-between align-items-start">
        <div class="mb-3">
            <h1>Dashboard</h1>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
        </div>

        <div class="flipboard d-flex justify-content-between flex-wrap mt-3">
            <div class="card flipboard-item" style="margin-top: -19px;">
                <h3>Total Income</h3>
                <h2>₹<?php echo number_format($total_income, 2); ?></h2>
            </div>
            <div class="card flipboard-item" style="margin-top: -19px;">
                <h3>Total Expenses</h3>
                <h2>₹<?php echo number_format($total_expense, 2); ?></h2>
            </div>
        </div>
    </div>

    <div class="actionbtndiv" style="margin-bottom: 150px;">
        <a href="add_expense.php" class="btn btn-primary">Add Income/Expense</a>
        <a href="view_expenses.php" class="btn btn-secondary">View All Records</a>
        <a href="history.php" class="btn btn-info">History</a>
    </div>
   
    <!-- Action Messages Section -->
    <div class="actiondiv container">
        <div class="row" style="margin-top: -125px;">
            <div class="col-12">
                <?php if ($total_expense > $total_income): ?>
                    <div class="alert alert-danger text-center">
                        <strong>Warning!</strong> Expenses exceed income! Remaining Amount: <strong>₹<?php echo number_format($total_expense - $total_income, 2); ?></strong>
                    </div>
                <?php elseif ($total_income > $total_expense): ?>
                    <div class="alert alert-success text-center">
                        <strong>Great job!</strong> You have surplus! Remaining Amount: <strong>[+ ₹<?php echo number_format($total_income - $total_expense, 2); ?>]</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Income and Expense Breakdown -->
<?php if (empty($incomes) && empty($expenses)): ?>
    <div class="alert alert-info text-center" style="margin-top: 13rem;">
        No transactions found! Start adding your income and expenses to view charts.
    </div>
<?php else: ?>
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <h3 class="text-center">Income and Expense Breakdown</h3>
                <div class="row">
                    <!-- Income Breakdown -->
                    <div class="col-md-6">
                        <h4 class="text-center">Income Breakdown</h4>
                        <div class="chart-container">
                            <canvas id="incomePieChart"></canvas>
                            <div class="legend" id="incomeLegend"></div>
                        </div>
                    </div>

                    <!-- Expense Breakdown -->
                    <div class="col-md-6">
                        <h4 class="text-center">Expense Breakdown</h4>
                        <div class="chart-container">
                            <canvas id="expensePieChart"></canvas>
                            <div class="legend" id="expenseLegend"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    <!-- Last 5 Transactions Section -->
    <div class="card mt-4 last-transactions-card">
        <h3>Recent Transactions</h3>
        <div class="table-responsive"> <!-- Added responsive wrapper -->
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $indexnumber = 1; // Initialize index number outside the loop
                    
                    // Combine both incomes and expenses while preserving the order
                    $lastTransactions = array_merge($expenses, $incomes);

                    // Sort to keep the order of last 5 transactions
                    usort($lastTransactions, function($a, $b) {
                        return strtotime($b['created_at']) - strtotime($a['created_at']);
                    });

                    // Limit to last 5 transactions
                    foreach (array_slice($lastTransactions, 0, 5) as $transaction): ?>
                        <tr class="<?php echo ($transaction['type'] == 'Expense') ? 'expense-row' : 'income-row'; ?>">
                            <td><?php echo $indexnumber++; ?></td>
                            <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['category']); ?></td>
                            <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($transaction['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Income Pie Chart
    const ctxIncome = document.getElementById('incomePieChart').getContext('2d');
    const incomePieChart = new Chart(ctxIncome, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($income_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($income_data); ?>,
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false // Hide the default legend
                }
            }
        }
    });

    // Generate custom income legend
    const incomeLegend = document.getElementById('incomeLegend');
    const incomeSources = <?php echo json_encode($income_sources); ?>;
    const incomePercentages = <?php echo json_encode($income_percentages); ?>;
    const incomeLabels = <?php echo json_encode($income_labels); ?>;

    incomeLabels.forEach((label, index) => {
        const totalAmount = incomeSources[label]; // Total amount for the category
        const percentage = incomePercentages[label].toFixed(2); // Format percentage
        const listItem = document.createElement('div'); // Use div for more styling options
        listItem.style.display = 'flex'; // Flex container
        listItem.style.alignItems = 'center'; // Center vertically

        // Create colored square
        const colorBox = document.createElement('span');
        colorBox.style.display = 'inline-block';
        colorBox.style.width = '15px'; // Set width
        colorBox.style.height = '15px'; // Set height
        colorBox.style.backgroundColor = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'][index]; // Match color with the chart
        colorBox.style.marginRight = '8px'; // Space between color box and text

        // Create text node
        const textNode = document.createTextNode(`${label}: ₹${totalAmount.toFixed(2)} (${percentage}%)`);

        // Append color box and text to list item
        listItem.appendChild(colorBox);
        listItem.appendChild(textNode);
        incomeLegend.appendChild(listItem);
    });

    // Expense Pie Chart
    const ctxExpense = document.getElementById('expensePieChart').getContext('2d');
    const expensePieChart = new Chart(ctxExpense, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($expense_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($expense_data); ?>,
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false // Hide the default legend
                }
            }
        }
    });

    // Generate custom expense legend
    const expenseLegend = document.getElementById('expenseLegend');
    const expenseSources = <?php echo json_encode($expense_sources); ?>;
    const expensePercentages = <?php echo json_encode($expense_percentages); ?>;
    const expenseLabels = <?php echo json_encode($expense_labels); ?>;

    expenseLabels.forEach((label, index) => {
        const totalAmount = expenseSources[label]; // Total amount for the category
        const percentage = expensePercentages[label].toFixed(2); // Format percentage
        const listItem = document.createElement('div'); // Use div for more styling options
        listItem.style.display = 'flex'; // Flex container
        listItem.style.alignItems = 'center'; // Center vertically

        // Create colored square
        const colorBox = document.createElement('span');
        colorBox.style.display = 'inline-block';
        colorBox.style.width = '15px'; // Set width
        colorBox.style.height = '15px'; // Set height
        colorBox.style.backgroundColor = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'][index]; // Match color with the chart
        colorBox.style.marginRight = '8px'; // Space between color box and text

        // Create text node
        const textNode = document.createTextNode(`${label}: ₹${totalAmount.toFixed(2)} (${percentage}%)`);

        // Append color box and text to list item
        listItem.appendChild(colorBox);
        listItem.appendChild(textNode);
        expenseLegend.appendChild(listItem);
    });
</script>

</body>
</html>
