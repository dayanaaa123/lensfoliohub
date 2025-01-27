<?php
session_start();
require '../../../../db/db.php';

if (!isset($_POST['id'])) {
    die('ID not provided.');
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accept'])) {
        $userId = $_POST['id'];
        
        $updateQuery = "UPDATE users SET is_active = 1 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['action_success'] = "Supplier accepted successfully!";
        } else {
            $_SESSION['action_error'] = "Error accepting supplier!";
        }
        header("Location: ../../web/api/admin.php");
    } elseif (isset($_POST['delete'])) {
        $userId = $_POST['id'];
        
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['action_success'] = "Supplier deleted successfully!";
        } else {
            $_SESSION['action_error'] = "Error deleting supplier!";
        }
    }

    exit();
}

?>
