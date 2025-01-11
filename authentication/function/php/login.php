<?php
session_start();
require '../../../db/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['login_error'] = "Please enter both email and password!";
        header("Location: ../../web/api/login.php");
        exit();
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details from users table
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND verify_status = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashedPassword = $user['password'];

        // Check if the account is disabled in the reports table
        $reportStmt = $conn->prepare("SELECT disable_status FROM reports WHERE reported_email = ?");
        $reportStmt->bind_param("s", $email);
        $reportStmt->execute();
        $reportResult = $reportStmt->get_result();

        if ($reportResult->num_rows > 0) {
            $report = $reportResult->fetch_assoc();
            if ($report['disable_status'] == 0) {
                $_SESSION['login_error'] = 'Your account has been disabled. Please <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recoverModal">Recover</button>';
                header("Location: ../../web/api/login.php");
                exit();
            }
        }

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();

            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] === 'supplier') {
                header("Location: ../../../index.php");
                exit();
            } elseif ($user['role'] === 'admin') {
                header("Location: ../../../features/admin/web/api/admin.php");
                exit();
            } elseif ($user['role'] === 'customer') {
                header("Location: ../../../index.php");
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid role!";
                header("Location: ../../web/api/login.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "Invalid email or password!";
            header("Location: ../../web/api/login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Invalid email or password!";
        header("Location: ../../web/api/login.php");
        exit();
    }
}
?>
