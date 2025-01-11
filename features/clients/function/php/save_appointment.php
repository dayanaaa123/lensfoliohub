<?php
require '../../../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['fullname'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $event = $_POST['event'];
    $time = $_POST['time'];
    $selected_date = $_POST['selected_date'];
    $email_uploader = $_POST['email_uploader'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO appointment (name, latitude, longitude, event, time, selected_date, email_uploader, email) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsssss", $name, $latitude, $longitude, $event, $time, $selected_date, $email_uploader, $email);

    if ($stmt->execute()) {
        $message = "Your appointment is pending approval.";

        $notifStmt = $conn->prepare("INSERT INTO notification (email, email_uploader, message, status) 
                                     VALUES (?, ?, ?, 'pending')");
        $notifStmt->bind_param("sss", $email, $email_uploader, $message);

        if ($notifStmt->execute()) {
            header('Location:../../web/api/status.php');
            exit();
        } else {
            echo "Error sending notification: " . $notifStmt->error;
        }

        $notifStmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
