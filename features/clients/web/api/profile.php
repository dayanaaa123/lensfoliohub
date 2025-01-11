<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}
include '../../../../db/db.php';

$email = $_SESSION['email'];
$role = $_SESSION['role'] ?? '';

$query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profileImg = $row['profile_img'];
    $name = $row['name'];
    $address = $row['location'];
    $birthday = $row['birthday'];
    $social_link = $row['social_link'];
} else {
    die('No user found.');
}

$stmt->close();
$conn->close();



?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                        <a class="nav-link" href="#">Profile</a>
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


    <section class="supplier-profile">
        


<div class="about-me-section">
    <div class="container mt-5 about-section">
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <div class="cardss">
                <form enctype="multipart/form-data" method="POST" action="../../function/php/profile.php" class="about-mes">
                    <div class="mb-3 text-center">
                        <!-- Display profile image -->
                        <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-imgs">
                        <h5><?php echo htmlspecialchars($name); ?></h5>
                        <input class="form-control mt-3" type="file" name="profile_img" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <!-- Name input -->
                        <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="mb-3">
                        <!-- Address input -->
                        <input type="text" class="form-control" name="address" placeholder="Address" value="<?php echo htmlspecialchars($address); ?>">
                    </div>
                    <div class="mb-3">
                        <!-- Birthday input -->
                        <input type="date" class="form-control" name="birthday" value="<?php echo htmlspecialchars($birthday); ?>">
                    </div>
                    <div class="mb-3">
                        <!-- New password input -->
                        <input type="password" class="form-control" name="password" placeholder="New Password">
                    </div>
                    <div class="mb-3">
                        <!-- Confirm password input -->
                        <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password">
                    </div>
                    <!-- Submit button -->
                    <button type="submit" class="btn about-me-button d-flex justify-content-center mx-auto">Save</button>
                </form>
                <hr>
                <button type="button" class="btn btn-danger d-flex w-50 mt-2 d-flex justify-content-center mx-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    Delete Account
                </button>

                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Confirm Account Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this account? You won't recover and all your images will be deleted.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form method="POST" action="../../function/php/delete.php">
                                <input type="hidden" name="delete_account" value="1">
                                <button type="submit" class="btn btn-danger">Confirm</button>
                            </form>
                        </div>
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
