<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
    <style>
        html, body {
            height: 100%; /* Ensure body takes full height */
            margin: 0; /* Remove default margin */
        }

        body {
            display: flex;
            flex-direction: column; /* Stack children vertically */
        }

        /* This class ensures the main content takes all available space */
        .content {
            flex: 1; /* Make this element grow to fill space */
        }

        footer {
            /* Additional styles for footer can be added here */
        }
    </style>
</head>
<body>

    <div class="content">
        <!-- Your content here -->
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?php echo date("Y"); ?> Expense Tracker | All Rights Reserved by ~ 

            <a href="https://www.instagram.com/" target="_blank" class="text-white mx-2">
                <i class="fab fa-instagram"></i> <!-- Instagram icon -->
            </a>
            <a href="https://twitter.com/" target="_blank" class="text-white mx-2">
                <i class="fab fa-twitter"></i> <!-- Twitter icon -->
            </a>
            <a href="https://www.facebook.com/" target="_blank" class="text-white mx-2">
                <i class="fab fa-facebook-f"></i> <!-- Facebook icon -->
            </a>
        </p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
