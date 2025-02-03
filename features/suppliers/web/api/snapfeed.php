<?php
    session_start();
    if (!isset($_SESSION['email'])) {
        header("Location: authentication/web/api/login.php");
        exit();
    }
    $email = $_SESSION['email'];
    $role = $_SESSION['role']; 

    $profileImg = ''; 

    if ($role != 'guest' && !empty($email)) {
        require '../../../../db/db.php';
    
        $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($profileImg);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
    
    }
    

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

</head>

<style>
    .success-box {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: none; /* Hidden by default */
    animation: fadeInOut 4s ease-in-out;
}

/* Fade in and out animation */
@keyframes fadeInOut {
    0% { opacity: 0; transform: translateY(10px); }
    10% { opacity: 1; transform: translateY(0); }
    90% { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(10px); }
}
      
        
</style>
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
                        <a class="nav-link" href="snapfeed.php">Snapfeed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="supplier.php">Supplier</a>
                    </li>
                </ul>
                <div class="d-flex ml-auto">
                    <?php if ($role != 'guest') { ?>
                        <div class="dropdown">
                            <button class="btn btn-theme dropdown-toggle" type=" button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="about-me.php">Main Profile</a></li>
                        <li><a class="dropdown-item" href="acc-status.php">Account Status</a></li>
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
    <div class="container">
        <!-- Card Section -->
        <div class="row">
            <div class="col-md-8 mb-3 justify-content-center align-items-center mx-auto">
            <form action="../../function/php/snapfeed.php" method="POST" enctype="multipart/form-data">
    <div class="d-flex gap-4 align-items-cente">
    <div class="card">
        <div class="row no-gutters">
            <!-- Left Section: Image -->
            <div class="col-md-6">
                <!-- This is where the chosen image will be previewed -->
                <img id="preview-img" src="../../../../assets/img/snapfeed/gallery-1.jpg" class="card-img show-input" alt="Selected Image">
            </div>
            <!-- Right Section: Profile Picture, Name, and Description -->
            <div class="col-md-6 d-flex">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 section-name">
                        <!-- Image for profile picture -->
                        <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img rounded-circle">
                        
                        <!-- Use the session variable for the card title -->
                        <h5 class="card-title mb-0">
                            <?php 
                                echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown User'; 
                            ?>
                        </h5>
                    </div>

                    <input type="file" id="card_img" name="card_img" accept="image/*,video/*" required onchange="previewCardImage(event)" class="file-input mt-2">
                    <input type="text" id="img_title" name="img_title" class="form-control mt-2" required placeholder="Image Title">
                    <textarea id="card_text" name="card_text" rows="4" class="mt-3" required placeholder="Input Photo Caption"></textarea>
                    <input type="submit" value="Submit" class="submit-project mt-2">
                </div>
            </div>
        </div>
    </div>

    <a href="snapfeeds.php" class="text-decoration-none d-flex align-items-center btn btn-success" style="height: 10vh;">Go to feeds</a>
</div>
</form>

<div id="success-message" class="success-box">
    <p>Post uploaded successfully!</p>
</div>

<script>
// Show success message if URL contains 'success=1'
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        document.getElementById('success-message').style.display = 'block';
        setTimeout(() => {
            document.getElementById('success-message').style.display = 'none';
        }, 4000); // Hide after 4 seconds
    }
};
</script>

<!-- JavaScript for Image Preview -->
<script>
function previewCardImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview-img');
        output.src = reader.result;  // Set the selected image as the source of the <img> tag
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

            </div>
        </div> 
</div>
    
        <!-- Image Section -->
        <section class="gallery-img mt-5">
            <div class="row disp_img">
                <?php
                    include '../../function/php/display_snapfeed.php'; 
                ?>
            </div>
        </section>
    
    </div>
</div>
     
    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px; background-color: #F3E7DB;">
          <path fill="#FAF7F2" fill-opacity="1"
            d="M0,128L60,138.7C120,149,240,171,360,170.7C480,171,600,149,720,133.3C840,117,960,107,1080,112C1200,117,1320,139,1380,149.3L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
          </path>
        </svg>
      </div>



    

    
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>

</body>

</html>
