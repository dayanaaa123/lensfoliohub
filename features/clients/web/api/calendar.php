<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role'];
// Retrieve the email_uploader from the URL or POST request
$emailUploader = '';
if (isset($_GET['email_uploader'])) {
    $emailUploader = urldecode($_GET['email_uploader']);
} elseif (isset($_POST['email_uploader'])) {
    $emailUploader = htmlspecialchars($_POST['email_uploader']);
}

if ($emailUploader) {

    require '../../../../db/db.php';

    // Initialize an array for time options
    $timeOptions = [];

    // Query to get available hours from about_me table
    $stmt = $conn->prepare("SELECT avail_hrs FROM about_me WHERE email = ?");
    $stmt->bind_param("s", $emailUploader);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // If data exists, process the avail_hrs field
        $availHrs = $row['avail_hrs'];
        $availHrsArray = explode(',', $availHrs);

        // Sort the available hours array to ensure it is in the correct order (12 AM to 11 PM)
        sort($availHrsArray);

        // Create options for the time select dropdown
        foreach ($availHrsArray as $hour) {
            $hour = (int)$hour;
            // Format the time as 12 AM, 1 AM, ..., 11 PM
            $formattedTime = date("g a", strtotime("$hour:00"));
            $timeOptions[] = "<option value='$hour'>$formattedTime</option>";
        }
    }
    $stmt->close();

    // Fetch the time from the appointment table for the given email_uploader
    $appointmentStmt = $conn->prepare("SELECT time FROM appointment WHERE email_uploader = ?");
    $appointmentStmt->bind_param("s", $emailUploader);
    $appointmentStmt->execute();
    $appointmentResult = $appointmentStmt->get_result();

    if ($appointmentRow = $appointmentResult->fetch_assoc()) {
        // Get the time from the appointment table
        $time = (int)$appointmentRow['time']; // Assuming 'time' is stored as an integer (e.g., 3, 14)

        // Remove the selected time from avail_hrs array
        if (($key = array_search($time, $availHrsArray)) !== false) {
            // Remove the value if it exists
            unset($availHrsArray[$key]);
        } else {
            // Add the new time if it's not already in the array
            $availHrsArray[] = $time;
        }

        // Sort the updated array to maintain the correct order
        sort($availHrsArray);

        // Convert the updated array back to a string and update avail_hrs
        $updatedAvailHrs = implode(',', $availHrsArray);

        // Update the about_me table with the new avail_hrs
        $updateStmt = $conn->prepare("UPDATE about_me SET avail_hrs = ? WHERE email = ?");
        $updateStmt->bind_param("ss", $updatedAvailHrs, $emailUploader);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        echo "No appointment data found for the given email_uploader.";
    }
    $appointmentStmt->close();

} else {
    echo "No email_uploader parameter provided.";
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/profile.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/supplier-profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

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
                    <a class="nav-link" href="../../../index/web/api/about-us.php">About</a>
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

        <div class="container">
  <div class="row mt-5">
    <!-- Calendar Section -->
    <div class="col-md-6">
      <div id="calendar"></div>
    </div>

    <!-- Booking Form and Map Section -->
    <div class="col-md-6 ">
    <div class="container booking-container">

    <form action="../../function/php/save_appointment.php" method="POST">
        <div class="card">
        <div class="card-body">
            
        <h2 class="text-center mb-4">BOOK A SESSION</h2>
            <div class="row mb-3 justify-content-center">
    
        <div class="col-10 d-flex gap-1 mt-2">
            <label for="fullname" class="form-label">Full Name:</label>
            <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Enter your full name" required>
        </div>
        <div class="col-10 d-flex flex-column gap-3">
            <div class="d-flex gap-1 mt-2">
                <label for="location" class="form-label">Location:</label>
                <input type="text" class="form-control" id="search-location" placeholder="Search for a location in Cavite" required>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>
            <div id="map" style="height: 300px; width: 100%; border: 1px solid #dddada; border-radius: 10px;"></div>
        </div>
        <div class="col-10 d-flex gap-1 mt-3">
            <label for="event" class="form-label">Event:</label>
            <select class="form-select" name="event" id="event" required>
                <option value="" disabled selected>Select an option</option>
                <option value="photography">Photography</option>
                <option value="videography">Videography</option>
            </select>
        </div>
        <script>
        // Pass the avail_hrs PHP value to JavaScript
        const availHrs = "<?php echo $availHrs; ?>";
        console.log("Available Hours: ", availHrs);
    </script>
        <div class="col-10 d-flex gap-1 mt-2">
            <label for="time" class="form-label">Time:</label>
            <select class="form-control" name="time" id="time" required>
                <option value="" disabled selected>Select Time</option>
                <?= implode("\n", $timeOptions); // Dynamically insert options ?>
            </select>
        </div>
        <input type="hidden" id="selected_date" name="selected_date">
        <input type="hidden" name="email_uploader" id="email_uploader">
        <input type="hidden" name="email" id="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
        <div class="col-10 d-flex gap-1 mt-2 book">
            <button type="submit" class="btn mt-2">Book</button>
        </div>
    </form>



      
    </div>
  </div>
</div>


 

</div>

        <!-- Google Maps API Script -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
<script>
 let map, marker, geocoder;

function initMap() {
  const caviteBounds = {
    north: 14.5063, 
    south: 14.0800, 
    east: 120.9826, 
    west: 120.6706
  };

  map = new google.maps.Map(document.getElementById('map'), {
    center: { lat: 14.2849, lng: 120.8784 }, 
    zoom: 12,
    restriction: {
      latLngBounds: caviteBounds, 
      strictBounds: true
    }
  });

  geocoder = new google.maps.Geocoder();
  const autocomplete = new google.maps.places.Autocomplete(document.getElementById('search-location'), {
    componentRestrictions: { country: 'ph' },
    bounds: caviteBounds, 
    strictBounds: true
  });

  autocomplete.addListener('place_changed', function () {
    const place = autocomplete.getPlace();
    if (!place.geometry) return;

    document.getElementById('latitude').value = place.geometry.location.lat();
    document.getElementById('longitude').value = place.geometry.location.lng();

    map.setCenter(place.geometry.location);
    map.setZoom(15);

    // Initialize or update the marker position
    if (marker) {
      marker.setPosition(place.geometry.location);
    } else {
      marker = new google.maps.Marker({
        map: map,
        position: place.geometry.location,
        draggable: true,
        icon: {
          url: "../../../../assets/img/camera.png",
          scaledSize: new google.maps.Size(60, 60)
        }
      });
    }

    document.getElementById('location').value = place.formatted_address;

    google.maps.event.addListener(marker, 'dragend', function () {
      geocodePosition(marker.getPosition());
    });
  });

  function geocodePosition(position) {
    geocoder.geocode({ latLng: position }, function (responses) {
      if (responses && responses.length > 0) {
        document.getElementById('location').value = responses[0].formatted_address;
      } else {
        document.getElementById('location').value = 'Cannot determine address at this location.';
      }
    });
  }
}

window.onload = initMap;

</script>

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

    <script src="../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var uploaderEmail = localStorage.getItem("uploader_email"); 

    console.log('Uploader Email:', uploaderEmail);

    if (uploaderEmail) {
        document.getElementById('email_uploader').value = uploaderEmail;
        console.log('Hidden Input Value:', document.getElementById('email_uploader').value); // Debug line
    } else {
        console.error("Uploader email not found in localStorage");
    }

    

    // Fetch the available dates for the uploader
    fetch('../../function/php/get_user_dates.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'uploader_email=' + encodeURIComponent(uploaderEmail)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const availableDates = data.dates; // Get available dates from the response
            initializeCalendar(availableDates);
        } else {
            console.error('Error fetching dates:', data.message);
        }
    })
    .catch(error => console.error('Fetch error:', error));

    function initializeCalendar(availableDates) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            dateClick: function(info) {
                var dateStr = info.dateStr;

                // Only allow selection for available dates
                if (availableDates.includes(dateStr)) {
                    // Set the selected date in the hidden input
                    document.getElementById('selected_date').value = dateStr;

                    // Remove active class from all days
                    const previouslySelected = document.querySelector('.fc-daygrid-day.active-date');
                    if (previouslySelected) {
                        previouslySelected.classList.remove('active-date');
                        previouslySelected.style.backgroundColor = ''; // Reset background color

                        // Reset the day number color for previously selected date
                        const prevDayNumber = previouslySelected.querySelector('.fc-daygrid-day-number');
                        if (prevDayNumber) {
                            prevDayNumber.style.color = ''; // Reset color to default
                        }
                    }

                    // Add active class to the clicked date
                    info.dayEl.classList.add('active-date');
                    info.dayEl.style.backgroundColor = '#7A3015'; // Change background color

                    // Set the text color for the day number to white
                    const dayNumber = info.dayEl.querySelector('.fc-daygrid-day-number');
                    if (dayNumber) {
                        dayNumber.style.color = '#fff'; // Set the day number color to white
                    }
                }
            },
            eventDidMount: function(info) {
                const dateStr = info.event.startStr;

                // Add the active class to available dates
                if (availableDates.includes(dateStr)) {
                    info.el.classList.add('available-date'); // Add available class
                } else {
                    info.el.classList.add('inactive-date'); // Add inactive class
                    info.el.style.opacity = '0.3'; // Set opacity for inactive dates
                }
            },
            datesSet: function(dateInfo) {
                const start = dateInfo.start; // Start of the current view
                const end = dateInfo.end; // End of the current view

                // Wait until the calendar has rendered
                setTimeout(() => {
                    // Get all day cells in the current view
                    const dayEls = document.querySelectorAll('.fc-daygrid-day');

                    // Iterate through the day elements
                    dayEls.forEach(dayEl => {
                        const dateStr = dayEl.dataset.date; // Get the data-date attribute

                        if (!availableDates.includes(dateStr)) {
                            dayEl.classList.add('inactive-date'); // Ensure inactive dates are set
                            dayEl.style.opacity = '0.3'; // Set opacity for inactive dates
                            dayEl.style.pointerEvents = 'none'; // Make them unclickable
                        }
                    });
                }, 0);
            }
        });

        // Add existing available dates as events for highlighting
        availableDates.forEach(date => {
            calendar.addEvent({
                title: 'Available',
                start: date,
                allDay: true,
                classNames: ['available-date'] // Ensure the class is used for styling
            });
        });

        calendar.render();

        // Store the calendar instance for later use
        window.calendarInstance = calendar; // Save reference to the calendar instance
    }
});



  </script>

</body>
</html>
