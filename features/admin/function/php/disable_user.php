<?php
require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disable'])) {
    $id = intval($_POST['id']);
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
    $query = "SELECT disable_status FROM reports WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $report = $result->fetch_assoc();

    if ($report) {
        $currentStatus = $report['disable_status'];

        if ($currentStatus == 0) {
            $message = "Account is already disabled.";
        } elseif ($currentStatus < 3) {
            $newStatus = $currentStatus + 1;
            $message = "User has been given warning level $newStatus.";
        } else {
            $newStatus = 0;
            $message = "User account has been disabled.";
        }

        $updateQuery = "UPDATE reports SET disable_status = ?, warning_reason = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("isi", $newStatus, $reason, $id);
        if ($updateStmt->execute()) {
            header("Location: ../../web/api/reports.php?message=" . urlencode($message));
            exit();
        } else {
            $message = "Failed to update the user's status.";
        }
    } else {
        $message = "User not found.";
    }

    header("Location: ../../web/api/reports.php?message=" . urlencode($message));
    exit();
}
?>
