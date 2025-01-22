<?php
require '../../../../db/db.php';
session_start();

if (isset($_SESSION['email']) && isset($_GET['email'])) {
    $session_email = $_SESSION['email']; // Logged-in user's email
    $email = $_GET['email']; // Email passed from the JS function (click_email)

    // Query to select both sender and receiver messages, combining them
    $stmt = $conn->prepare("SELECT uploader_email as sender, text, created_at, 
                            CASE WHEN uploader_email = ? THEN 'sent' ELSE 'received' END as type 
                            FROM chat 
                            WHERE email = ? OR uploader_email = ? 
                            ORDER BY created_at ASC");
    $stmt->bind_param("sss", $session_email, $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = array();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    $stmt->close();
    $conn->close();
}
?>
