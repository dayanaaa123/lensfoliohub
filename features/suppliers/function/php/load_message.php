<?php
// Include the database connection file
require_once '../../../../db/db.php';

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$session_email = $_SESSION['email']; // Get session email

// Get uploader email from the GET request
$uploader_email = isset($_GET['uploader_email']) ? htmlspecialchars($_GET['uploader_email']) : '';

if (!empty($uploader_email)) {
    // Fetch chat messages between the session email and uploader email
    $query = "
        SELECT * 
        FROM chat 
        WHERE 
            (email = ? AND email_uploader = ?) OR 
            (email = ? AND email_uploader = ?)
        ORDER BY created_at ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $session_email, $uploader_email, $uploader_email, $session_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output messages in the required format
    while ($row = $result->fetch_assoc()) {
        if ($row['email'] === $session_email) {
            // Sent message
            echo "
                <div class='message sent'>
                    <div class='message-text'>{$row['message']}</div>
                </div>
            ";
        } else {
            // Received message
            echo "
                <div class='message received'>
                    <img src='https://via.placeholder.com/40' alt='Supplier' class='profile-pic'>
                    <div class='message-text'>{$row['message']}</div>
                </div>
            ";
        }
    }
    $stmt->close();
} else {
    echo "No messages found.";
}
?>