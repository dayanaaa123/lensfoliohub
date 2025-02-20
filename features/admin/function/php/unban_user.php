<?php
include '../../../../db/db.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Debug: Check if email exists in reports
    $stmt = $conn->prepare("SELECT * FROM reports WHERE reported_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email found in reports table. Proceeding with update.<br>";

        // Update disable_status in reports table
        $stmt = $conn->prepare("UPDATE reports SET disable_status = 1 WHERE reported_email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            echo "Disable status updated successfully for " . htmlspecialchars($email) . "<br>";
            $_SESSION['success'] = "Account has been unbanned.";

            // Delete data from recovery_requests table
            $stmt = $conn->prepare("DELETE FROM recovery_requests WHERE email = ?");
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                echo "Recovery request deleted successfully.<br>";
            } else {
                echo "Error deleting recovery request: " . $stmt->error;
            }
        } else {
            echo "Error updating disable status: " . $stmt->error;
            $_SESSION['error'] = "There was an error unbanning the account.";
        }
    } else {
        echo "Email not found in reports table.";
    }

    $stmt->close();
    header("Location: ../../web/api/recover.php");
    exit();
}
?>
