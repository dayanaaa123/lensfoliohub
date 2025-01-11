<?php
// Database configuration
require '../../../../db/db.php';

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Start the session to get the user's email
session_start();
if (!isset($_SESSION['email'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in.']));
}
$userEmail = $_SESSION['email']; // Ensure this session variable is set

// Prepare a SQL statement to fetch current dates for the user
$stmt = $conn->prepare("SELECT day_available FROM users WHERE email = ? AND role = 'supplier'");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$currentDates = $row ? explode(',', $row['day_available']) : [];
$currentDates = array_map('trim', $currentDates); // Trim whitespace

// Return the available dates in JSON format
echo json_encode(['status' => 'success', 'dates' => $currentDates]);

// Close the statements and connection
$stmt->close();
$conn->close();
?>
