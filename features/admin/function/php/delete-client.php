<?php
session_start();
require '../../../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $update_query = "UPDATE users SET disable_status = 0 WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Client status enabled successfully!";
    } else {
        $_SESSION['error'] = "Failed to enable the client status. Please try again.";
    }

    header("Location: ../../web/api/registered-client.php");
    exit();
}
?>
