<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: authentication/web/api/login.php");
        exit();
    }
    require '../../../../db/db.php';
    $email = $_SESSION['email'];
    $role = $_SESSION['role']; 
    // Fetch reporter details from the session
    $reporterEmail = $_SESSION['email'];
    $reporterRole = $_SESSION['role'];

    // Fetch reporter name from the database
    $query = "SELECT name FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $reporterEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $reporterName = $result->fetch_assoc()['name'];

    // Fetch reported user details
    $uploaderEmail = htmlspecialchars($_POST['uploader_email']);
    $query = "SELECT name FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $uploaderEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $reportedName = $result->fetch_assoc()['name'];

    $profileImg = ''; 

if ($role != 'guest' && !empty($email)) {
    require '../../../../db/db.php';

    $stmt = $conn->prepare("SELECT profile_img FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profileImg);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    $profileImg = '' . $profileImg;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="assets/img/profile.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/supplier-profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
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
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../../../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about-us.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="snapfeed.php">Snapfeed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="supplier.php">Supplier</a>
                </li>
            </ul>

            <!-- Profile dropdown (right) -->
            <div class="d-flex ms-auto">
                <?php if ($role != 'guest') { ?>
                    <div class="dropdown">
                        <button class="btn btn-theme dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
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
<?php
require '../../../../db/db.php';

// Default values
$profileImg = ''; 
$about_me = '';
$profession = '';
$latitude = '';
$longitude = '';
$age = '';
$name = 'Unknown User';
$uploaderEmail = '';
$location_text = ''; 
$worksCount = 0; 

if (isset($_POST['uploader_email']) && !empty($_POST['uploader_email'])) {
    $uploaderEmail = htmlspecialchars($_POST['uploader_email']);
    
    echo '<script>
        // Store the uploaderEmail in localStorage so it persists across pages
        localStorage.setItem("uploader_email", "' . $uploaderEmail . '");
    </script>';
} else {
  
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var storedEmail = localStorage.getItem("uploader_email");
            if (storedEmail) {
                // Resubmit the form with the email from localStorage
                var form = document.createElement("form");
                form.method = "POST";
                form.action = window.location.href;

                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "uploader_email";
                input.value = storedEmail;

                form.appendChild(input);
                document.body.appendChild(form);

                form.submit();
            } else {
                alert("No uploader email found.");
            }
        });
    </script>';
    exit; 
}


if (!empty($uploaderEmail)) {

    $sql = "SELECT u.name, u.profile_img, a.about_me, a.profession, a.latitude, a.longitude, a.age, a.location_text, a.price
            FROM about_me a 
            JOIN users u ON a.email = u.email 
            WHERE a.email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("s", $uploaderEmail);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['name'];
        $profileImg = $user['profile_img'] ?  $user['profile_img'] : $profileImg;
        $about_me = $user['about_me'];
        $profession = $user['profession'];
        $latitude = $user['latitude'];
        $longitude = $user['longitude'];
        $age = $user['age'];
        $location_text = $user['location_text']; 
        $price = $user['price'];
    } else {
        echo "No data found for this user.";
    }


    $countSql = "SELECT COUNT(*) AS worksCount FROM snapfeed WHERE email = ?";
    $countStmt = $conn->prepare($countSql);
    if ($countStmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $countStmt->bind_param("s", $uploaderEmail);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    if ($countResult->num_rows > 0) {
        $countRow = $countResult->fetch_assoc();
        $worksCount = $countRow['worksCount'];
    } else {
        echo "No works found for this user.";
    }

    // Clean up
    $stmt->close();
    $countStmt->close();
} else {
    echo "No email provided.";
}

$conn->close();
?>




<section class="supplier-profile">
        <div class="container mt-5">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a href="#"><button class="nav-link about-me highlight">About <?php echo htmlspecialchars($name); ?></button></a>
                </li>
                <li class="nav-item">
                    <a href="projects.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link">Projects</button></a>
                </li>
                <li class="nav-item">
                    <a href="calendar.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link calendar">Calendar</button></a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link contacts">Contacts</button></a>
                </li>

            </ul>
        </div>





<div class="about-me-section">
<div class="container mt-5">
    <div class="row">
      <div class="col-lg-5 col-md-12 mb-4">
  <div class="card text-center text-white h-100 about-me-container position-relative">
    <div class="card-body d-flex flex-column">
      <div id="slide-1" class="slider-content active">
         <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-imgs mx-auto">
        <h5 class="text-center"><?php echo htmlspecialchars($name); ?></h5>
        <div class="about_mee" style="min-height: 42vh; overflow-y: auto;">
          <p class="text-start context"><?php echo nl2br(htmlspecialchars($about_me)); ?></p>
        </div> 
        <a href="#" class="btn about-me-button">Hire me</a>
        <button class="btn fw-bold" data-bs-toggle="modal" data-bs-target="#reportModal">...</button>
        <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="../../function/php/submit_report.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportModalLabel">Report User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="reportReason" class="form-label text-black fw-bold">Reason for Reporting</label>
                                <textarea class="form-control" id="reportReason" name="reason" rows="4" required></textarea>
                            </div>
                            <input type="hidden" name="reporter_name" value="<?php echo htmlspecialchars($reporterName); ?>">
                            <input type="hidden" name="reporter_email" value="<?php echo htmlspecialchars($reporterEmail); ?>">
                            <input type="hidden" name="reported_name" value="<?php echo htmlspecialchars($reportedName); ?>">
                            <input type="hidden" name="reported_email" value="<?php echo htmlspecialchars($uploaderEmail); ?>">
                            <input type="hidden" name="role" value="<?php echo htmlspecialchars($reporterRole); ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit Report</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>

      <div id="slide-2" class="slider-content">
        <p><strong>Location:</strong></p>
        <p><?php echo htmlspecialchars($location_text); ?></p>
        <div id="map" style="height: 300px; width: 100%;"></div>
      </div>

      <button id="prev-btn" class="btn btn-outline-primary position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); display: none;">
        &lt;
      </button>

      <button id="next-btn" class="btn btn-outline-primary position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">
        &gt;
      </button>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var currentSlide = 1;
    var totalSlides = 2; 

    var nextBtn = document.getElementById("next-btn");
    var prevBtn = document.getElementById("prev-btn");

    function updateButtons() {
      prevBtn.style.display = (currentSlide === 1) ? 'none' : 'block';
      nextBtn.style.display = (currentSlide === totalSlides) ? 'none' : 'block';
    }

    nextBtn.addEventListener("click", function() {
      document.getElementById('slide-' + currentSlide).classList.remove('active');
      
      currentSlide++;
      
      document.getElementById('slide-' + currentSlide).classList.add('active');
      updateButtons(); 
    });

    prevBtn.addEventListener("click", function() {

      document.getElementById('slide-' + currentSlide).classList.remove('active');
      

      currentSlide--;
      

      document.getElementById('slide-' + currentSlide).classList.add('active');
      updateButtons(); 
    });


    updateButtons();
  });
