<?php

require '../../../../db/db.php';

// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'];
        $card_img = $_POST['card_img'];

        if (empty($email) || empty($card_img)) {
            throw new Exception('Invalid input');
        }

        // Check if the user has already hearted this card
        $checkHeartQuery = "SELECT 1 FROM user_hearts WHERE email = ? AND card_img = ?";
        $stmt = $conn->prepare($checkHeartQuery);
        $stmt->bind_param("ss", $email, $card_img);
        $stmt->execute();
        $stmt->store_result();

        $action = $stmt->num_rows > 0 ? 'active' : 'inactive';

        echo json_encode(['status' => 'success', 'action' => $action]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
