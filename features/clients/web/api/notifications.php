<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'] ?? '';



if (!isset($_SESSION['email'])) {
    die('No session email found.');
}

$email = $_SESSION['email'];

require '../../../../db/db.php';

if ($role != 'guest' && !empty($email)) {
    // Fetch profile image
    $stmt = $conn->prepare("SELECT profile_img FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profileImg);
    $stmt->fetch();
    $stmt->close();

    $profileImg = '../../../../assets/img/profile/' . $profileImg;
}

// Fetch notifications for the logged-in user from the database
$sql = "SELECT * FROM notification WHERE email = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC); // Fetch all notifications as an associative array

$stmt->close();
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/profile.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/status.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
</head>

<body>
    <div id="preloader">
        <div class="line"></div>
        <div class="left"></div>
        <div class="right"></div>
    </div>

    <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-none d-md-block logo" href="#">
            LENSFOLIOHUB
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                style="stroke: black; fill: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Links (left) -->
            <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../.././../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="about-us.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Snapfeed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-me.php">Profile</a>
                    </li>
                </ul>

            <!-- Profile dropdown (right) -->
            <div class="d-flex ms-auto">
                <?php if ($role != 'guest') { ?>
                    <div class="dropdown">
                        <button class="btn btn-theme dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="profile.php">Main Profile</a></li>
                                    <li><a class="dropdown-item" href="status.php">Booking Status</a></li>
                                    <li><a class="dropdown-item" href="history.php">History</a></li>
                                    <li><a class="dropdown-item" href="notifications.php">Notifications</a></li>
                            <li><a class="dropdown-item" href="../../../index/function/php/logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <!-- User is not logged in, display a login link -->
                    <a href="authentication/web/api/login.php" class="btn btn-theme" type="button">Login</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>
  
   <div class="history">
            <div class="container">
            <div class="row d-flex justify-content-center mt-4">
                    <div class="card col-md-5">
                        <div class="card-body" style="height: 70vh; overflow-y: auto;">
                        <?php if (!empty($notifications)): ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <?php
                                    switch ($notification['status']) {
                                        case 'pending':
                                            $btnClass = 'btn-info';
                                            $iconClass = 'bi-info-circle';
                                            break;
                                        case 'update':
                                            $btnClass = 'btn-warning';
                                            $iconClass = 'bi-exclamation-triangle';
                                            break;
                                        case 'cancelled':
                                        case 'declined':
                                            $btnClass = 'btn-danger';
                                            $iconClass = 'bi-x-circle';
                                            break;
                                        case 'Accepted':
                                        case 'Completed':
                                            $btnClass = 'btn-success';
                                            $iconClass = 'bi-check-circle';
                                            break;
                                        default:
                                            $btnClass = 'btn-secondary'; // Fallback
                                            $iconClass = 'bi-question-circle';
                                    }
                                    ?>
                                    <!-- Notification Item -->
                                    <div class="d-flex gap-3 justify-content-center">
                                        <div class="w-100 btn <?php echo $btnClass; ?> d-flex gap-3 justify-content-center mb-2">
                                            <i class="bi <?php echo $iconClass; ?> text-white"></i>
                                            <p class="mb-0 d-flex align-items-center text-white"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No notifications available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
   </div>




    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px;">
          <path fill="#FAF7F2" fill-opacity="1"
            d="M0,128L60,138.7C120,149,240,171,360,170.7C480,171,600,149,720,133.3C840,117,960,107,1080,112C1200,117,1320,139,1380,149.3L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
          </path>
        </svg>
      </div>

      <script>
    document.addEventListener("DOMContentLoaded", function() {
        const stars = document.querySelectorAll('#starRating .star');
        const ratingInput = document.getElementById('rating');
        const submitButton = document.getElementById('submitRating');
        const reviewInput = document.getElementById('review');

        // Handle Star Rating Selection
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const ratingValue = this.getAttribute('data-value');
                ratingInput.value = ratingValue;
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= ratingValue) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
        });
    });
</script>

   

    

    <script src="../../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    



</body>
</html>
