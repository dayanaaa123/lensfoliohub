<?php
session_start();  // Start the session to access $_SESSION

require '../../../../db/db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $id = $_POST['id']; // You can use this to fetch the card image and email
    $comments = $_POST['comments'];
    
    // Validate inputs (optional but recommended)
    if (empty($id) || empty($comments)) {
        die("ID and comments cannot be empty.");
    }

    // Ensure session email is set
    if (!isset($_SESSION['email'])) {
        die("Session email is not set.");
    }
    $sessionEmail = $_SESSION['email']; // Get session email

    // Fetch card image and email from snapfeed table based on the provided ID
    $stmtFetch = $conn->prepare("SELECT card_img, email FROM snapfeed WHERE id = ?");
    $stmtFetch->bind_param("i", $id); // "i" means integer for id

    // Execute the fetch statement
    $stmtFetch->execute();
    $result = $stmtFetch->get_result();
    $cardImg = '';
    $email = '';

    // Check if there are results and get the values
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cardImg = $row['card_img']; // Get the card image
        $email = $row['email']; // Get the email from snapfeed
    }

    // Prepare the SQL statement to insert the comment into the comments table
    $stmtInsert = $conn->prepare("INSERT INTO comments (card_img, email, comments, session_email) VALUES (?, ?, ?, ?)");
    $stmtInsert->bind_param("ssss", $cardImg, $email, $comments, $sessionEmail); // Bind all fields, including session email

    // Execute the insert statement
    if ($stmtInsert->execute()) {
        header('Location: ../../web/api/snapfeed.php');
        exit;
    } else {
        // Handle the error
        echo '<script>
                console.error("Error: Could not post comment. ' . htmlspecialchars($stmtInsert->error) . '");
              </script>';
    }

    // Close the statements
    $stmtFetch->close();
    $stmtInsert->close();
}

// Close the database connection
$conn->close();
?>
