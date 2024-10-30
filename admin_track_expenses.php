<?php 
include 'header.php'; 
include 'db.php'; // Adjust path if necessary

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: user_dashboard.php");
    exit(); // Stop further code execution
}

// Fetch all approved users
$usersQuery = "SELECT * FROM users WHERE approve = 1"; // Only approved users
$usersResult = mysqli_query($conn, $usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Users - Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom CSS for table borders */
        .table {
            border: 1px solid #dee2e6; /* Light gray border for table */
        }
        .table th, .table td {
            border: 1px solid #dee2e6; /* Light gray border for table cells */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1 class="mt-5 text-center">All Approved Users</h1>

        <div class="table-responsive">
            <table class="table table-bordered mt-4"> <!-- Added table-bordered class -->
                <thead>
                    <tr>
                        <th>#</th> <!-- New column for auto-increment number -->
                        <th>User Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Joining Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($usersResult) > 0): ?>
                        <?php $counter = 1; // Initialize counter for auto-increment ?>
                        <?php while ($user = mysqli_fetch_assoc($usersResult)): ?>
                            <tr>
                                <td><?php echo $counter++; // Increment the counter for each user ?></td> <!-- Display the counter -->
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('d-m-Y | g:i A', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="admin_user_expenses.php?user_id=<?php echo $user['id']; ?>" class="btn btn-info btn-sm">View Expenses</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="admin_dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a><br><br>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
