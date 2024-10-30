<?php 
// session_start(); // Ensure the session is started
include 'db.php'; // Adjust path if necessary
include 'header.php'; 

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Restricted! Only admins can access this page.');</script>";
    header("Location: ../user_dashboard.php"); // Redirect to user dashboard if not admin
    exit(); // Stop further code execution
}

// Fetch users from the database
$query = "SELECT * FROM users"; // Adjust table name as needed
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom styles for responsive table */
        @media (max-width: 575.98px) { /* For iPhone SE and similar screens */
            .table-responsive td,
            .table-responsive th {
                white-space: nowrap; /* Prevent text from wrapping */
                overflow: hidden; /* Hide overflow text */
                text-overflow: ellipsis; /* Show ellipsis for overflow text */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-5">
            <h1>Manage Users</h1>
            <a href="admin_add_user.php" class="btn btn-success">Add New User</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <!-- <th>Last Name</th> -->
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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
