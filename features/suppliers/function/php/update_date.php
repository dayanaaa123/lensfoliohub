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

// Get the posted data
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['date'])) {
    die(json_encode(['status' => 'error', 'message' => 'No date provided.']));
}

$date = $data['date'];

// Prepare a SQL statement to fetch current dates for the user
$stmt = $conn->prepare("SELECT day_available FROM users WHERE email = ? AND role = 'supplier'");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$currentDates = $row ? explode(',', $row['day_available']) : [];
$currentDates = array_map('trim', $currentDates); // Trim whitespace

// Check if the date is already in the array
if (in_array($date, $currentDates)) {
    // Remove the date if it's already present
    $currentDates = array_filter($currentDates, function($d) use ($date) {
        return $d !== $date; // Remove the clicked date
    });
} else {
    // Add the new date if it's not already present
    $currentDates[] = $date; 
}

// Convert the array back to a comma-separated string
$updatedDates = implode(',', $currentDates);

// Prepare the SQL statement to update the user with the new date list
$updateStmt = $conn->prepare("UPDATE users SET day_available = ? WHERE email = ? AND role = 'supplier'");
if (!$updateStmt) {
    die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
}

// Bind parameters and execute the update
$updateStmt->bind_param("ss", $updatedDates, $userEmail);
if (!$updateStmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $updateStmt->error]);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Date updated successfully.']);
}

// Close the statements and connection
$stmt->close();
$updateStmt->close();
$conn->close();
?>
