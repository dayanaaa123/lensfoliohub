<?php
session_start();  


require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../../../../assets/img/snapfeed/";  
    $card_img = $target_dir . basename($_FILES["card_img"]["name"]);

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
            $email = "unknown@example.com"; 
        }

        $sql = "INSERT INTO snapfeed (img_title, card_img, card_text, email) 
                VALUES ('$img_title', '$card_img', '$card_text', '$email')";

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

    $checkSql = "SELECT * FROM hearts WHERE image_id = ? AND email = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("is", $imageId, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $deleteSql = "DELETE FROM hearts WHERE image_id = ? AND email = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("is", $imageId, $email);
        $stmt->execute();
    } else {
        $insertSql = "INSERT INTO hearts (image_id, email) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("is", $imageId, $email);
        $stmt->execute();
    }

    $countSql = "SELECT COUNT(*) AS heart_count FROM hearts WHERE image_id = ?";
    $stmt = $conn->prepare($countSql);
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $countResult = $stmt->get_result();
    $row = $countResult->fetch_assoc();

    echo json_encode(['newCount' => $row['heart_count']]);
}
?>
