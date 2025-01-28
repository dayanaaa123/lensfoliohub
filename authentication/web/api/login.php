<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../PHPMailer/src/Exception.php';
require '../../../PHPMailer/src/PHPMailer.php';
require '../../../PHPMailer/src/SMTP.php';

include '../../../db/db.php'; // Include your DB connection

$message = ''; // Variable to store messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if verification code was entered (this happens after email is sent)
    if (isset($_POST['verification_code']) && $_POST['verification_code'] != '') {
        if ($_POST['verification_code'] == $_SESSION['verification_code']) {
            // Correct code, update the verify_status to 1
            $email = $_POST['email'];

            // Password match check
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm-password'];

            if ($password !== $confirm_password) {
                $message = 'Passwords do not match!';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Update the user's password and verification status
                $stmt = $conn->prepare("UPDATE users SET verify_status = 2, password = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                if ($stmt->execute()) {
                    $message = 'Registered Successfully! Click log in to continue';
                } else {
                    $message = 'Error updating verification status.';
                }
                $stmt->close();
            }
        } else {
            $message = 'Incorrect verification code!';
        }
    } else {
        // Initial verification, send email and store verification code in the DB
        $email = $_POST['email'];
        $role = $_POST['role'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];
        $birthday = $_POST['birthday'];
        $address = $_POST['address'];
        $social_link = !empty($_POST['social_link']) ? $_POST['social_link'] : null;
        $profession = !empty($_POST['profession']) ? $_POST['profession'] : null;
        $years_in_profession = !empty($_POST['years_in_profession']) ? $_POST['years_in_profession'] : null;

        if ($password !== $confirm_password) {
            $message = 'Passwords do not match!';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Check if the email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($emailCount);
            $stmt->fetch();
            $stmt->close();

            if ($emailCount > 0) {
                $message = 'Duplicate email!';
            } else {
                // Generate a 4-digit verification code
                $verification_code = rand(1000, 9999);
                $_SESSION['verification_code'] = $verification_code;

                // Save the verification code and other fields in the database
                $stmt = $conn->prepare("
                    INSERT INTO users (email, role, name, password, verification_code, birthday, address, social_link, profession, years_in_profession) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("ssssssssss", $email, $role, $name, $hashed_password, $verification_code, $birthday, $address, $social_link, $profession, $years_in_profession);
                if ($stmt->execute()) {
                    // Send verification email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->SMTPDebug = 2;
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.hostinger.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'lensfoliohub@lensfoliohub.com'; 
                        $mail->Password   = '#Bakitako23'; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('lensfoliohub@lensfoliohub.com', 'LENSFOLIOHUB');
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body    = 'Your verification code is: ' . $verification_code;

                        // Send email
                        if ($mail->send()) {
                            $message = 'Email sent successfully!';
                        }
                    } catch (Exception $e) {
                        $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $message = 'Error saving user data.';
                }
                $stmt->close();
            }
        }
    }
    echo $message;
    exit;
}



?>




<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>LENSFOLIOHUB</title>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="../../css/login.css">
</head>



<script>
	document.querySelector('input[type="date"]').addEventListener('focus', function() {
    this.value = '';  // Clear default value
});
</script>

<body>

	<div class="scene d-flex justify-content-center align-items-center">

		
		<div class="container box">
			<div class="box1 box11 text-center">
				<h3>LENSFOLIOHUB</h3>
				<div class="line"></div>
				<div class="dont mt-4">
					<p class="register-p d-flex justify-content-center">Enter your details and start your journey with us</p>
					<p class="mb-0 d-flex text-align-center justify-content-center align-items-center mx-auto gap-1 mt-2" >Don't have an account?</p>
					<button class="registerbtn" id="registerBtn">GET STARTED</button>
				</div>
				</div>
			<div class="box2 box22">
			<?php
			require '../../../db/db.php'; 
				if (isset($_SESSION['signup_success'])) {
					echo '<div class="alert alert-success">' . $_SESSION['signup_success'] . '</div>';
					unset($_SESSION['signup_success']);
				}
				if (isset($_SESSION['login_error'])) {
					echo '<div class="alert alert-danger">' . $_SESSION['login_error'] . '</div>';
					unset($_SESSION['login_error']);
				}
			?>
			<div class="wrapper">
    <form id="loginForm" action="../../function/php/login.php" method="POST">
        <h2>Login</h2>
        <div class="input-field">
            <input type="text" name="email" required>
            <label>Enter your email</label>
        </div>
        <div class="input-field">
            <input type="password" name="password" required>
            <label>Enter your password</label>
        </div>
        <a href="#" id="forgotPassword" class="forgot">Forgot Password?</a>
        <button type="submit" class="mt-2">Log In</button>
    </form>
    
    <form id="resetForm" style="display:none;">
        <h2>Forgot Password?</h2>
        <div class="input-field">
            <input type="email" name="reset_email" required>
            <label>Enter your email</label>
        </div>
        <button type="button" id="sendCode" class="mt-2">Send Code</button>
    </form>
    
    <form id="resetCodeForm" style="display:none;">
        <h2>Enter the 4-digit Code</h2>
        <div class="input-field">
            <input type="text" name="reset_code" required>
            <label>Enter 4-digit code</label>
        </div>
        <button type="submit" class="mt-2">Verify</button>
    </form>
    
    <form id="passwordResetForm" style="display:none;">
        <h2>Reset Password</h2>
        <div class="input-field">
            <input type="password" name="password" required>
            <label>Enter new password</label>
        </div>
        <button type="submit" class="mt-2">Reset Password</button>
    </form>
