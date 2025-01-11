<?php
session_start();
include '../../../../db/db.php';

// Function to delete from about_me table
function deleteAboutMe($email) {
    global $conn;

    // Prepare DELETE statement
    $stmt = $conn->prepare("DELETE FROM about_me WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        echo 'Error: ' . $stmt->error;
        $stmt->close();
        return false;
    }
}

// Function to delete from snapfeed table
function deleteSnapfeed($email) {
    global $conn;

    // Prepare DELETE statement
    $stmt = $conn->prepare("DELETE FROM snapfeed WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        echo 'Error: ' . $stmt->error;
        $stmt->close();
        return false;
    }
}

// Function to delete from users table
function deleteUser($email) {
    global $conn;

    // Prepare DELETE statement for users table
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param('s', $email);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        echo 'Error: ' . $stmt->error;
        $stmt->close();
        return false;
    }
}

// Function to delete user account from all tables
function deleteUserAccount($email) {
    global $conn;

    // Delete from about_me
    $aboutMeDeleted = deleteAboutMe($email);

    // Delete from snapfeed
    $snapfeedDeleted = deleteSnapfeed($email);

    // Delete from users
    $userDeleted = deleteUser($email);

    if ($aboutMeDeleted && $snapfeedDeleted && $userDeleted) {
        return true;
    } else {
        return false;
    }
}

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $result = deleteUserAccount($email);
    if ($result) {
        // Successfully deleted from all tables, redirect to login page
        header("Location: ../../../../authentication/web/api/login.php");
        exit();
    } else {
        echo 'Error: Unable to delete account. Please try again.';
    }
}
?>
