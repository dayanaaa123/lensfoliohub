<?php
require '../../../../db/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    $checkQuery = "SELECT profile_image FROM template1 WHERE id = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    if ($stmtCheck === false) {
        die('Error preparing the check query: ' . $conn->error);
    }
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $imageRow = $resultCheck->fetch_assoc();
        $imagePath = "../../../../assets/img/template/" . $imageRow['profile_image'];

        $deleteQuery = "DELETE FROM template1 WHERE id = ?";
        $stmtDelete = $conn->prepare($deleteQuery);
        if ($stmtDelete === false) {
            die('Error preparing the delete query: ' . $conn->error);
        }
        $stmtDelete->bind_param("i", $id);
        if ($stmtDelete->execute()) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            header("Location: ../../web/api/projects.php?status=success");
            exit();
        } else {
            echo "Error executing delete query: " . $stmtDelete->error;
        }
        $stmtDelete->close();
    } else {
        echo "No record found for ID: " . htmlspecialchars($id);
    }

    $stmtCheck->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>
