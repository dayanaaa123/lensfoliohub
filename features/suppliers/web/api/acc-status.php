<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];  // Session email
$role = $_SESSION['role'];   // User role

// Database connection
require '../../../../db/db.php';

// Query to get the reasons for reports where reported_email matches the session email
$query = "SELECT reason FROM reports WHERE reported_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);  // Use $email instead of $userEmail
$stmt->execute();
$result = $stmt->get_result();

// Initialize the status message
$statusMessage = 'Good! Keep it up!'; // Default status if no reports
$reasons = []; // Array to store reasons

// Check the number of reports and collect reasons
$reportCount = 0;
while ($row = $result->fetch_assoc()) {
    $reportCount++;
    $reasons[] = $row['reason']; // Store each reason in the reasons array
}

// Set the account status based on the report count
if ($reportCount == 1) {
    $statusMessage = "You have 1 report.";
} elseif ($reportCount == 2) {
    $statusMessage = "You have 2 reports.";
} elseif ($reportCount == 4) {
    $statusMessage = "You have 4 reports.";
} elseif ($reportCount > 4) {
    $statusMessage = "You have multiple reports.";
}

// Fetch profile image if role is not 'guest' and email is valid
if ($role != 'guest' && !empty($email)) {
    // Query to get the profile image
    $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profileImg);
    $stmt->fetch();
    $stmt->close();
}

// Close the database connection
$conn->close();
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</head>

<style>
    body{
        background-color: #FBF9FA !important;
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
                        <li><a class="dropdown-item" href="acc-status.php">Account Status</a></li>
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
         

        <?php
include '../../../../db/db.php';

if (!isset($_SESSION['email'])) {
    die('Email not found in session.');
}

$email = $_SESSION['email'];

$profile_img = ''; 
$profession = '';
$about_me = '';
$age = '';
$latitude = '';
$longitude = '';
$price = '';
$name = ''; 
$portfolio = '';

// Fetch profile_img from users and other details from about_me
$stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('s', $email);

if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$stmt->bind_result($profile_img);
$stmt->fetch();
$stmt->close();

// Fetch remaining details from about_me
$stmt = $conn->prepare("SELECT name, profession, about_me, age, latitude, longitude, price, portfolio FROM about_me WHERE email = ?");
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('s', $email);

if (!$stmt->execute()) {
    die('Execute failed: ' . $stmt->error);
}

$stmt->bind_result($name, $profession, $about_me, $age, $latitude, $longitude, $price, $portfolio);

if (!$stmt->fetch()) {
    // Set defaults if no record is found in about_me
    $name = ''; 
    $profession = '';
    $about_me = '';
    $age = '';
    $latitude = '';
    $longitude = '';
    $price = '';
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Set default profile_img if not found
if (empty($profile_img)) {
    $profile_img = 'profile.jpg'; 
}
?>



<!-- HTML Code -->
<div class="about-me-section">
    <div class="container mt-5 about-section">
        <div class="col-md-6 d-flex flex-column justify-content-center card-about">
            <h3>Account Status</h3>
            <p class="btn btn-danger"><?php echo htmlspecialchars($statusMessage); ?></p>

            <!-- If there are reports, show the reasons -->
            <?php if ($reportCount > 0): ?>
                <div class="card mt-3">
                    <div class="card-header text-danger fw-bold">
                        Reasons for Reports:
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($reasons as $reason): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($reason); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


         
    </section>

     



    <script src="../../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    
    <script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places&callback=initMap" async defer>
</script>
    <script>
    function initMap() {
    // Cavite, Philippines bounds
    var caviteBounds = {
        north: 14.48,
        south: 13.91,
        west: 120.70,
        east: 121.10
    };

    // Center the map on Cavite
    var caviteCenter = { lat: 14.2710, lng: 120.9050 };

    // Get latitude and longitude from hidden inputs
    var initialLat = parseFloat(document.getElementById('latitude').value) || caviteCenter.lat;
    var initialLng = parseFloat(document.getElementById('longitude').value) || caviteCenter.lng;

    // Initialize the map
    var map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: initialLat, lng: initialLng },
        zoom: 17,
        restriction: {
            latLngBounds: caviteBounds,
            strictBounds: false
        }
    });

    // Initialize the marker
    var marker = new google.maps.Marker({
        position: { lat: initialLat, lng: initialLng },
        map: map,
        draggable: true // Make the marker draggable
    });

    // Initialize the Places Autocomplete service
    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'), {
        bounds: caviteBounds,
        componentRestrictions: { country: 'ph' }
    });

    // When a user selects a place from the autocomplete suggestions
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        // Check if the selected place is within the bounds of Cavite
        if (place.geometry.location.lat() < caviteBounds.south ||
            place.geometry.location.lat() > caviteBounds.north ||
            place.geometry.location.lng() < caviteBounds.west ||
            place.geometry.location.lng() > caviteBounds.east) {
            alert("Please select a location within Cavite, Philippines.");
            return;
        }

        // Set the map's center to the selected place
        map.setCenter(place.geometry.location);
        map.setZoom(17);

        // Move the marker to the selected place
        marker.setPosition(place.geometry.location);

        // Update the hidden latitude and longitude fields
        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();
    });

    // Update latitude and longitude when the marker is dragged
    marker.addListener('dragend', function(event) {
        var lat = event.latLng.lat();
        var lng = event.latLng.lng();

        // Check if the marker is within the Cavite bounds
        if (lat < caviteBounds.south || lat > caviteBounds.north ||
            lng < caviteBounds.west || lng > caviteBounds.east) {
            alert("Please place the marker within Cavite, Philippines.");
            marker.setPosition({ lat: initialLat, lng: initialLng }); // Reset to the initial position
            map.setCenter({ lat: initialLat, lng: initialLng });
        } else {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }
    });
}

// Initialize the map when the window loads
window.onload = initMap;
</script>
</body>
</html>
