<?php
include 'header.php'; 
include 'db.php'; // Include your database connection file

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT first_name, last_name, email, role FROM users WHERE id = ?");
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();

// Check if the user was found
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    
    // Email should not be updated
    // $email = $_POST['email']; // REMOVE this line

    // Check if password fields are filled for updating the password
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password === $confirm_password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $first_name, $last_name, $hashed_password, $user_id);
        } else {
            echo "<script>alert('Passwords do not match!');</script>";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $first_name, $last_name, $user_id);
    }

    if ($stmt->execute()) {
        echo "
        <script>
            alert('Profile updated successfully!');
            setTimeout(function() {
                window.location.href = '" . ($user['role'] == 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php') . "';
            }, 1000);
        </script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }
}

// Handle account deletion after user confirms
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmed_delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        session_destroy(); // Destroy the session after account deletion
        echo "
        <script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Success',
                    text: 'Account deleted successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';
                    }
                });
            }, 1000);
        </script>";
    } else {
        echo "<script>alert('Error deleting account!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <!-- Update Profile Form -->
        <form action="edit_profile.php" method="POST" id="profileForm">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="password">New Password (leave blank if you don't want to change)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                <small id="passwordMessage" class="form-text text-danger" style="display: none;"></small>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" name="update_profile" class="btn btn-primary" id="updateButton" disabled>Update Profile</button>
                <button type="button" class="btn btn-danger" style="margin-right: auto; margin-left: 5px;" onclick="confirmDelete()">Delete Account</button>
            </div>
        </form>

        <!-- Form for confirmed delete (submitted after confirmation) -->
        <form id="deleteForm" action="edit_profile.php" method="POST" style="display:none;">
            <input type="hidden" name="confirmed_delete" value="1">
        </form>
    </div>

    <!-- Include JS libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>

    <script>
        // Function to show confirmation popup
        function confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover your account!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        }

        // Enable the update button only when changes are made
        const originalFirstName = document.getElementById('first_name').value;
        const originalLastName = document.getElementById('last_name').value;
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm_password');
        const updateButton = document.getElementById('updateButton');
        const passwordMessage = document.getElementById('passwordMessage');

        function checkForChanges() {
            const newFirstName = document.getElementById('first_name').value;
            const newLastName = document.getElementById('last_name').value;
            const newPassword = passwordField.value;
            const newConfirmPassword = confirmPasswordField.value;

            // Check if password fields match
            if (newPassword && newPassword !== newConfirmPassword) {
                passwordMessage.textContent = "Passwords do not match!";
                passwordMessage.style.display = "block";
                updateButton.disabled = true;
            } else {
                passwordMessage.textContent = "";
                passwordMessage.style.display = "none";
                updateButton.disabled = !(newFirstName !== originalFirstName || newLastName !== originalLastName || (newPassword && newPassword === newConfirmPassword));
            }
        }

        // Add event listeners to input fields
        document.getElementById('first_name').addEventListener('input', checkForChanges);
        document.getElementById('last_name').addEventListener('input', checkForChanges);
        passwordField.addEventListener('input', checkForChanges);
        confirmPasswordField.addEventListener('input', checkForChanges);
    </script>
</body>
</html>
