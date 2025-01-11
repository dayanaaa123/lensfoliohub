<?php
session_start();
include '../../../../db/db.php';

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve selected hours from the POST request
    $availHrs = isset($_POST['avail_hrs']) ? $_POST['avail_hrs'] : '';

    // Ensure avail_hrs is a valid array
    if (!empty($availHrs)) {
        // Explode the string to get an array of selected hours
        $hoursArray = explode(',', $availHrs);

        // Validate the input - ensure only numbers 0-23 are present
        $hoursArray = array_filter($hoursArray, function ($hour) {
            return is_numeric($hour) && $hour >= 0 && $hour <= 23;
        });

        // Convert back to a comma-separated string for storage
        $hoursString = implode(',', $hoursArray);

        // Update the `avail_hrs` column in the `about_me` table
        $stmt = $conn->prepare("UPDATE about_me SET avail_hrs = ? WHERE email = ?");
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param('ss', $hoursString, $email);

        if ($stmt->execute()) {
            // Redirect on success
            header('Location: ../../web/api/calendar.php?status=success');
            exit();
        } else {
            die('Error: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        die('No hours selected.');
    }
}

// Close the database connection
$conn->close();
?>
