<?php
// Include the database connection and header with session_start()
include 'db.php';
include 'header.php'; // Make sure header.php contains session_start()

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Prepare SQL statement to fetch user data, including the approve status
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND approve = 1");
        $stmt->bind_param("s", $email); // 's' indicates the type is string
        $stmt->execute();
        
        // Fetch user record
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify the password and approve status
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];
            
            // Check user role and redirect accordingly
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            } else {
                header("Location: user_dashboard.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            // Set session variable for error message
            $_SESSION['login_error'] = "Invalid email or password!";
            header("Location: login.php"); // Redirect to login page
            exit();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(); // Catch any exceptions
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

</head>
<body>

<!-- Login Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4" style="margin-top: -70px;">Login to Your Account</h2>
                
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <a href="forgot_password.php" class="d-block mb-2">Forgot Password</a>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                    <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        <?php if (isset($_SESSION['login_error'])): ?>
            alert("<?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>"); // Show alert
            // Optionally redirect after alert
            setTimeout(function() {
                window.location.href = 'login.php'; // Redirect to login page
            }); //},3 Change this to 3000 milliseconds (3 seconds)
        <?php endif; ?>
    });
</script>

</body>
</html>
