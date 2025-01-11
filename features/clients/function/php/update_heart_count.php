<?php

require '../../../../db/db.php';

// Set headers for JSON response
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'];
        $card_img = $_POST['card_img'];
        $action = $_POST['action'];

        if (empty($email) || empty($card_img) || empty($action)) {
            throw new Exception('Invalid input');
        }

        if ($action === 'active') {
            // Add heart
            $addHeartQuery = "INSERT IGNORE INTO user_hearts (email, card_img) VALUES (?, ?)";
            $updateSnapfeedQuery = "UPDATE snapfeed SET hearts_count = hearts_count + 1 WHERE card_img = ?";
        } else {
            // Remove heart
            $removeHeartQuery = "DELETE FROM user_hearts WHERE email = ? AND card_img = ?";
            $updateSnapfeedQuery = "UPDATE snapfeed SET hearts_count = hearts_count - 1 WHERE card_img = ?";
        }

        $conn->begin_transaction();

        if ($action === 'active') {
            $stmt = $conn->prepare($addHeartQuery);
            $stmt->bind_param("ss", $email, $card_img);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare($removeHeartQuery);
            $stmt->bind_param("ss", $email, $card_img);
            $stmt->execute();
        }

        $stmt = $conn->prepare($updateSnapfeedQuery);
        $stmt->bind_param("s", $card_img);
        $stmt->execute();

        $conn->commit();

        // Get updated hearts count
        $stmt = $conn->prepare("SELECT hearts_count FROM snapfeed WHERE card_img = ?");
        $stmt->bind_param("s", $card_img);
        $stmt->execute();
        $stmt->bind_result($heartsCount);
        $stmt->fetch();

        echo json_encode(['status' => 'success', 'action' => $action, 'hearts_count' => $heartsCount]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

