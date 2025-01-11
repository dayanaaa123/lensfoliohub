<?php
include '../../../../db/db.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Debugging: Echo the reported_email value
    echo "<strong>Debugging:</strong> Email received: " . htmlspecialchars($email);

    // Update disable_status in the reports table
    $stmt = $conn->prepare("UPDATE reports SET disable_status = 1 WHERE reported_email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account has been unbanned.";
        echo "Disable status updated successfully.";
    } else {
        $_SESSION['error'] = "There was an error unbanning the account.";
        echo "Error updating disable status.";
    }

    $stmt->close();
    header("Location: ../../web/api/recover.php");
    exit();
}
?>
