<?php

session_start();
include '../../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profileImg = 'profile.jpg';
    $isActive = 0;

    if (isset($_POST['social_link']) && !empty($_POST['social_link'])) {
        $socialLink = $_POST['social_link'];
    } else {
        $socialLink = NULL;
    }

    if ($role == 'customer') {
        $sql = "INSERT INTO users (role, name, email, address, birthday, social_link, password, profile_img, is_active) 
                VALUES ('$role', '$name', '$email', '$address', '$birthday', '$socialLink', '$password', '$profileImg', '$isActive')";
    } elseif ($role == 'supplier') {
        $profession = $_POST['profession'];
        $yearsInProfession = $_POST['years_in_profession'];

        $sql = "INSERT INTO users (role, name, email, address, birthday, social_link, profession, years_in_profession, password, profile_img, is_active) 
                VALUES ('$role', '$name', '$email', '$address', '$birthday', '$socialLink', '$profession', '$yearsInProfession', '$password', '$profileImg', '$isActive')";
    }

    if ($conn->query($sql) === TRUE) {
        $_SESSION['signup_success'] = "Sign up successful! Log in to your account.";
        header("Location: ../../web/api/login.php");
        exit();
    } else {
        if ($conn->errno == 1062) {
            echo "Error: This email is already registered. Please use a different email.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
