<?php
session_start(); // Start the session
include 'db.php'; // Include database connection

// List of accessible pages for guests
$accessiblePages = ['index.php', 'login.php', 'register.php', 'forgot_password.php'];

// Get the current filename
$currentFile = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Expense Tracker</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Use Roboto font */
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <h3 class="navbar-brand">
            𝐄𝐗𝐏𝐄𝐍𝐒𝐄 𝐓𝐑𝐀𝐂𝐊𝐄𝐑
        </h3>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link"><i class="fas fa-home" style="margin-top: 3px;"></i></a>
                    </li>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_manage_users.php">Manage Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_track_expenses.php">View Expenses</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_statistics.php">Statistics</a></li>
                        <li class="nav-item"><a class="nav-link" href="edit_profile.php">Edit Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="user_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="add_expense.php">Add Expense/Income</a></li>
                        <li class="nav-item"><a class="nav-link" href="view_expenses.php">View Records</a></li>
                        <li class="nav-item"><a class="nav-link" href="history.php">Monthly History</a></li>
                        <li class="nav-item"><a class="nav-link" href="edit_profile.php">Edit Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php endif; ?>
                
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="et/Contact-us.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Close the navbar when a link is clicked
    $(document).ready(function() {
        $('.navbar-nav a').on('click', function() {
            $('.navbar-collapse').collapse('hide'); // Close the navbar
        });
    });
</script>

</body>
</html>
