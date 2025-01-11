<?php
require '../../../../db/db.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Query to fetch messages for the given email
    $stmt = $conn->prepare("SELECT uploader_email as sender, text, 
                            CASE WHEN uploader_email = ? THEN 'sent' ELSE 'received' END as type 
                            FROM chat 
                            WHERE email = ? 
                            ORDER BY id ASC");
    $stmt->bind_param("ss", $session_email, $email);
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
