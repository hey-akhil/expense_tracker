<?php
include 'header.php';
include 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// Step 1: Handle OTP Generation
if (isset($_POST['send_otp'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP and update it in the database
        $otp = rand(100000, 999999);
        $otp_created_at = date('Y-m-d H:i:s');
        
        $update_stmt = $conn->prepare("UPDATE users SET otp = ?, otp_created_at = ? WHERE email = ?");
        $update_stmt->bind_param('iss', $otp, $otp_created_at, $email);
        $update_stmt->execute();

        // Send OTP email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '1rivvet@gmail.com';
            $mail->Password = 'mrvy nmde ghnn jnev';  // Replace with correct password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('1rivvet@gmail.com', 'Expense Tracker');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Verification Code';
$mail->Body = "
    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
            <h2 style='color: #4CAF50; text-align: center;'>Expense Tracker - OTP Verification</h2>
            <p>Dear User,</p>
            <p>We received a request to reset your password for your Expense Tracker account.</p>
            <p>Please use the following One-Time Password (OTP) to complete your verification:</p>
            <div style='text-align: center;'>
                <p style='font-size: 24px; font-weight: bold; background-color: #f0f0f0; padding: 15px; border-radius: 5px; display: inline-block;'>
                    $otp
                </p>
            </div>
            <p style='font-size: 14px;'>This OTP is valid for the next 10 minutes. If you did not request this change, you can safely ignore this email.</p>
            <p>Thank you for using Expense Tracker!</p>
            <hr>
            <p style='font-size: 12px; color: #777;'>Need help? Contact our support team or visit our <a href='#' style='color: #4CAF50;'>Help Center</a>.</p>
            <p style='font-size: 12px; color: #777;'>This is an automated message. Please do not reply.</p>
        </div>
    </div>
";

            $mail->send();
            $_SESSION['email'] = $email;
            $otp_sent = true;
        } catch (Exception $e) {
            $error_message = "Error sending OTP: " . $mail->ErrorInfo;
        }
    } else {
        $error_message = "Email not found.";
    }
}

// Step 2: Handle OTP Verification
if (isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email'];

    // Check the OTP from the database
    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($stored_otp);
    $stmt->fetch();
    $stmt->close();

    if ($entered_otp == $stored_otp) {
        $otp_verified = true;
    } else {
        $error_message = "Invalid OTP. Please try again.";
    }
}

// Step 3: Handle Password Update
if (isset($_POST['save_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['email'];

    if ($password === $confirm_password) {
        // Hash and update password in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashed_password, $email);
        $stmt->execute();

        $success_message = "Password updated successfully! Redirecting to login...";
        session_destroy();
        header("refresh:0;url=login.php");  // Redirect after 0 seconds
        exit();
    } else {
        $error_message = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/style_forgot.css">
    <style>
        /* Adjusted styles for an expanded box size */
        .container {
            max-width: 800px; /* Increase max width */
            min-width: 320px; /* Ensure box doesn't shrink too much */
            padding: 40px; /* More inner spacing */
            margin: 50px auto; /* Center align with vertical margin */
            border: 1px solid #ddd; /* Add subtle border */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Soft shadow for emphasis */
            background-color: #f8f9fa; /* Light background for a clean look */
            border-radius: 8px; /* Rounded corners */
        }
        .form-control {
            height: 50px; /* Taller input fields */
            font-size: 1.1rem; /* Slightly larger font for better readability */
        }
        .btn {
            height: 50px;
            font-size: 1.2rem; /* Larger button text */
            border-radius: 8px; /* Match button with container radius */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Forgot Password</h2>

    <?php if (isset($success_message)) { echo "<p class='text-success text-center'>$success_message</p>"; } ?>
    <?php if (isset($error_message)) { echo "<p class='text-danger text-center'>$error_message</p>"; } ?>

    <!-- Step 1: Email Input and Send OTP -->
    <?php if (!isset($otp_sent) && !isset($otp_verified)): ?>
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <button type="submit" name="send_otp" class="btn btn-primary btn-block">Send OTP</button>
        </form>
    <?php endif; ?>

    <!-- Step 2: OTP Verification -->
    <?php if (isset($otp_sent) && !isset($otp_verified)): ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
            </div>
            <button type="submit" name="verify_otp" class="btn btn-success btn-block">Verify OTP</button>
        </form>
    <?php endif; ?>

    <!-- Step 3: Password Update -->
    <?php if (isset($otp_verified) && $otp_verified): ?>
        <form method="POST">
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Enter New Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" name="save_password" class="btn btn-primary btn-block">Save Password</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include 'footer.php'; ?>

