<?php
session_start();
require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $reason = htmlspecialchars($_POST['reason']);
    $reporterName = htmlspecialchars($_POST['reporter_name']);
    $reporterEmail = htmlspecialchars($_POST['reporter_email']);
    $reportedName = htmlspecialchars($_POST['reported_name']);
    $reportedEmail = htmlspecialchars($_POST['reported_email']);
    $role = htmlspecialchars($_POST['role']);


    // Insert the report into the `reports` table
    $query = "INSERT INTO reports (reporter_name, reporter_email, reported_name, reported_email, role, reason, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "ssssss",
        $reporterName,
        $reporterEmail,
        $reportedName,
        $reportedEmail,
        $role,
        $reason
    );

    if ($stmt->execute()) {
        // Redirect to a success page or display a success message
        header("Location:../../web/api/about-me.php");
        exit();
    } else {
        // Handle errors
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if the request method is not POST
    header("Location:../../web/api/about-me.php");
    exit();
}
?>