</div>

<script>
 document.getElementById('forgotPassword').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('resetForm').style.display = 'block';
});

document.getElementById('sendCode').addEventListener('click', function(e) {
    e.preventDefault();
    var email = document.querySelector('input[name="reset_email"]').value;

    // Send the email with the reset code via AJAX
    sendResetEmail(email);
});

function sendResetEmail(email) {
    $.ajax({
        type: "POST",
        url: "../../function/php/send_reset_code.php",
        data: {email: email, send_reset_email: true},
        success: function(response) {
            console.log('Response from server: ', response);
            alert(response);  // Display the success message
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('resetCodeForm').style.display = 'block';
        },
        error: function(error) {
            console.log('Error: ', error);
            alert('Error sending email. Please try again.');
        }
    });
}

document.getElementById('resetCodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var enteredCode = document.querySelector('input[name="reset_code"]').value;
    var newPassword = document.querySelector('input[name="password"]').value;  // Correct form field for password

    // Send the verification code and new password to reset the password via AJAX
    $.ajax({
        type: "POST",
        url: "../../function/php/send_reset_code.php",
        data: {verification_code: enteredCode, password: newPassword, verify_reset_code: true},
        success: function(response) {
            console.log('Response from server: ', response);
            if (response.includes('successful')) {
                alert(response);  // Display the success message
                document.getElementById('resetCodeForm').style.display = 'none';
                document.getElementById('passwordResetForm').style.display = 'block';
            } else {
                alert(response);  // Display error messages if not successful
            }
        },
        error: function(error) {
            console.log('Error: ', error);
            alert('Error resetting password. Please try again.');
        }
    });
});

</script>


				<?php if (isset($_SESSION['disable_status']) && $_SESSION['disable_status'] === true): ?>
				<script>
					document.addEventListener("DOMContentLoaded", function() {
						var modal = new bootstrap.Modal(document.getElementById('disableModal'));
						modal.show();
					});
				</script>
				<?php unset($_SESSION['disable_status']); ?>
			<?php endif; ?>
			</div>
		</div>

		<div class="modal fade" id="recoverModal" tabindex="-1" role="dialog" aria-labelledby="recoverModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<form action="../../function/php/process_recovery.php" method="POST">
						<div class="modal-header">
							<h5 class="modal-title" id="recoverModalLabel">Request Account Recovery</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="emailAddress" class="form-label">Email Address</label>
								<input type="email" class="form-control" id="emailAddress" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
							</div>
							<div class="mb-3">
								<label for="recoveryReason" class="form-label">Explain why your account should be recovered:</label>
								<textarea class="form-control" id="recoveryReason" name="recovery_reason" rows="4" required></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Submit Request</button>
						</div>
					</form>
				</div>
			</div>
		</div>


		<!-- Second container (Registration) -->
		<div class="container2 box">
			<div class="box1 box33">
				<div class="message"></div>
				<h3 class="create-account">CREATE ACCOUNT</h3>
				<form action="" method="POST" id="signupForm">
    <div class="hide-this">
        <!-- Role Selection -->
        <div class="form-group">
            <label class="hidden-label">Are you a:</label>
            <div>
                <div class="radio-group">
                    <input type="radio" id="customer" name="role" value="customer" required>
                    <label for="customer">Customer</label>
                </div>
            </div>
            <div>
                <div class="radio-group">
                    <input type="radio" id="supplier" name="role" value="supplier" required>
                    <label for="supplier">Supplier</label>
                </div>
            </div>
        </div>

        <!-- Common Fields -->
        <div class="input-field">
            <input type="text" name="name" required>
            <label for="name">Enter your name</label>
        </div>

        <div class="input-field">
            <input type="email" name="email" id="email" required>
            <label for="email">Enter your email</label>
        </div>
		<div class="input-field">
			<input type="text" name="address" id="address" required>
			<label for="address">Enter your address</label>
		</div>
		<div class="input-field">
			<p class="mb-0">Enter your Birthday</p>
			<input type="date" name="birthday" placeholder="hi" required style="padding-left: 20px;">
		</div>

        <!-- Additional Fields for Supplier -->
        <div id="supplierFields" class="roleFields" style="display:none;">
            <div class="input-field">
                <input type="url" name="social_link">
                <label for="social_link">Enter your fb/ig link</label>
            </div>
			<div class="input-field d-flex justify-content-start">		
				<select name="profession" id="profession" style="background-color: #fff;">
					<option value="">Select Profession</option>
					<option value="photographer">Photographer</option>
					<option value="videographer">Videographer</option>
				</select>
				</div>

            <div class="input-field">
                <input type="number" name="years_in_profession">
                <label for="years_in_profession">Years in Profession</label>
            </div>
        </div>

        <!-- Password Fields -->
        <div class="d-flex gap-2">
    <div class="input-field">
        <input type="password" name="password" id="password" required pattern=".{8,}" title="Password must be at least 8 characters long and contain at least one special character">
        <label for="password">Enter your password</label>
    </div>

    <div class="input-field">
        <input type="password" name="confirm-password" id="confirm-password" required>
        <label for="confirm-password">Confirm your password</label>
    </div>



    <!-- Email verification input -->
    <div id="emailVerificationCode" style="display: none; margin-top: 50px;">
        <div class="input-field">
            <input type="text" name="verification_code" id="verification_code">
            <label for="verification_code">Enter the 4-digit code sent to your email</label>
        </div>
    </div>

    <!-- Buttons -->
    <button type="submit" class="loginbtn" id="submitBtn">Verify Email Address</button>
    <button type="submit" class="loginbtn" id="signUpBtn" style="display: none;">Submit Sign Up</button>
