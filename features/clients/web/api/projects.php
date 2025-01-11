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
        $stmt->close();
        $conn->close();

        // Set the profile image path
        $profileImg = !empty($profileImg) ? '../../../../assets/img/profile/' . $profileImg : 'path/to/default-image.jpg';
    } else {
    }
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


        <?php
require '../../../../db/db.php';

// Initialize variables
$uploaderEmail = ''; 
$snapfeedImages = [];
$viewType = 'grid'; // Default view type

// Step 1: Check if email is provided via POST or localStorage
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

if (!empty($uploaderEmail)) {
    $sql_about_me = "SELECT view_type FROM about_me WHERE email = ?";
    $stmt_about_me = $conn->prepare($sql_about_me);
    if ($stmt_about_me === false) {
        die("SQL Error: " . $conn->error);
    }
    $stmt_about_me->bind_param("s", $uploaderEmail);
    $stmt_about_me->execute();
    $result_about_me = $stmt_about_me->get_result();

    if ($result_about_me->num_rows > 0) {
        $about_me_data = $result_about_me->fetch_assoc();
        $viewType = $about_me_data['view_type']; 
    } else {
        echo "No view type found for this user.";
    }

    $stmt_about_me->close();

    // Fetch images from snapfeed
    $sql_snapfeed = "SELECT card_img FROM snapfeed WHERE email = ?";
    $stmt_snapfeed = $conn->prepare($sql_snapfeed);
    if ($stmt_snapfeed === false) {
        die("SQL Error: " . $conn->error);
    }

    $stmt_snapfeed->bind_param("s", $uploaderEmail);
    $stmt_snapfeed->execute();
    $result_snapfeed = $stmt_snapfeed->get_result();

    // Fetch all image paths
    while ($row = $result_snapfeed->fetch_assoc()) {
        $snapfeedImages[] = $row['card_img'];
    }

    $stmt_snapfeed->close();
} else {
    echo "No email provided.";
}

$conn->close();
?>

<div class="container mt-5">
    <?php if ($viewType === 'grid') : ?>
        <div id="grid-layout" class="projects" style="display: block;">
            <div class="row">
                <?php
                if (!empty($snapfeedImages)) {
                    foreach ($snapfeedImages as $image) {
                        echo '<div class="col-md-3 mb-3">';
                        echo '<img src="../../../../assets/img/snapfeed/' . htmlspecialchars($image) . '" class="img-fluid img-wh" alt="Image">';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No images found for this user.</p>';
                }
                ?>
            </div>
        </div>
    <?php elseif ($viewType === 'carousel') : ?>
        <div id="carousel-layout" class="carousel-container" style="display: block;">
            <div class="carousel-slider">
                <?php
                if (!empty($snapfeedImages)) {
                    foreach ($snapfeedImages as $index => $image) {
                        echo '<img id="img' . $index . '" src="../../../../assets/img/snapfeed/' . htmlspecialchars($image) . '" alt="Image" class="carousel-img">';
                    }
                } else {
                    echo '<p>No images found for this user.</p>';
                }
                ?>
            </div>
            <div class="buttons">
                <button class="prev" onclick="plusSlides(-1)">&#10094;</button>
                <button class="next" onclick="plusSlides(1)">&#10095;</button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let slideIndex = 1;

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function showSlides(n) {
    let slides = document.getElementsByClassName("carousel-img");
    let totalSlides = slides.length;

    if (n > totalSlides - 2) { slideIndex = totalSlides - 2; }
    if (n < 1) { slideIndex = 1; }

    for (let i = 0; i < totalSlides; i++) {
        slides[i].style.display = "none";
        slides[i].classList.remove("middle", "side");
    }

    for (let i = slideIndex - 1; i < slideIndex + 2; i++) {
        if (slides[i]) {
            slides[i].style.display = "block";
        }
    }


    let middleIndex = slideIndex;

    // Set the middle image
    if (slides[middleIndex]) {
        slides[middleIndex].classList.add("middle");
    }

    // Set the side images
    if (slides[middleIndex - 1]) {
        slides[middleIndex - 1].classList.add("side");
    }
    if (slides[middleIndex + 1]) {
        slides[middleIndex + 1].classList.add("side");
    }
}


// Initialize the carousel to show the first set of images
showSlides(slideIndex);
</script>



    </section>

    <script>
        
    </script>
     
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
