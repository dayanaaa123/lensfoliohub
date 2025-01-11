<?php
include('../../../../db/db.php');
session_start(); // Start the session to access the current user's email

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointmentId = $_POST['appointmentId'];
    $status = $_POST['status'];

    // Get the email of the logged-in user (session) - email uploader
    $emailUploader = $_SESSION['email'];

    // Fetch the email of the person associated with the appointment
    $sql = "SELECT email FROM appointment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $userEmail = $row['email'];
    $stmt->close();

    // Update the status in the appointment table
    $sqlUpdate = "UPDATE appointment SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("si", $status, $appointmentId);

    if ($stmt->execute()) {
        // Create the notification message based on the status
        $message = "";
        if ($status == 'Accepted') {
            $message = "Your appointment has been accepted.";
        } elseif ($status == 'Declined') {
            $message = "Your appointment has been declined.";
        } elseif ($status == 'Completed') {
            $message = "Your appointment has been completed.";
        }

        // Insert a single notification record for both the user and the uploader
        $notif_stmt = $conn->prepare("INSERT INTO notification (email, email_uploader, status, message) VALUES (?, ?, ?, ?)");
        $notif_stmt->bind_param("ssss", $userEmail, $emailUploader, $status, $message);
        $notif_stmt->execute();

        // Success response
        echo 'Success';
    } else {
        echo 'Error updating status';
    }

    $stmt->close();
    $conn->close();
}
?>
