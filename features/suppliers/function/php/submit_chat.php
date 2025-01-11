<?php
// Start the session
session_start();

require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $email = isset($_SESSION['email']) ? $conn->real_escape_string($_SESSION['email']) : null; // Email from session
    $uploader_email = isset($_POST['uploader_email']) ? $conn->real_escape_string($_POST['uploader_email']) : null; // Uploader email from POST
    $text = isset($_POST['text']) ? $conn->real_escape_string($_POST['text']) : null; // Chat message

    // Check if email is set
    if (!empty($email)) {
        // Debug: Ensure session email is correct
        echo "Session email: " . $email . "<br>";

        // Get the role from the `users` table
        $roleQuery = "SELECT role FROM users WHERE email = '$email'";
        $roleResult = $conn->query($roleQuery);

        if (!$roleResult) {
            die("Role query failed: " . $conn->error);
        }
        if ($roleResult->num_rows > 0) {
            $roleRow = $roleResult->fetch_assoc();
            $role = $roleRow['role']; // Role from the database
            echo "Fetched role: " . $role . "<br>"; // Debugging
        } else {
            die("No user found with email: " . $email);
        }
    } else {
        die("Session email is missing.");
    }

    // Check if required fields are not empty
    if (!empty($uploader_email) && !empty($text)) {
        // SQL query to insert the chat message
        $sql = "INSERT INTO chat (email, uploader_email, text, role) 
                VALUES ('$email', '$uploader_email', '$text', '$role')";

        if ($conn->query($sql) === TRUE) {
            // Redirect or display success message
            header("Location: ../../web/api/contacts.php");
            exit();
        } else {
            die("Insert failed: " . $conn->error);
        }
    } else {
        die("All fields are required.");
    }
}

// Close the connection
$conn->close();
?>
