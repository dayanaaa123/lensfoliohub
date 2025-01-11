<?php
include '../../../db/db.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '../../../PHPMailer/src/Exception.php';
require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';

// Suppress warnings and notices
error_reporting(E_ERROR | E_PARSE);

header('Content-Type: application/json'); // Always return JSON response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit();
    }

    // Generate a unique verification code
    $verification_code = md5(time() . $email);

    // Save the code to the database
    $sql = "UPDATE users SET verification_code = '$verification_code' WHERE email = '$email'";
    if ($conn->query($sql) && $conn->affected_rows > 0) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ejivancablanida@gmail.com';
            $mail->Password = 'acjf ngko qlfb cuju';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('lensfoliohub@gmail.com', 'LENSFOLIOHUB');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Your verification code is: <strong>$verification_code</strong>";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Verification email sent successfully.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found or unable to update verification code.']);
    }

    $conn->close();
}
?>
