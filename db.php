<?php
$servername = "localhost";   // Change if your DB server is hosted remotely
$username = "root";          // Database username
$password = "";              // Database password
$dbname = "expense_tracker_db";  // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF8 for proper encoding support
$conn->set_charset("utf8");

?>
