<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../PHPMailer/src/Exception.php';
require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';

function sendResetEmail($email) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ejivancablanida@gmail.com'; 
        $mail->Password   = 'acjf ngko qlfb cuju'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('Barkyards@gmail.com', 'Barks Yards');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset Code';
        $reset_code = rand(1000, 9999);  // Generate a 4-digit reset code
        $mail->Body    = "Your password reset code is: $reset_code";

        $mail->send();
        
        // Store reset code and expiry time in the database (for demonstration, assuming it's stored correctly)
        $_SESSION['reset_code'] = $reset_code;
        $_SESSION['reset_email'] = $email;

        return "Reset code sent successfully to $email.";
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_reset_email'])) {
    $email = $_POST['email'];
    echo sendResetEmail($email);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_reset_code'])) {
    $entered_code = $_POST['verification_code'];
    $password = $_POST['password'];  // Ensure we're using `password` here

    if ($entered_code == $_SESSION['reset_code']) {
        // Assuming you have a database connection
        require '../../../db/db.php'; 

        $email = $_SESSION['reset_email'];

        // No hashing yet, you want to see the plain text in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt === false) {
            die('Prepare() failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param('ss', $password, $email); 
        if ($stmt->execute()) {
            echo "<p class='alert alert-success'>Password reset successful! Redirecting to login...</p>";
            header('Location: ../../web/api/login.php');
            exit();
        } else {
            echo 'Execute() failed: ' . htmlspecialchars($stmt->error);
        }
    } else {
        echo "<p class='alert alert-danger'>Invalid verification code.</p>";
    }
}
?>
