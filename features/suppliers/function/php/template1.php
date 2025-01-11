<?php
session_start();
include '../../../../db/db.php';

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $template = isset($_POST['template']) ? $_POST['template'] : 'grid'; // Default to grid if not selected

    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name']; // Use the uploaded file's original name
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/lensfoliohub/assets/img/template/';
        $dest_path = $uploadFileDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Insert the image filename, email, and template into the database
            $stmt = $conn->prepare("INSERT INTO template1 (email, profile_image, template) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $email, $fileName, $template);

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
