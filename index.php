<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* Ensure all cards have the same height */
        .card {
            min-height: 200px; /* Adjust this value as needed */
        }

        .features-section .row > .col-md-4 {
            display: flex;
            justify-content: center;
        }

        .card {
            width: 100%; /* Ensure card takes full column width */
        }

        /* Align card content */
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        .card-body i {
            font-size: 3rem;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 stylish-heading">Track Your Expenses Easily</h1>
        <p class="lead">Manage your finances with ease. Register or log in to start tracking your daily expenses effortlessly.</p><br>
        <a href="register.php" class="btn btn-primary btn-lg mr-2">Register Now !</a>
        <!-- <a href="login.php" class="btn btn-outline-light btn-lg">Login</a> -->

    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 text-center">
    <div class="container">
        <h2 style="margin-top: -40px;">Why Choose Our Expense Tracker?</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-fill fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Easy Expense Tracking</h5>
                        <p class="card-text">Record your daily expenses and categorize them effortlessly. Visualize your spending trends.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-shield-fill fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Secure and Private</h5>
                        <p class="card-text">We value your privacy. Your data is securely stored and only accessible by you.<br><br></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-wallet2 fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Budget Friendly</h5>
                        <p class="card-text">Our app helps you stay within your budget by giving you clear insights into your spending.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
