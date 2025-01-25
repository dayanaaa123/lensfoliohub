<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php"); // Redirect to login if not authenticated
    exit();
}

// Get email and role from session
$email = $_SESSION['email'];
$role = $_SESSION['role']; 

// Default values
$uploaderEmail = ''; 
$profileImg = ''; 
$name = 'Unknown User'; // Default name

// Only fetch the uploader's profile image and name if the user is not a guest and email is available
if ($role != 'guest' && !empty($email)) {
    require '../../../../db/db.php';  // Include database connection

    // Check if the `uploader_email` is provided via POST or GET (depending on your project structure)
    if (isset($_POST['uploader_email'])) {
        $uploaderEmail = $_POST['uploader_email'];
    } elseif (isset($_GET['uploader_email'])) {
        $uploaderEmail = $_GET['uploader_email'];
    }

    // Ensure the email is not empty
    if (!empty($uploaderEmail)) {
        // Prepare and execute SQL query to fetch uploader's profile image and name
        $stmt = $conn->prepare("SELECT u.name, u.profile_img FROM users u WHERE u.email = ?");
        $stmt->bind_param("s", $uploaderEmail);
        $stmt->execute();
        $stmt->bind_result($name, $profileImg);
        $stmt->fetch();


        // Set the profile image path
        $profileImg = !empty($profileImg) ? '../../../../assets/img/profile/' . $profileImg : 'path/to/default-image.jpg';
    } else {
    }
}

require '../../../../db/db.php';

$sql = "SELECT template, profile_image, gallery_name FROM template1 WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uploaderEmail);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/logo.jpg" type="image/png">
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
                    <li class="nav-item">
                    <a class="nav-link" href="supplier.php">Supplier</a>
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


    <section class="supplier-profile">
        <div class="container mt-5">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a href="about-me.php"><button class="nav-link about-me">About Me</button></a>
                </li>
                <li class="nav-item">
                    <a href="projects.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link highlight">Projects</button></a>
                </li>
                <li class="nav-item">
                    <a href="calendar.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link calendar">Calendar</button></a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link contacts">Contacts</button></a>
                </li>
            </ul>
        </div>

        <?php
require '../../../../db/db.php';

// Initialize variables
$uploaderEmail = ''; 
$snapfeedImages = [];
$viewType = ''; // No default view type, will be fetched from the database

// Step 1: Check if email_uploader is provided via POST or localStorage
if (isset($_POST['uploader_email']) && !empty($_POST['uploader_email'])) {
    $uploaderEmail = htmlspecialchars($_POST['uploader_email']);
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
?>

<div id="grid-layout" class="projects">
    <div class="row g-3">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-3">
                <div class="card">
                    <img src="../../../../assets/img/template/<?= $row['profile_image'] ?>" 
                         class="img-fluid img-thumbnail w-100" style="height: 30vh; cursor: pointer;" 
                         alt="Gallery Image" 
                         data-bs-toggle="modal" 
                         data-bs-target="#galleryModal<?= $row['gallery_name'] ?>">
                </div>
            </div>

            <!-- Modal for Gallery -->
            <div class="modal fade" id="galleryModal<?= $row['gallery_name'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $row['gallery_name'] ?> Gallery</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['view_type'])) {
                            // Handle the POST request to update the view type
                            $viewType = $_POST['view_type']; // Get the selected view type from POST
                            $updateQuery = "UPDATE template1 SET template = ? WHERE email = ?"; 
                            $stmtUpdate = $conn->prepare($updateQuery);
                            $stmtUpdate->bind_param("ss", $viewType, $email);
                            $stmtUpdate->execute();
                            $stmtUpdate->close();

                            // Set current view directly from POST
                            $currentView = $viewType;
                        } 

                        // Fetch the current template value from the database regardless
                        $fetchQuery = "SELECT template FROM template1 WHERE email = ?";
                        $stmtFetch = $conn->prepare($fetchQuery);
                        $stmtFetch->bind_param("s", $uploaderEmail);
                        $stmtFetch->execute();
                        $resultFetch = $stmtFetch->get_result();

                        // Directly set the current view based on database value (assumes it only contains valid values)
                        $rowFetch = $resultFetch->fetch_assoc();
                        $currentView = $rowFetch['template']; 

                        $stmtFetch->close();
                        ?>


                            <!-- Gallery Content -->
                            <div class="row g-3">
                                <?php 
                                $galleryQuery = "SELECT image_name FROM gallery_images WHERE gallery_name = ?";
                                $stmtGallery = $conn->prepare($galleryQuery);
                                $stmtGallery->bind_param("s", $row['gallery_name']);
                                $stmtGallery->execute();
                                $galleryResult = $stmtGallery->get_result();

                                if ($currentView === 'grid'): ?>
                                    <!-- Grid View -->
                                    <div class="grid">
                                        <div class="row">
                                            <?php while ($galleryRow = $galleryResult->fetch_assoc()): ?>
                                                <div class="col-md-4">
                                                    <img src="../../../../assets/img/gallery/<?= $galleryRow['image_name'] ?>" 
                                                         class="img-fluid img-thumbnail w-100" 
                                                         alt="Gallery Image" style="height: 30vh;">
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                <?php elseif ($currentView === 'carousel'): ?>
                                    <!-- Carousel View -->
                                    <div id="galleryCarousel<?= $row['gallery_name'] ?>" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php 
                                            $isActive = true; 
                                            while ($galleryRow = $galleryResult->fetch_assoc()): ?>
                                                <div class="carousel-item <?= $isActive ? 'active' : '' ?>">
                                                    <img src="../../../../assets/img/gallery/<?= $galleryRow['image_name'] ?>" 
                                                         class="d-block w-100" 
                                                         alt="Gallery Image" style="height: 60vh;">
                                                </div>
                                                <?php $isActive = false; ?>
                                            <?php endwhile; ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel<?= $row['gallery_name'] ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel<?= $row['gallery_name'] ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php $stmtGallery->close(); ?>
                            </div>

                           

                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>




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
    


</body>
</html>
