<?php
session_start();
include '../../../../db/db.php';

// Ensure email is stored in session
if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $profession = isset($_POST['profession']) ? implode(',', $_POST['profession']) : '';
    $about_me = $_POST['about_me'];
    $age = $_POST['age'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $location_text = $_POST['location_text'];
    $price = $_POST['price'];
    $portfolio = $_POST['portfolio'];  // Get portfolio URL from form

    $profileImg = ''; // Initialize variable

    // Delete current profile image if a new file is uploaded
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == UPLOAD_ERR_OK) {
        // Fetch the existing profile image from the `users` table
        $stmt = $conn->prepare("SELECT profile_img FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($currentProfileImg);
        $stmt->fetch();
        $stmt->close();

        if (!empty($currentProfileImg)) {
            $filePath = '../../../../assets/img/profile/' . $currentProfileImg; // Relative path to the file
            if (file_exists($filePath)) {
                unlink($filePath); 
            }
        }

        // Repeat the same process for `about_me` table if necessary
        $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($currentAboutMeImg);
        $stmt->fetch();
        $stmt->close();

        if (!empty($currentAboutMeImg)) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/lensfoliohub/assets/img/profile/' . $currentAboutMeImg;
            if (file_exists($filePath)) {
                unlink($filePath); 
            }
        }

        $fileTmpPath = $_FILES['profile_img']['tmp_name'];
        $fileName = $_FILES['profile_img']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExts = array(
            'jpg', 'jpeg', 'png', 'jfif',
            'gif', 'bmp', 'tiff',        
            'mp4', 'mkv', 'avi', 'mov',  
            'flv', 'wmv', 'webm', '3gp',  
            'mpeg', 'mpg', 'm4v'          
        );

        if (in_array($fileExtension, $allowedExts)) {
            $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/lensfoliohub/assets/img/profile/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $uniqueName = uniqid('profile_', true) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $uniqueName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profileImg = $uniqueName;

                $stmt = $conn->prepare("UPDATE users SET profile_img = ? WHERE email = ?");
                $stmt->bind_param('ss', $profileImg, $email);
                $stmt->execute();
                $stmt->close();


                $stmt = $conn->prepare("UPDATE about_me SET profile_image = ? WHERE email = ?");
                $stmt->bind_param('ss', $profileImg, $email);
                $stmt->execute();
                $stmt->close();
            } else {
                die('Error moving uploaded file');
            }
        } else {
            die('Unsupported file type');
        }
    }

    // Check if an entry exists in the about_me table
    $stmt = $conn->prepare("SELECT email FROM about_me WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();

    if ($exists) {
        // Update existing record in about_me (without profile_image)
        $stmt = $conn->prepare("UPDATE about_me SET name = ?, profession = ?, about_me = ?, age = ?, latitude = ?, longitude = ?, location_text = ?, price = ?, portfolio = ? WHERE email = ?");
        $stmt->bind_param('sssssdssss', $name, $profession, $about_me, $age, $latitude, $longitude, $location_text, $price, $portfolio, $email);
    } else {
        // Insert new record into about_me (with profile_image)
        $stmt = $conn->prepare("INSERT INTO about_me (name, profession, about_me, age, latitude, longitude, location_text, price, email, profile_image, portfolio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssdssssss', $name, $profession, $about_me, $age, $latitude, $longitude, $location_text, $price, $email, $profileImg, $portfolio);
    }

    // Execute the query for about_me table
    if ($stmt->execute()) {
        // Also update the name in the users table if the name has been edited
        if (!empty($name)) {
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE email = ?");
            $stmt->bind_param('ss', $name, $email);
            $stmt->execute();
            $stmt->close();
        }

        header('Location: ../../web/api/about-me.php');
        exit();
    } else {
        echo 'Error: ' . $stmt->error;
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>
