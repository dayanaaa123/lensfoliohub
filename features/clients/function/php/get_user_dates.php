<?php
require '../../../../db/db.php';

// Start the session to get the user's email
session_start();
if (!isset($_SESSION['email'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in.']));
}

// Get the uploader's email from local storage (or pass it in some other way)
$uploaderEmail = isset($_POST['uploader_email']) ? htmlspecialchars($_POST['uploader_email']) : '';

// Fetch the available dates for the uploader
$sql = "SELECT day_available FROM users WHERE email = ? AND role = 'supplier'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uploaderEmail);
$stmt->execute();
$result = $stmt->get_result();

$availableDates = [];
if ($row = $result->fetch_assoc()) {
    $availableDates = explode(',', $row['day_available']);
    $availableDates = array_map('trim', $availableDates); // Clean up any whitespace
}

// Return the dates as JSON
echo json_encode(['status' => 'success', 'dates' => $availableDates]);

// Clean up
$stmt->close();
$conn->close();
?>
