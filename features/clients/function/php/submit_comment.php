<?php
require '../../../../db/db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comments = $_POST['comments'];
    $hearts_count = $_POST['hearts_count'];
    $id = $_POST['id']; // Assume 'id' is sent in the POST request

    // Prepare the SQL statement to update the record
    $stmt = $mysqli->prepare("UPDATE snapfeed SET comments = ?, hearts_count = ? WHERE id = ?");
    $stmt->bind_param("sii", $comments, $hearts_count, $id); // "sii" means string, integer, integer types

    // Execute the statement
    if ($stmt->execute()) {
        // Echo a success message
        echo "Comment updated successfully for ID: " . $id;
    } else {
        echo "Error updating comment: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the MySQLi connection
$mysqli->close();
?>