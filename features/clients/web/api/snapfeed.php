<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: ../../../../authentication/web/api/login.php");
        exit();
    }
    $email = $_SESSION['email'];
    $role = $_SESSION['role']; 

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

    $profileImg = '../../../../assets/img/profile/' . $profileImg;
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/logo.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/snapfeed.css">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    
</head>


<body>
    <div id="preloader">
        <div class="line"></div>
        <div class="left"></div>
        <div class="right"></div>
    </div>

    <nav class="navbar navbar-expand-lg ">
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

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../.././../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-us.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#snapfeed">Snapfeed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="supplier.php">Supplier</a>
                    </li>

                  
                </ul>
                <div class="d-flex ml-auto">
                    <?php if ($role != 'guest') { ?>
                        <div class="dropdown">
                            <button class="btn btn-theme dropdown-toggle" type=" button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="customer/profile.php">Main Profile</a></li>
                                            <li><a class="dropdown-item" href="status.php">Booking Status</a></li>
                                            <li><a class="dropdown-item" href="history.php">History</a></li>
                                            <li><a class="dropdown-item" href="notifications.php">Notifications</a></li>
                                    <li><a class="dropdown-item" href="../../../index/function/php/logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php } else { ?>
                        <!-- User is not logged in, display a login link -->
                        <a href="authentication/web/api/login.php" class="btn-theme" type="button">Login</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="text-box">
        POWER TO THE CREATORS
        
    </div> 
        <!-- Image Section -->
        <section class="gallery-img mt-5">
            <div class="row disp_img">
                <?php
                    include '../../function/php/display_snapfeed.php'; 
                ?>
            </div>
        </section>

        <script>
          document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="show-comments-"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.id.replace('show-comments-', '');
            document.getElementById('modal-main-title-' + id).style.display = 'none';
            document.getElementById('modal-main-text-' + id).style.display = 'none';
            this.style.display = 'none';
            document.getElementById('comments-section-' + id).style.display = 'block';
        });
    });

    document.querySelectorAll('[id^="show-text-"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.id.replace('show-text-', '');
            document.getElementById('modal-main-title-' + id).style.display = 'block';
            document.getElementById('modal-main-text-' + id).style.display = 'block';
            document.getElementById('show-comments-' + id).style.display = 'inline';
            document.getElementById('comments-section-' + id).style.display = 'none';
        });
    });
});

        </script>
    
    </div>
</div>



     
    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px; background-color: #F3E7DB;">
          <path fill="#FAF7F2" fill-opacity="1"
            d="M0,128L60,138.7C120,149,240,171,360,170.7C480,171,600,149,720,133.3C840,117,960,107,1080,112C1200,117,1320,139,1380,149.3L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
          </path>
        </svg>
      </div>
<footer class="footer mb-0">
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
    
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

<script type="text/javascript">
$(document).ready(function () {
    function copyToClipboard() {
        var aux = document.createElement("input");
        aux.setAttribute("value", "LENSFOLIOHUB COPYRIGHT PROTECTION.");
        document.body.appendChild(aux);
        aux.focus(); 
        aux.select();
        document.execCommand("copy"); 
        document.body.removeChild(aux);
        console.log("Text copied to clipboard.");
    }

    $(document).on('keyup', function (e) {
        if (e.keyCode === 44) { 
            e.preventDefault();
            copyToClipboard();
        }
    });

 
});
</script>

<script>
    document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        document.body.classList.add('screenshot-blocked');
    } else {
        document.body.classList.remove('screenshot-blocked');
    }
});

window.addEventListener('devicemotion', (event) => {
    if (event.accelerationIncludingGravity) {
        document.body.classList.add('screenshot-blocked');
    }
});
</script>







</html>
