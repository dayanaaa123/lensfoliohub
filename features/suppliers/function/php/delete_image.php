<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

require '../../../../db/db.php';

if (isset($_POST['id'])) {
    $imageId = intval($_POST['id']);

    // Prepare and execute delete query
    $sql = "DELETE FROM snapfeed WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $imageId);

    if ($stmt->execute()) {
        echo "Image deleted successfully.";
    } else {
        echo "Error deleting image: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
