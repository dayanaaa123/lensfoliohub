<?php
session_start(); 

require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../../../../assets/img/snapfeed/";  
    $file_name = basename($_FILES["card_img"]["name"]); 
    $card_img = $target_dir . $file_name; 

    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'webm'); 

    if (in_array($file_ext, $allowed_extensions)) {
        if (move_uploaded_file($_FILES["card_img"]["tmp_name"], $card_img)) {

            if (isset($_SESSION['name'])) {
                $card_title = $_SESSION['name'];  
            } else {
                $card_title = "Unknown"; 
            }

            $card_text = $_POST['card_text'];
            $img_title = $_POST['img_title'];

            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
            } else {
            }

            // Insert data into the MySQL database
            $sql = "INSERT INTO snapfeed (img_title, card_img, card_text, email) 
                    VALUES ('$img_title', '$file_name', '$card_text', '$email')"; // Use the file name here

            if ($conn->query($sql) === TRUE) {
                header("Location: ../../web/api/snapfeed.php?success=1");
                exit();
            }else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Sorry, only images and videos (jpg, jpeg, png, gif, mp4, avi, mov, webm) are allowed.";
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
