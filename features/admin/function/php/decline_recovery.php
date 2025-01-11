<?php
include '../../../../db/db.php'; 

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $stmt = $conn->prepare("DELETE FROM recovery_requests WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Recovery request has been declined and deleted.";
    } else {
        $_SESSION['error'] = "There was an error declining and deleting the recovery request.";
    }

    $stmt->close();
    header("Location: ../../web/api/recover.php");
    exit();
}
?>