</script>

<style>

  .slider-content {
    display: none;
  }
  
  .slider-content.active {
    display: block;
  }
</style>




      <div class="col-lg-7 col-md-12">
        <div class="row h-100">

          <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
              <div class="card-body">
                <h5 class="card-title">Profession</h5>
                <p>  <?php 

                    $professionsArray = explode(',', $profession); 
                    echo !empty($professionsArray) ? implode(', ', array_map('htmlspecialchars', $professionsArray)) : 'None'; 
                    ?></p>
              </div>
            </div>
          </div>

          <!-- Small Card 2 -->
          <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
              <div class="card-body">
                <h5 class="card-title">Works</h5>
                <p class="card-text"><?php echo $worksCount; ?></p>
              </div>
            </div>
          </div>

          <!-- Small Card 3 -->
          <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
              <div class="card-body">
                <h5 class="card-title">Pricing</h5>
                <p class="card-text">₱<?php echo $price; ?> /hr</p>
              </div>
            </div>
          </div>

          <!-- Small Card 4 -->
          <div class="col-md-6 mb-4">
            <div class="card h-100 text-center">
              <div class="card-body">
                <h5 class="card-title">Age</h5>
                <p><strong></strong> <?php echo htmlspecialchars($age); ?> Year's Old</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    


         
    </section>

     
    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px;">
          <path fill="#FAF7F2" fill-opacity="1"
            d="M0,128L60,138.7C120,149,240,171,360,170.7C480,171,600,149,720,133.3C840,117,960,107,1080,112C1200,117,1320,139,1380,149.3L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
          </path>
        </svg>
      </div>


    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-md-4">
                    <h5>About Photography News</h5>
                    <p>Stay updated with the latest news, trends, and innovations in the world of photography. Whether you're a professional or an enthusiast, our articles are designed to inspire and inform.</p>
                </div>
    
                <!-- Quick Links -->
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Latest News</a></li>
                        <li><a href="#">Photography Tips</a></li>
                        <li><a href="#">Camera Reviews</a></li>
                    </ul>
                </div>
    
                <!-- Contact Section -->
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>Email: info@photographynews.com</p>
                    <p>Phone: +123 456 7890</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; 2024 Photography News. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    



    <script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places&callback=initMap" async defer>
</script>
    <script>
      function initMap() {
    var fixedLat = <?php echo htmlspecialchars($latitude); ?>;
    var fixedLng = <?php echo htmlspecialchars($longitude); ?>;

    var caviteBounds = {
        north: 14.48,
        south: 13.91,
        west: 120.70,
        east: 121.10
    };

    var map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: fixedLat, lng: fixedLng },
        zoom: 17,
        restriction: {
            latLngBounds: caviteBounds,
            strictBounds: false 
        },

        disableDefaultUI: true, 
        zoomControl: false,    
        draggable: false,      
        scrollwheel: false,   
        disableDoubleClickZoom: true, 
        gestureHandling: 'none'
    });

    var marker = new google.maps.Marker({
        position: { lat: fixedLat, lng: fixedLng },
        map: map
    });
}

window.onload = initMap;



</script>
</body>
</html>
