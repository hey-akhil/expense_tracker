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

// Initialize variables to hold form data and errors
$first_name = '';
$last_name = '';
$email = '';
$password = ''; // New password variable
$role = 'user'; // Default role
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password'])); // Sanitize password
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));

    // Validate form data
    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }
    if (empty($last_name)) {
        $errors[] = 'Last name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if (empty($role)) {
        $errors[] = 'Role is required.';
    }

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = 'Email already exists. Please choose another.';
    }

    // If there are no errors, insert the new user
    if (empty($errors)) {
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO users (first_name, last_name, email, password, role, approve) VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$role', 1)";
        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('User added successfully!'); window.location.href='admin_manage_users.php';</script>";
            exit();
        } else {
            $errors[] = 'Error adding user: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4" style="text-align: center;">Add New User</h1>
        <!-- <h2>Welcome, ?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2> -->

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="mt-4" style="width: 50%; margin-inline: auto;">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role (select role from dropdown)</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="user" <?php echo ($role === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="admin_manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
