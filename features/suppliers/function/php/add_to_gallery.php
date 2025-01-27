<?php
session_start();
include '../../../../db/db.php';

if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gallery_name = isset($_POST['gallery_name']) ? $_POST['gallery_name'] : 'default'; 

    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gallery_image']['tmp_name'];
        $fileName = $_FILES['gallery_image']['name'];
        
        // Define the relative path for the upload directory
        $uploadFileDir = '../../../../assets/img/gallery/';
        $dest_path = $uploadFileDir . $fileName;

        // Ensure the upload directory exists
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $stmt = $conn->prepare("INSERT INTO gallery_images (email, gallery_name, image_name) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $email, $gallery_name, $fileName);

            if ($stmt->execute()) {
                header('Location: ../../web/api/projects.php');
                exit();
            } else {
                echo 'Error: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            die('Error moving uploaded file');
        }
    } else {
        echo 'Error: No file uploaded or upload error';
    }
}

$conn->close();
?>
