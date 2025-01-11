<?php
include('../../../../db/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cancelReason = $_POST['cancelReason'];
    $appointmentId = $_POST['appointmentId'];

    // Fetch appointment details to get the emails of the user and the uploader
    $sql = "SELECT email, email_uploader FROM appointment WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    $userEmail = $appointment['email'];
    $emailUploader = $appointment['email_uploader'];
    $stmt->close();

    // Update the appointment status to 'Cancelled'
    $sql = "UPDATE appointment SET status = 'Cancelled', cancel_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cancelReason, $appointmentId);

    if ($stmt->execute()) {
        // Insert notification for both the user and the uploader
        $message = "Appointment has been cancelled.";

        // Insert notification with both email and email_uploader
        $notif_stmt = $conn->prepare("INSERT INTO notification (email, email_uploader, status, message) VALUES (?, ?, 'cancelled', ?)");
        $notif_stmt->bind_param("sss", $userEmail, $emailUploader, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        // Redirect to success page
        header('Location: ../../web/api/status.php?message=success');
        exit();
    } else {
        echo 'Error occurred while updating appointment status.';
    }

    $stmt->close();
    $conn->close();
}
?>
