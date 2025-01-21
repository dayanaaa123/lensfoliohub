<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'] ?? '';
$defaultProfileImg = 'profile.jpg'; 
$profileImg = $defaultProfileImg;
$profileImg = '' . $profileImg;

require '../../../../db/db.php';

$sql = "
  SELECT 
        a.*, 
        u.name AS user_name,
        u.name AS uploader_name 
    FROM 
        appointment AS a 
    LEFT JOIN 
        users AS u 
    ON 
        a.email_uploader = u.email 
    WHERE 
        a.email = ?
        AND a.status IN ('completed', 'cancelled')
";

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



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/logo.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/status.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
</head>

<style>
    .star {
        font-size: 30px;
        cursor: pointer;
        color: #ddd; /* Unselected star color */
    }

    .star.selected {
        color: gold; /* Selected star color */
    }
</style>
<body>
    <div id="preloader">
        <div class="line"></div>
        <div class="left"></div>
        <div class="right"></div>
    </div>

    <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-none d-md-block logo" href="../../../../index.php">
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

<div class="status">
    <div class="col-md-8 mx-auto">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="card mb-3 mt-4 d-flex justify-content-center mx-auto">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title fw-bold mb-0 d-flex align-items-center"> <?php echo htmlspecialchars($row['name']); ?></h5>
                            <div class="button d-flex gap-1 d-flex align-items-center ">
                            <p class="card-text">
                                <?php
                                $status = htmlspecialchars($row['status']);
                                $class = "";
                                if ($status === "Pending") {
                                    $class = "btn btn-primary";
                                } elseif ($status === "Accepted") {
                                    $class = "btn btn-success";
                                } elseif ($status === "Completed") {
                                    $class = "btn btn-success fw-bold";
                                } elseif ($status === "Cancelled") {
                                    $class = "btn btn-danger fw-bold";
                                } elseif ($status === "Decline") {
                                    $class = "btn btn-danger";
                                }
                                
                                ?>
                                <button class="<?php echo $class; ?> mt-3"><?php echo $status; ?></button>
                            </p>
                            <?php
                                if ($row['status'] == 'Pending') {
                                    echo '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal" style="height: 6vh;">Cancel</button>';
                                }
                            ?>
                            <?php
                                if ($row['status'] == 'Completed') {
                                    echo '<button class="btn btn-warning text-white btn-md" data-bs-toggle="modal" data-bs-target="#ratingModal">
                                            Rate Supplier
                                        </button>';
                                }
                            ?>
                            <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cancelModalLabel">Cancel Appointment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="../../function/php/cancel_appointment.php" method="POST" id="cancelForm">
                                        <div class="mb-3">
                                            <label for="cancelReason" class="form-label">Cancel Reason</label>
                                            <textarea class="form-control" id="cancelReason" name="cancelReason" rows="3" required></textarea>
                                        </div>
                                        <input type="hidden" id="appointmentId" name="appointmentId" value="<?php echo $row['id']; ?>"> <!-- Hidden input for appointment ID -->
                                        <button type="submit" class="btn btn-danger d-flex w-50 mx-auto">Submit</button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>

                            

                            <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="ratingModalLabel">Rate Supplier</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="../../function/php/submit_rating.php" method="POST" id="ratingForm">
                                                    <!-- Star Rating -->
                                                    <div id="starRating" class="d-flex justify-content-center">
                                                        <span class="star" data-value="1">&#9733;</span>
                                                        <span class="star" data-value="2">&#9733;</span>
                                                        <span class="star" data-value="3">&#9733;</span>
                                                        <span class="star" data-value="4">&#9733;</span>
                                                        <span class="star" data-value="5">&#9733;</span>
                                                    </div>
                                                    <input type="hidden" id="rating" name="rating" value="0"> <!-- Store selected rating -->

                                                    <!-- Optional Review -->
                                                    <div class="mt-3">
                                                        <label for="review" class="form-label">Review (Optional):</label>
                                                        <textarea class="form-control" id="review" name="review" rows="3"></textarea>
                                                    </div>
                                                    <!-- Hidden fields for emails -->
                                                    <input type="hidden" name="user_email" value="<?php echo $_SESSION['email']; ?>"> <!-- Logged-in user's email -->
                                                    <input type="hidden" name="supplier_email" id="supplier_email" value="<?php echo htmlspecialchars($row['email_uploader']); ?>"> <!-- Supplier's email -->
                                                    <button type="submit" class="btn btn-primary mt-3">Submit Rating</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          

                        </div>
                        <p class="card-text"> <?php echo htmlspecialchars($row['email']); ?></p>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <p class="card-text">Selected Date:</p>
                            <?php echo date("M j, Y", strtotime($row['selected_date'])); ?>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <p class="card-text">Supplier Name:</p>
                            <?php echo htmlspecialchars($row['uploader_name']); ?>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <p class="card-text">Supplier Email:</p>
                            <?php echo htmlspecialchars($row['email_uploader']); ?>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <p class="card-text">Location:</p>
                            <div id="map-<?php echo $row['id']; ?>" style="height: 300px; width: 40%; border-radius: 10px;"></div>
                            <script>
                                function initMap<?php echo $row['id']; ?>() {
                                    const location = { lat: <?php echo $row['latitude']; ?>, lng: <?php echo $row['longitude']; ?> };
                                    const map = new google.maps.Map(document.getElementById("map-<?php echo $row['id']; ?>"), {
                                        zoom: 15,
                                        center: location,
                                    });
                                    new google.maps.Marker({
                                        position: location,
                                        map: map,
                                    });
                                }
                                google.maps.event.addDomListener(window, "load", initMap<?php echo $row['id']; ?>);
                            </script>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <p class="card-text">Event:</p>
                            <?php echo htmlspecialchars($row['event']); ?>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <p class="card-text">Time:</p>
                            <?php echo htmlspecialchars($row['time']); ?>
                        </div>
                        <?php
                            if ($row['status'] == 'Cancelled') {
                                echo '<div class="d-flex justify-content-between mb-2">';
                                echo '<p class="card-text">Reason:</p>';
                                echo '<p>' . htmlspecialchars($row['cancel_reason']) . '</p>';
                                echo '</div>';
                            }
                        ?>
                        
                     
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='alert alert-info text-center'>No appointments found</div>";
        }
        ?>
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
