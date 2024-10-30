<?php
include 'header.php';
include 'db.php'; // Ensure $conn is initialized here

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Registration Form Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if email is already registered
    $check_email_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_stmt->store_result();

    if ($check_email_stmt->num_rows > 0) {
        echo "<script>alert('Your email is already registered.'); 
              window.location.href = 'login.php';</script>";
        $check_email_stmt->close();
        exit();
    }

    $check_email_stmt->close();

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    // Hash password and generate OTP
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $otp = rand(100000, 999999);
    $otp_created_at = date('Y-m-d H:i:s');

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, otp, otp_created_at, approve, created_at) 
                            VALUES (?, ?, ?, ?, 'user', ?, ?, 0, NOW())");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $hashedPassword, $otp, $otp_created_at);

    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        $_SESSION['otp'] = $otp;

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '1rivvet@gmail.com'; // Replace with your email
            $mail->Password = 'mrvy nmde ghnn jnev'; // Use Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('1rivvet@gmail.com', 'Expense Tracker');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code - Expense Tracker Verification';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
                    <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);'>
                        <h2 style='color: #4CAF50; text-align: center;'>Expense Tracker - OTP Verification</h2>
                        <p>Dear <strong>$first_name $last_name</strong>,</p>
                        <p>Use the following OTP to verify your email:</p>
                        <div style='text-align: center;'>
                            <p style='font-size: 24px; font-weight: bold; background-color: #f0f0f0; padding: 10px; border-radius: 5px; display: inline-block;'>
                                $otp
                            </p>
                        </div>
                        <p>This OTP is valid for 10 minutes. If you didn’t request this, ignore this email.</p>
                    </div>
                </div>";
            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            echo "<script>alert('Failed to send OTP. Please try again.'); window.location.href = 'register.php';</script>";
            exit();
        }

        $_SESSION['registration_success'] = true;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// OTP Verification Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email'];

    // Fetch OTP from the database
    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ? AND approve = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_otp);
    $stmt->fetch();
    $stmt->close();

    if ($entered_otp == $db_otp) {
        // Update the approve field to 1
        $update_stmt = $conn->prepare("UPDATE users SET approve = 1 WHERE email = ?");
        $update_stmt->bind_param("s", $email);
        $update_stmt->execute();
        $update_stmt->close();

        echo "<script>alert('OTP verified successfully! Your account is now active.'); 
              window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP! Please try again.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
    .validation-list {
        display: none;
        list-style: none;
        padding: 0;
        margin-top: 5px;
    }

    .validation-list li {
        color: red;
        margin-bottom: 5px;
        transition: color 0.3s;
    }

    .validation-list li.success {
        color: green;
    }
</style>

</head>
<body>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Create an Account</h2>
                <form action="" method="POST">
                    <?php if (!isset($_SESSION['registration_success'])): ?>
                        <!-- Registration Form -->
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required oninput="validatePassword()">
                            <ul class="validation-list" id="passwordCriteria">
                                <li id="length" class="error">At least 6 characters</li>
                                <li id="uppercase" class="error">One uppercase letter and one number</li>
                                <li id="special" class="error">One special character (@$!%*?&)</li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                    <?php else: ?>
                        <!-- OTP Verification Form -->
                        <div class="alert alert-success">OTP has been sent to your email.</div>
                        <div class="form-group">
                            <label for="otp">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required>
                        </div>
                        <button type="submit" name="verify_otp" class="btn btn-success btn-block">Verify OTP</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    const passwordField = document.getElementById('password');
const passwordCriteria = document.getElementById('passwordCriteria');

// Show password criteria when the field is focused or being typed in
passwordField.addEventListener('focus', () => {
    passwordCriteria.style.display = 'block';
});

passwordField.addEventListener('input', validatePassword);

function validatePassword() {
    const password = passwordField.value;

    const lengthCriteria = document.getElementById('length');
    const uppercaseCriteria = document.getElementById('uppercase');
    const specialCriteria = document.getElementById('special');

    // Update each criterion based on the current input
    updateCriterion(lengthCriteria, password.length >= 6);
    updateCriterion(uppercaseCriteria, /[A-Z]/.test(password) && /\d/.test(password));
    updateCriterion(specialCriteria, /[@$!%*?&]/.test(password));
}

function updateCriterion(element, isValid) {
    if (isValid) {
        element.classList.add('success');  // Mark as success
    } else {
        element.classList.remove('success');
    }
}
</script>
</body>
</html>
