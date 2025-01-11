<?php
session_start();  // Start the session to access session variables

// Database connection
require '../../../../db/db.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the file upload for card image
    $target_dir = "../../../../assets/img/snapfeed/";  // Folder where images will be stored
    $file_name = basename($_FILES["card_img"]["name"]); // Get only the file name
    $card_img = $target_dir . $file_name;  // Create the full file path

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["card_img"]["tmp_name"], $card_img)) {
        // File upload was successful

        // Use the session variable for the card title (assuming 'name' is already set)
        if (isset($_SESSION['name'])) {
            $card_title = $_SESSION['name'];  // Retrieve the name from the session
        } else {
            $card_title = "Unknown";  // Fallback in case the session name is not set
        }

        $card_text = $_POST['card_text'];
        $img_title = $_POST['img_title'];

        // Check if email is set in session
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
        } else {
            $email = "unknown@example.com";  // Fallback in case the session email is not set
        }

        // Insert data into the MySQL database
        $sql = "INSERT INTO snapfeed (img_title, card_img, card_text, email) 
                VALUES ('$img_title', '$file_name', '$card_text', '$email')"; // Use the file name here

        if ($conn->query($sql) === TRUE) {
            header("Location: ../../web/api/snapfeed.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageId = $_POST['image_id'];
    $email = $_SESSION['email'];

    // Check if user already reacted
    $checkSql = "SELECT * FROM hearts WHERE image_id = ? AND email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("is", $imageId, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Remove heart react
        $deleteSql = "DELETE FROM hearts WHERE image_id = ? AND email = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("is", $imageId, $email);
        $stmt->execute();
    } else {
        // Add heart react
        $insertSql = "INSERT INTO hearts (image_id, email) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("is", $imageId, $email);
        $stmt->execute();
    }

    // Get updated heart count
    $countSql = "SELECT COUNT(*) AS heart_count FROM hearts WHERE image_id = ?";
    $stmt = $conn->prepare($countSql);
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $countResult = $stmt->get_result();
    $row = $countResult->fetch_assoc();

    echo json_encode(['newCount' => $row['heart_count']]);
}
?>
