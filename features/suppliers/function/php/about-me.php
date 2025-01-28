<?php
session_start();
include '../../../../db/db.php';

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = !empty($_POST['name']) ? $_POST['name'] : null;
    $profession = isset($_POST['profession']) ? implode(',', $_POST['profession']) : null;
    $about_me = !empty($_POST['about_me']) ? $_POST['about_me'] : null;
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    $location_text = !empty($_POST['location_text']) ? $_POST['location_text'] : null;
    $price = !empty($_POST['price']) ? (float)$_POST['price'] : null;
    $portfolio = !empty($_POST['portfolio']) ? $_POST['portfolio'] : null;
    $profileImg = null; // Initialize profile image variable

    // Handle file upload for profile image
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_img']['tmp_name'];
        $fileName = $_FILES['profile_img']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff'];

        if (in_array($fileExtension, $allowedExts)) {
            $uploadFileDir = '../../../../assets/img/profile/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            // Generate a unique name for the image
            $uniqueName = uniqid('profile_', true) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $uniqueName;

            // Delete old profile image if it exists
            $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($oldImage);
                $stmt->fetch();
                $stmt->close();
                
                // Remove the old image if it exists
                if ($oldImage && file_exists($uploadFileDir . $oldImage)) {
                    unlink($uploadFileDir . $oldImage);
                }
            }
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profileImg = $uniqueName;
            } else {
                die('Error moving uploaded file.');
            }
        } else {
            die('Unsupported file type.');
        }
    }

    // Check if an entry exists in the `about_me` table
    $stmt = $conn->prepare("SELECT email FROM about_me WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Update existing record in `about_me` table
        $query = "UPDATE about_me SET name = ?, profession = ?, about_me = ?, age = ?, latitude = ?, longitude = ?, location_text = ?, price = ?, portfolio = ?";
        if ($profileImg !== null) {
            $query .= ", profile_image = ?";
        }
        $query .= " WHERE email = ?";
        
        $stmt = $conn->prepare($query);
        if ($profileImg !== null) {
            $stmt->bind_param(
                'sssiddsssss',
                $name,
                $profession,
                $about_me,
                $age,
                $latitude,
                $longitude,
                $location_text,
                $price,
                $portfolio,
                $profileImg,
                $email
            );
        } else {
            $stmt->bind_param(
                'sssiddssss',
                $name,
                $profession,
                $about_me,
                $age,
                $latitude,
                $longitude,
                $location_text,
                $price,
                $portfolio,
                $email
            );
        }
    } else {
        // Insert new record into `about_me` table
        $stmt = $conn->prepare("INSERT INTO about_me (name, profession, about_me, age, latitude, longitude, location_text, price, email, profile_image, portfolio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'sssiddsdsss',
            $name,
            $profession,
            $about_me,
            $age,
            $latitude,
            $longitude,
            $location_text,
            $price,
            $email,
            $profileImg,
            $portfolio
        );
    }

    // Execute the query for `about_me` table
    if ($stmt->execute()) {
        // Update name in `users` table if it has been edited
        if (!empty($name)) {
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE email = ?");
            $stmt->bind_param('ss', $name, $email);
            $stmt->execute();
            $stmt->close();
        }

        // Redirect to the about-me page
        header('Location: ../../web/api/about-me.php');
        exit();
    } else {
        die('Error executing query: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>
