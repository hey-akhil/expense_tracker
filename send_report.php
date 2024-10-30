<?php
require 'vendor/autoload.php'; // Include dompdf

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// Include database connection
include 'db.php';

session_start();

// Get email from command line argument
$email = $argv[1];

// Get the last month's data for report
$last_month = date('Y-m', strtotime('last month'));
$query = "SELECT * FROM expenses WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $last_month, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any records
if ($result->num_rows === 0) {
    echo "No records for this month, no email sent.\n";
    exit; // Exit if no records found
}

// Prepare data for the PDF
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

// Generate PDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$html = '<h1>Monthly Report for ' . date('F Y', strtotime($last_month)) . '</h1>';
$html .= '<table border="1" cellpadding="5" cellspacing="0"><tr><th>Type</th><th>Category</th><th>Amount</th><th>Date</th></tr>';

foreach ($transactions as $transaction) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($transaction['type']) . '</td>';
    $html .= '<td>' . htmlspecialchars($transaction['category']) . '</td>';
    $html .= '<td>₹' . number_format($transaction['amount'], 2) . '</td>';
    $html .= '<td>' . date('d-m-Y', strtotime($transaction['created_at'])) . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

// Load HTML into Dompdf
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Save PDF to a file
$pdf_output = $dompdf->output();
$pdf_file = 'monthly_report_' . date('Y_m') . '.pdf';
file_put_contents($pdf_file, $pdf_output);

// Send email with PDF attachment using PHPMailer
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '1rivvet@gmail.com'; // Your email address
    $mail->Password = 'mrvy nmde ghnn jnev'; // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('1rivvet@gmail.com', 'Expense Trackers');
    $mail->addAddress($email); // Recipient email

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Monthly Report';
    $mail->Body = 'Please find attached your monthly report.';

    // Attach PDF file
    $mail->addAttachment($pdf_file);

    // Send the email
    $mail->send();
    echo "Monthly report sent to: $email\n";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}

// Clean up
unlink($pdf_file); // Delete the generated PDF after sending
