<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: authentication/web/api/login.php");
        exit();
    }
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];

    require '../../../../db/db.php';

    // Fetch profile image
    $sql = "SELECT profile_img FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profileImg);
    $stmt->fetch();
    $stmt->close();

    $profileImg = $profileImg ?: 'default.png';

    // Query to count users
    $userCountQuery = "SELECT COUNT(*) AS user_count FROM users";
    $userCountResult = $conn->query($userCountQuery);
    $userCount = $userCountResult->fetch_assoc()['user_count'];

    // Query to count snapfeed entries
    $snapfeedCountQuery = "SELECT COUNT(*) AS snapfeed_count FROM snapfeed";
    $snapfeedCountResult = $conn->query($snapfeedCountQuery);
    $snapfeedCount = $snapfeedCountResult->fetch_assoc()['snapfeed_count'];

    // Close the connection after all queries
    $conn->close();
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
    <link rel="stylesheet" href="../../css/about-us.css">

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
            <ul class="navbar-nav me-auto d-flex justify-content-end w-100">
                <li class="nav-item">
                    <a class="nav-link" href="../../../../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">About</a>
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
                        <li><a class="dropdown-item" href="about-me.php">Main Profile</a></li>
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

    <section class="about_us mt-5">
        <div class="head">
            <div class="lines"></div>
            <h2 class="headings">ABOUT US</h2>
            <div class="lines"></div>
        </div>
        <div class="about_us mt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-10 col-sm-12">
                        <div class="aboutus-body">
                            <p class="text-justify indent">
                                <b>LENSFOLIOHUB</b>, is a platform designed to connect talented photographers with clients in need of high-quality visual storytelling. We make it easy for photographers to showcase their work and grow their business, while offering clients a streamlined experience to find the perfect photographer for their project. Whether you're looking for portraits, events, or commercial photography, LENSFOLIOHUB ensures a seamless connection between creative professionals and those seeking their expertise.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="who_we_are mt-5">
            <div class="container">
                <div class="row align-items-center justify-content-center gap-2">
                    <div class="col-lg-4 col-md-6">
                        <h2 class="who-title">WHO WE ARE</h2>
                        <p class="who-text text-justify indent">
                            We are I.T students from CvSU - CCAT, driven by our passion for innovation and creativity in the digital world. At <b>LENSFOLIOHUB</b>, our mission is to bridge the gap between photographers and clients, offering a platform where talented professionals can connect with those in need of high-quality photography.
                        </p>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="placeholder-image">
                            <img src="../../../../assets/img/authen-bg.jpg" alt="Placeholder Image" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="stats-section mt-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-4 col-md-4 col-sm-12 circle-mid">
                    <div class="stat-item">
                        <i class="fas fa-users stat-icon"></i>
                        <p class="stat-text"><span><?php echo $userCount; ?></span> Users</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 circle-mid">
                    <div class="stat-item">
                        <i class="fas fa-camera stat-icon"></i>
                        <p class="stat-text"><span><?php echo $snapfeedCount; ?></span> Captures</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 circle-mid">
                    <div class="stat-item">
                        <i class="fas fa-calendar-alt stat-icon"></i>
                        <p class="stat-text"><span>2 Months</span> Experience</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="info-section mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="vision-box">
                        <h3 class="section-title">VISION</h3>
                        <p class="text-justify indent">At <b>LENSFOLIOHUB</b>, our vision is to become the leading platform where creativity meets opportunity, enabling photographers and clients to seamlessly connect. We aim to revolutionize the photography industry by providing a space where talent is recognized, and quality photography is accessible to everyone.</p>
                    </div>
                </div>
                <!-- Team Section -->
                <div class="col-lg-6 col-md-12">
                    <div class="team-box text-center mx-auto">
                        <h3 class="section-title text-start">OUR TEAM</h3>
                        <div class="row justify-content-center">
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3 d-flex justify-content-center">
                                <img src="../../../../assets/img/our-team/team1.jpg" alt="Team Member" class="team-img img-fluid">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3 d-flex justify-content-center">
                                <img src="../../../../assets/img/our-team/team1.jpg" alt="Team Member" class="team-img img-fluid">
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 mb-3 d-flex justify-content-center">
                                <img src="../../../../assets/img/our-team/team1.jpg" alt="Team Member" class="team-img img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row mt-4">
                <!-- Contact Us Section -->
                <div class="col-lg-6 col-md-12">
                    <div class="contact-box">
                        <h3 class="section-title">CONTACT US</h3>
                        <p><i class="fas fa-phone-alt"></i> Phone: 123-456-7890</p>
                        <p><i class="fas fa-envelope"></i> Email: info@lensfoliohub.com</p>
                    </div>
                </div>
                <!-- Mission Section -->
                <div class="col-lg-6 col-md-12">
                    <div class="mission-box">
                        <h3 class="section-title">MISSION</h3>
                        <p class="text-justify indent">Our mission is to empower photographers by providing them with the tools and exposure needed to grow their careers, while offering clients a simple and efficient way to find the perfect photographer for any project. We strive to foster meaningful connections and create opportunities that enable professional and creative success in the world of photography.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
     
    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px;">
          <path fill="#a67b5b" fill-opacity="1"
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

    
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>

</body>
</html>
