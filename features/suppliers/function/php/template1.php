<?php
session_start();
include '../../../../db/db.php';

if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $template = isset($_POST['template']) ? $_POST['template'] : 'grid';
    $gallery_name = isset($_POST['gallery_name']) ? $_POST['gallery_name'] : 'default';

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $uploadFileDir = '../../../../assets/img/template/';
        $dest_path = $uploadFileDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $stmt = $conn->prepare("INSERT INTO template1 (email, profile_image, template, gallery_name) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $email, $fileName, $template, $gallery_name);

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
    }
    $conn->close();
}
?>
