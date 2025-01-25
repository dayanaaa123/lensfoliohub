<?php
require '../../../../db/db.php';

// Get email from the POST request
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;

if (!empty($email)) {
    // Update the 'is_seen' field for the message sent by the session user to this email
    $updateQuery = "UPDATE chat SET is_seen = 1 WHERE email = '$email' AND is_seen = 0";
    if ($conn->query($updateQuery) === TRUE) {
        echo 'Message marked as seen.';
    } else {
        echo 'Error marking message as seen.';
    }
} else {
    echo 'Email not provided.';
}

$conn->close();
?>
