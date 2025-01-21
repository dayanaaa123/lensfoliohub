<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../PHPMailer/src/Exception.php';
require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';

include '../../../db/db.php'; // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if verification code was entered (this happens after email is sent)
    if (isset($_POST['verification_code']) && $_POST['verification_code'] != '') {
        if ($_POST['verification_code'] == $_SESSION['verification_code']) {
            // Correct code, update the verify_status to 1
            $email = $_POST['email'];
            $stmt = $conn->prepare("UPDATE users SET verify_status = 2 WHERE email = ?");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $_SESSION['message'] = 'Email verified successfully!';
            } else {
                $_SESSION['message'] = 'Error updating verification status.';
            }
        } else {
            $_SESSION['message'] = 'Incorrect verification code!';
        }
    } else {
        // Initial verification, send email and store verification code in the DB
        $email = $_POST['email'];
        $role = $_POST['role'];
        $name = $_POST['name'];
        $password = $_POST['password'];

        // Generate a 4-digit verification code
        $verification_code = rand(1000, 9999);
        $_SESSION['verification_code'] = $verification_code;

        // Save the verification code in the database
        $stmt = $conn->prepare("INSERT INTO users (email, role, name, password, verification_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $role, $name, $password, $verification_code);
        if ($stmt->execute()) {
            // Send verification email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'ejivancablanida@gmail.com'; 
                $mail->Password   = 'acjf ngko qlfb cuju'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('no-reply@example.com', 'No Reply');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body    = 'Your verification code is: ' . $verification_code;

                // Send email
                if ($mail->send()) {
                    $_SESSION['message'] = 'Email sent successfully!';
                }
            } catch (Exception $e) {
                $_SESSION['message'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['message'] = 'Error saving user data.';
        }
    }
}
?>
