<?php
include 'header.php'; // Include header with session management
include 'db.php'; // Include database connection

// Fetch expenses and incomes for the current month
$user_id = $_SESSION['user_id'];
$current_month = date('m'); // Get current month
$current_year = date('Y'); // Get current year

$query = "SELECT * FROM expenses WHERE user_id = ? AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY created_at DESC";
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

// Calculate total sums
$total_expense = array_sum(array_column($expenses, 'amount'));
$total_income = array_sum(array_column($incomes, 'amount'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses & Incomes - Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .icon {
            cursor: pointer;
            margin-right: 10px;
            transition: color 0.3s ease;
        }
        .icon:hover {
            color: #007bff;
        }
        table {
            width: 100%;
            table-layout: auto;
        }
        @media (max-width: 768px) {
            th, td {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4" style="margin-top: -50px;">View Expenses & Incomes</h2>
        <div class="text-center mb-4">
            <button class="btn btn-secondary" id="show-expenses">Show Expenses</button>
            <button class="btn btn-secondary" id="show-incomes">Show Incomes</button>
            <button class="btn btn-secondary" id="show-all">Show All Records</button>
        </div>

        <h3 id="view-title">Expenses</h3>

        <div class="filter-container mb-4" style="display: none;">
            <input type="text" id="filter-input" placeholder="Search Categories" onkeyup="filterRecords()">
        </div>

        <table class="table table-bordered" id="expenses-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expenses as $expense): ?>
                <tr class="record-row" data-id="<?php echo $expense['id']; ?>">
                    <td><?php echo htmlspecialchars($expense['category']); ?></td>
                    <td><?php echo htmlspecialchars($expense['amount']); ?></td>
                    <td><?php echo date('d-m-Y | g:i A', strtotime($expense['created_at'])); ?></td>
                    <td>
                        <span class="icon" onclick="openEditModal(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['category']); ?>', <?php echo htmlspecialchars($expense['amount']); ?>, '<?php echo htmlspecialchars($expense['created_at']); ?>')"><i class="fas fa-edit" style="line-height: 2;"></i></span>
                        <span class="icon" onclick="deleteExpense(<?php echo $expense['id']; ?>)"><i class="fas fa-trash-alt"></i></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 id="total-expense">Total Expense: <?php echo $total_expense; ?></h4>

        <table class="table table-bordered" id="incomes-table" style="display: none;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incomes as $income): ?>
                <tr class="record-row" data-id="<?php echo $income['id']; ?>">
                    <td><?php echo htmlspecialchars($income['category']); ?></td>
                    <td><?php echo htmlspecialchars($income['amount']); ?></td>
                    <td><?php echo date('d-m-Y | g:i A', strtotime($income['created_at'])); ?></td>
                    <td style="line-height: 1.5;">
                        <span class="icon" onclick="openEditModal(<?php echo $income['id']; ?>, '<?php echo htmlspecialchars($income['category']); ?>', <?php echo htmlspecialchars($income['amount']); ?>, '<?php echo htmlspecialchars($income['created_at']); ?>')"><i class="fas fa-edit" style="line-height: 1.5;"></i></span>
                        <span class="icon" onclick="deleteIncome(<?php echo $income['id']; ?>)"><i class="fas fa-trash-alt"></i></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 id="total-income" style="display: none;">Total Income: <?php echo $total_income; ?></h4>

        <table class="table table-bordered" id="all-records-table" style="display: none;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_merge($expenses, $incomes) as $record): ?>
                <tr class="record-row" data-id="<?php echo $record['id']; ?>">
                    <td><?php echo htmlspecialchars($record['category']); ?></td>
                    <td><?php echo htmlspecialchars($record['type']); ?></td>
                    <td><?php echo htmlspecialchars($record['amount']); ?></td>
                    <td><?php echo date('d-m-Y | g:i A', strtotime($record['created_at'])); ?></td>
                    <td>
                        <span class="icon" onclick="openEditModal(<?php echo $record['id']; ?>, '<?php echo htmlspecialchars($record['category']); ?>', <?php echo htmlspecialchars($record['amount']); ?>, '<?php echo htmlspecialchars($record['created_at']); ?>')"><i class="fas fa-edit"></i></span>
                        <span class="icon" onclick="<?php echo $record['type'] == 'Expense' ? 'deleteExpense(' . $record['id'] . ')' : 'deleteIncome(' . $record['id'] . ')'; ?>"><i class="fas fa-trash-alt"></i></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal for editing -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="record-id">
                        <div class="form-group">
                            <label for="record-category">Category</label>
                            <input type="text" class="form-control" id="record-category">
                        </div>
                        <div class="form-group">
                            <label for="record-amount">Amount</label>
                            <input type="number" class="form-control" id="record-amount">
                        </div>
                        <div class="form-group">
                            <label for="record-date">Date</label>
                            <input type="date" class="form-control" id="record-date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save-changes">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function filterRecords() {
                const input = document.getElementById('filter-input').value.toLowerCase();
                const rows = document.querySelectorAll('.record-row');
                rows.forEach(row => {
                    const category = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const amount = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    if (category.includes(input) || amount.includes(input)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            document.getElementById('show-all').onclick = function() {
                document.getElementById('incomes-table').style.display = 'none';
                document.getElementById('expenses-table').style.display = 'none';
                document.getElementById('all-records-table').style.display = 'table';
                document.getElementById('total-income').style.display = 'none';
                document.getElementById('total-expense').style.display = 'none';
                document.querySelector('.filter-container').style.display = 'block';
                document.getElementById('view-title').innerText = 'All Records';
            };

            document.getElementById('show-expenses').onclick = function() {
                document.getElementById('incomes-table').style.display = 'none';
                document.getElementById('expenses-table').style.display = 'table';
                document.getElementById('all-records-table').style.display = 'none';
                document.getElementById('total-income').style.display = 'none';
                document.getElementById('total-expense').style.display = 'block';
                document.querySelector('.filter-container').style.display = 'none';
                document.getElementById('view-title').innerText = 'Expenses';
            };

            document.getElementById('show-incomes').onclick = function() {
                document.getElementById('incomes-table').style.display = 'table';
                document.getElementById('expenses-table').style.display = 'none';
                document.getElementById('all-records-table').style.display = 'none';
                document.getElementById('total-income').style.display = 'block';
                document.getElementById('total-expense').style.display = 'none';
                document.querySelector('.filter-container').style.display = 'none';
                document.getElementById('view-title').innerText = 'Incomes';
            };

            function openEditModal(id, category, amount, date) {
                document.getElementById('record-id').value = id;
                document.getElementById('record-category').value = category;
                document.getElementById('record-amount').value = amount;
                document.getElementById('record-date').value = date.split(' ')[0]; // Get date only
                $('#editModal').modal('show');
            }

            document.getElementById('save-changes').onclick = function() {
                const id = document.getElementById('record-id').value;
                const category = document.getElementById('record-category').value;
                const amount = document.getElementById('record-amount').value;
                const date = document.getElementById('record-date').value;

                // Perform AJAX request to save changes
                $.ajax({
                    url: 'save_record.php',
                    type: 'POST',
                    data: { id: id, category: category, amount: amount, date: date },
                    success: function(response) {
                        location.reload(); // Reload the page after saving changes
                    },
                    error: function(xhr, status, error) {
                        alert('Error saving changes: ' + error);
                    }
                });
            };

            function deleteExpense(id) {
                if (confirm('Are you sure you want to delete this expense?')) {
                    $.ajax({
                        url: 'delete_expense.php',
                        type: 'POST',
                        data: { id: id, type: 'expense' },
                        success: function(response) {
                            location.reload(); // Reload the page after deletion
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting record: ' + error);
                        }
                    });
                }
            }

            function deleteIncome(id) {
                if (confirm('Are you sure you want to delete this income?')) {
                    $.ajax({
                        url: 'delete_income.php',
                        type: 'POST',
                        data: { id: id, type: 'income' },
                        success: function(response) {
                            location.reload(); // Reload the page after deletion
                        },
                        error: function(xhr, status, error) {
                            alert('Error deleting record: ' + error);
                        }
                    });
                }
            }
        </script>
    </div>
</section>

</body>
</html>
