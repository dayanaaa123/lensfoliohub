<?php
// submit_rating.php

session_start();

include '../../../../db/db.php';

// Get form data
$user_email = $_POST['user_email'];  // Logged-in user's email
$supplier_email = $_POST['supplier_email'];  // Supplier's email
$rating = $_POST['rating'];  // Rating (1-5)
$review = $_POST['review'];  // Optional review

// Fetch user name associated with the user_email
$stmt_name = $conn->prepare("SELECT name FROM users WHERE email = ?");
$stmt_name->bind_param("s", $user_email);
$stmt_name->execute();
$result_name = $stmt_name->get_result();

// If user exists, get the name
if ($row = $result_name->fetch_assoc()) {
    $name = $row['name'];  // Correct column name 'name'
} else {
    $name = '';  // In case the user is not found
}

$stmt_name->close();

// Prepare and bind SQL statement for inserting rating
$stmt = $conn->prepare("INSERT INTO ratings (user_email, supplier_email, name, rating, review) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssis", $user_email, $supplier_email, $name, $rating, $review);

// Execute the query
if ($stmt->execute()) {
    // Redirect to status.php with success message
    header("Location: ../../web/api/status.php?success=1");
    exit();
} else {
    echo "error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
