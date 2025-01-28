<?php
require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id']; // Get the id of the image/gallery to be deleted

    // Prepare your delete query to remove images and gallery from the database
    $deleteQuery = "DELETE FROM gallery_images WHERE id = ?";
    $stmtDelete = $conn->prepare($deleteQuery);
    if ($stmtDelete === false) {
        // Error preparing the query
        die('Error preparing the query: ' . $conn->error);
    }
    $stmtDelete->bind_param("i", $id); // Use "i" for integer

    // Execute and check for success
    if ($stmtDelete->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error executing delete query: " . $stmtDelete->error;
    }

    // Optionally, delete the image files from the server
    $imageQuery = "SELECT image_name FROM gallery_images WHERE id = ?";
    $stmtImage = $conn->prepare($imageQuery);
    $stmtImage->bind_param("i", $id);
    $stmtImage->execute();
    $resultImage = $stmtImage->get_result();

    if ($imageRow = $resultImage->fetch_assoc()) {
        $imagePath = "../../../../assets/img/gallery/" . $imageRow['image_name'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file
        }
    }

    // Close statements
    $stmtDelete->close();
    $stmtImage->close();

    // Redirect to the gallery page or show a success message
    header("Location: ../../web/api/projects.php");
    exit();
}
?>
