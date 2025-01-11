<?php
require '../../../../db/db.php';

$query = "SELECT * FROM users WHERE role = 'supplier' AND is_active = 0";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['accept'])) {
        $userId = $_POST['user_id'];
        
        $updateQuery = "UPDATE users SET is_active = 1 WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['action_success'] = "Supplier accepted successfully!";
        } else {
            $_SESSION['action_error'] = "Error accepting supplier!";
        }
    } elseif (isset($_POST['delete'])) {
        $userId = $_POST['user_id'];
        
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['action_success'] = "Supplier deleted successfully!";
        } else {
            $_SESSION['action_error'] = "Error deleting supplier!";
        }
    }
    header("Location: ../../web/api/admin.php");
    exit();
}

?>
