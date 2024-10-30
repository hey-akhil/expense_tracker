<?php 
include 'db.php'; 
include 'header.php'; 

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Main Layout: Full Height and Flexbox */
        html, body {
            height: 100%; /* Ensure body takes full height */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        /* header {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
        } */

        /* Content Section */
        .content {
            flex: 1; /* Take remaining space between header and footer */
            background: linear-gradient(to right, #74ebd5, #acb6e5); /* Gradient */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            margin-top: 20px;
        }

        /* Footer */
        /* footer {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
        } */

        /* Card Styling */
        .card {
            transition: transform 0.2s;
            border: 1px solid #007bff;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0px 4px 12px rgba(0, 123, 255, 0.5);
        }

        @media (max-width: 768px) {
            .card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header
    <header>
        <h1>Expense Tracker Admin Panel</h1>
    </header> -->

    <!-- Content Section -->
    <div class="content">
        <div class="container">
            <h2 class="mt-3">Welcome, 
                <?php echo isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : 'Admin'; ?>!
            </h2>

            <div class="row mt-4">
                <!-- User Management Card -->
                <div class="col-md-4 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users"></i> User Management</h5>
                            <p class="card-text">
                                Monitor, add, update, or remove users from the system. Stay in control of who can access and use the platform.
                            </p>
                            <a href="admin_manage_users.php" class="btn btn-primary">Manage Users</a>
                        </div>
                    </div>
                </div>

                <!-- View Expenses Card -->
                <div class="col-md-4 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> View Expenses</h5>
                            <p class="card-text">
                                Review all submitted expenses across users. Ensure compliance and maintain an overview of all financial records.
                            </p>
                            <a href="admin_track_expenses.php" class="btn btn-primary">View All Expenses</a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="col-md-4 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-chart-line"></i> Statistics</h5>
                            <p class="card-text">
                                Get a bird’s-eye view of the financial data with powerful charts. Track trends and make informed decisions.
                            </p>
                            <a href="admin_statistics.php" class="btn btn-primary">View Statistics</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <!-- <footer>
        <p>&copy; 2024 Expense Tracker. All Rights Reserved.</p>
    </footer> -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include 'footer.php';?>