<?php
require '../../../../db/db.php';
session_start();

if (isset($_POST['uploader_email']) && !empty($_POST['uploader_email'])) {
    $session_email = $_SESSION['email']; // Logged-in user's email
    $uploader_email = htmlspecialchars($_POST['uploader_email']); // Sender's email from POST

    // Debugging the values
    error_log("Session Email: " . $session_email);
    error_log("Uploader Email: " . $uploader_email);

    // Query to select both sender and receiver messages, combining them
    $stmt = $conn->prepare("SELECT uploader_email as sender, text, created_at, 
                            CASE WHEN uploader_email = ? THEN 'sent' ELSE 'received' END as type 
                            FROM chat 
                            WHERE (email = ? AND uploader_email = ?) OR (email = ? AND uploader_email = ?) 
                            ORDER BY created_at ASC");
    $stmt->bind_param("sssss", $session_email, $session_email, $uploader_email, $uploader_email, $session_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = array();

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
    $stmt->close();
    $conn->close();
} else {
    error_log("Uploader email is missing");
}

?>