</form>

			</div>
			<div class="box2 box44 text-center">
				<h3>Welcome Back!</h3>
				<div class="lined"></div>
				<p>Already have an account?</p>
				<button class="loginbtn" id="loginBtn">LOG-IN</button>
			</div>
		</div>

		<script>
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let email = document.getElementById('email').value;
    let verificationCode = document.getElementById('verification_code').value;

    // Get the message div
    let messageDiv = document.querySelector('.message');

    // Remove any previous message classes
    messageDiv.classList.remove('btn', 'btn-success', 'btn-danger');

    if (!verificationCode) {
        // Send email verification and hide the form
        document.getElementById('submitBtn').disabled = false;
        
        fetch('', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Handle success or error
            messageDiv.textContent = data; // Show the message in the div

            // Determine success or failure and apply appropriate button class
            if (data.includes('successfully')) {
                messageDiv.classList.add('btn', 'btn-success');
				messageDiv.textContent = 'Registered Successfully! Click log in to continue'; // Success message
            } else {
                messageDiv.classList.add('btn', 'btn-danger'); // Error message
            }

            if (data.includes('Email sent successfully')) {
                // Hide the current form and show the verification code input
                document.getElementById('submitBtn').style.display = 'none';
                document.querySelector('.hide-this').style.display = 'none';
                document.getElementById('emailVerificationCode').style.display = 'block';
                document.getElementById('signUpBtn').style.display = 'block';
            }
        })
        .catch(error => console.log(error));
    } else if (verificationCode) {
        // Verify the code and proceed with sign-up
        fetch('', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Handle success or error
            messageDiv.textContent = data; // Show the message in the div

            // Determine success or failure and apply appropriate button class
            if (data.includes('successfully')) {
                messageDiv.classList.add('btn', 'btn-success'); // Success message
            } else {
                messageDiv.classList.add('btn', 'btn-danger'); // Error message
            }
        })
        .catch(error => console.log(error));
    }
});


</script>



		

	<script>
		document.getElementById('signupForm').addEventListener('submit', function(event) {
			var password = document.getElementById('password').value;
			var confirmPassword = document.getElementById('confirm-password').value;
			var errorDiv = document.getElementById('password-error');
			var specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;

			if (password !== confirmPassword) {
				errorDiv.textContent = "Passwords do not match!";
				errorDiv.style.display = 'block';
				event.preventDefault();
				return false;
			} else if (password.length < 8) {
				errorDiv.textContent = "Password must be at least 8 characters long!";
				errorDiv.style.display = 'block';
				event.preventDefault();
				return false;
			} else if (!specialCharPattern.test(password)) {
				errorDiv.textContent = "Password must contain at least one special character!";
				errorDiv.style.display = 'block';
				event.preventDefault();
				return false;
			} else {
				errorDiv.style.display = 'none';
			}
		});

		document.querySelectorAll('input[name="role"]').forEach(role => {
			role.addEventListener('change', function () {
				const isCustomer = this.value === 'customer';

				// Toggle supplier fields
				const supplierFields = document.getElementById('supplierFields');
				supplierFields.style.display = isCustomer ? 'none' : 'block';

				// Optionally, clear the input values for hidden fields
				supplierFields.querySelectorAll('input').forEach(input => {
					input.disabled = isCustomer;
					if (isCustomer) {
						input.value = ''; // Clear supplier fields when hiding
					}
				});
			});
		});

	</script>


	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
	<script src="../../function/script/login.js"></script>
</body>
</html>