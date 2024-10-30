<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3f4f7;
        }
        .contact-header {
            background-image: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        .contact-header h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .contact-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
        .btn-custom {
            background-color: #6a11cb !important;
            color: white !important;
            border: none;
        }
        .btn-custom:hover {
            background-color: #2575fc !important;
        }
        .social-icons a {
            margin: 0 10px;
            font-size: 1.5rem;
            color: #6a11cb;
            transition: color 0.3s;
        }
        .social-icons a:hover {
            color: #2575fc;
        }
        /* .cd{
            height: Auto !important;
        } */
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="contact-header">
        <h1>Get in Touch with Us</h1>
        <p>We'd love to hear from you! Please feel free to reach out via the form below or visit us at our location.</p>
    </div>

    <div class="container my-5">
        <div class="row">
            <!-- Contact Form -->
            <div class="cd col-md-6">
                <div class="card contact-card p-4">
                    <h3 class="mb-4">Contact Us</h3>
                    <form action="send_message.php" method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom btn-block mt-3">Send Message</button>
                    </form>
                </div>
            </div>

            <!-- Address & Social Section -->
            <div class="cd col-md-6">
                <div class="card contact-card p-4">
                    <h3 class="mb-4">Our Address</h3>
                    <p><strong>Office Address:</strong></p>
                    <p>1234 Business Avenue, <br> City Name, State, ZIP Code</p>

                    <h4 class="mt-4">Phone</h4>
                    <p>+91-1234567890</p>

                    <h4 class="mt-4">Email</h4>
                    <p>support@yourwebsite.com</p>

                    <h4 class="mt-4">Follow Us</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Google Maps Section -->
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-10 col-md-8 col-lg-10">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093705!2d144.95373521531774!3d-37.816279742013116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf5770f0f9a9d4f!2sYour+Business+Location!5e0!3m2!1sen!2sin!4v1614764485732!5m2!1sen!2sin" 
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</div><br><br>


    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include '../footer.php'; ?>