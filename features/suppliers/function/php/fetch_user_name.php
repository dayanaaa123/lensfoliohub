<?php
// fetch_user_name.php

include('../../../../db/db.php'); // Include your database connection file

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the email from the query string
$email = $_GET['email'];

// Prepare SQL query to fetch user name and profile picture
$query = "SELECT name, profile_img FROM users WHERE email = ?";
$stmt = $conn->prepare($query);

// Check if prepare failed
if ($stmt === false) {
    die('Error in query preparation: ' . $conn->error);
}

$stmt->bind_param("s", $email); // Bind the email parameter

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo $user['name'] . '|' . $user['profile_img'];  // Concatenate name and profile picture URL with a delimiter
} else {
    echo '';  // Return empty string if no user found
}

// Close the statement
$stmt->close();
?>
