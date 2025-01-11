<?php
session_start();
include '../../../../db/db.php';

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

// Initialize variables to avoid undefined variable warnings
$name = $address = $birthday = $fb_ig_link = $profileImg = '';

// Fetch the user's current profile data
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($name, $address, $birthday, $fb_ig_link, $profileImg);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect input values
    $name = $_POST['name'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $fb_ig_link = $_POST['fb_ig_link'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    // Hash the password if it is provided
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            die('Unsupported file type.');
        }

        // Define upload path
        $uploadDir = '../../../../assets/img/profile/';
        $destPath = $uploadDir . basename($fileName);

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $profileImg = $destPath;
        } else {
            die('Error uploading file.');
        }
    }

    // Update users table
    $query = "UPDATE users SET name = ?, address = ?, birthday = ?, fb_ig_link = ?, profile_image = ?";

    $params = [$name, $address, $birthday, $fb_ig_link, $profileImg];
    $types = "sssss";

    // Append password update if provided
    if (!empty($hashedPassword)) {
        $query .= ", password = ?";
        $params[] = $hashedPassword;
        $types .= "s";
    }

    $query .= " WHERE email = ?";
    $params[] = $email;
    $types .= "s";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo 'Profile updated successfully!';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
