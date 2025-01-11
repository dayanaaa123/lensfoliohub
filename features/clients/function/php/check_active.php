<?php
require '../../../../db/db.php';

// Check if the request is POST and contains JSON data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];
    $card_img = $data['card_img'];

    // Prepare the query to check if the image is active
    $query = "SELECT 1 FROM user_hearts WHERE email = ? AND card_img = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $card_img);
    $stmt->execute();
    $stmt->store_result();

    // Check if there is a record matching the criteria
    if ($stmt->num_rows > 0) {
        // Image is active
        echo json_encode(['status' => 'active']);
    } else {
        // Image is not active
        echo json_encode(['status' => 'inactive']);
    }

    $stmt->close();
    $conn->close();
}
?>
