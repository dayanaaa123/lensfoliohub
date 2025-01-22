<?php
require '../../../../db/db.php';
session_start();

if (isset($_SESSION['email']) && isset($_GET['uploader_email'])) {
    $session_email = $_SESSION['email']; // Logged-in user's email
    $uploader_email = $_GET['uploader_email']; // Email of the uploader (sender)

    // Query to fetch profile_image from about_me table using uploader_email
    $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
    $stmt->bind_param("s", $uploader_email);
    $stmt->execute();
    $result = $stmt->get_result();

    $profile_image = null;

    if ($row = $result->fetch_assoc()) {
        $profile_image = $row['profile_image']; // Fetch the profile image
    }

    echo $profile_image; // Output profile image path as plain text
    $stmt->close();
    $conn->close();
}
?>
