<?php
session_start();
include '../../../db/db.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $recovery_reason = htmlspecialchars($_POST['recovery_reason']);

    // Check if email and reason are not empty
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email address.";
        header("Location: ../../web/api/login.php");
        exit();
    }

    if (empty($recovery_reason)) {
        $_SESSION['error'] = "Please provide a reason for the recovery request.";
        header("Location: ../../web/api/login.php");
        exit();
    }

    // Prepare SQL query to insert the recovery request into the database
    $stmt = $conn->prepare("INSERT INTO recovery_requests (email, recovery_reason) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $recovery_reason);

    // Execute the query and check for success
    if ($stmt->execute()) {
        $_SESSION['success'] = "Your recovery request has been submitted successfully.";
    } else {
        $_SESSION['error'] = "There was an error processing your request.";
    }

    $stmt->close();
    header("Location: ../../web/api/login.php");
    exit();
}
?>
