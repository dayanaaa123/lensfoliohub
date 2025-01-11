<?php
include '../../../../db/db.php';

session_start();

if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = !empty($_POST['name']) ? trim($_POST['name']) : null;
    $address = !empty($_POST['address']) ? trim($_POST['address']) : null;
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $social_link = !empty($_POST['social_link']) ? filter_var($_POST['social_link'], FILTER_SANITIZE_URL) : null;
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
    $profile_img = null;
    $errors = [];
    $notification_message = null;

    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $notification_message = "Password has been changed.";
        }
    }

    // Fetch current profile image before updating (to delete old image)
    $stmt = $conn->prepare("SELECT profile_img FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_img = $result->fetch_assoc()['profile_img'];
    $stmt->close();

    // Handle file upload (if provided)
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $upload_dir = '../../../../assets/img/profile/';
        $file_tmp = $_FILES['profile_img']['tmp_name'];
        $file_name = basename($_FILES['profile_img']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file type. Allowed types: jpg, jpeg, png, gif.";
        } else {
            $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $errors[] = "Failed to upload profile image.";
            } else {
                $profile_img = $new_file_name;
                if ($current_img && file_exists($upload_dir . $current_img)) {
                    unlink($upload_dir . $current_img);
                }
            }
        }
    }

    if (empty($errors)) {
        // Dynamically build the query to update only provided fields
        $fields = [];
        $params = [];
        $types = '';

        if ($name !== null) {
            $fields[] = "name = ?";
            $params[] = $name;
            $types .= 's';
        }
        if ($address !== null) {
            $fields[] = "address = ?";
            $params[] = $address;
            $types .= 's';
        }
        if ($birthday !== null) {
            $fields[] = "birthday = ?";
            $params[] = $birthday;
            $types .= 's';
        }
        if ($social_link !== null) {
            $fields[] = "social_link = ?";
            $params[] = $social_link;
            $types .= 's';
        }
        if ($profile_img !== null) {
            $fields[] = "profile_img = ?";
            $params[] = $profile_img;
            $types .= 's';
        }
        if (!empty($hashed_password)) {
            $fields[] = "password = ?";
            $params[] = $hashed_password;
            $types .= 's';
        }

        $params[] = $email;
        $types .= 's';

        $update_query = "UPDATE users SET " . implode(', ', $fields) . " WHERE email = ?";
        $stmt = $conn->prepare($update_query);

        if ($stmt) {
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                if (!$notification_message) {
                    $notification_message = "Profile has been updated.";
                }

                // Add notification
                $notif_stmt = $conn->prepare("INSERT INTO notification (email, status, message) VALUES (?, 'update', ?)");
                $notif_stmt->bind_param("ss", $email, $notification_message);
                $notif_stmt->execute();
                $notif_stmt->close();

                header('Location: ../../web/api/profile.php?success=Profile updated successfully');
                exit;
            } else {
                header('Location: ../../web/api/profile.php?error=Failed to update profile.');
                exit;
            }

            $stmt->close();
        } else {
            header('Location: ../../web/api/profile.php?error=Database query preparation failed.');
            exit;
        }
    } else {
        header('Location: ../../web/api/profile.php?error=' . urlencode(implode(', ', $errors)));
        exit;
    }
}

$conn->close();
?>
