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


 else {
    $profileImg = 'default.jpg'; 
}
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
                    <a href="projects.php"><button class="nav-link">Projects</button></a>
                </li>
                <li class="nav-item">
                    <a href="calendar.php"><button class="nav-link calendar highlight">Calendar</button></a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php"><button class="nav-link contacts">Message</button></a>
                </li>
            </ul>
        </div>

        <div class="container">
  <div class="row mt-5">
    <!-- Calendar Section -->
    <div class="col-md-5">
        <div class="d-flex justify-content-end">
        <button class="btn btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#workingHoursModal">Update Working Hours</button>
        <div class="modal fade" id="workingHoursModal" tabindex="-1" aria-labelledby="workingHoursModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form action="../../function/php/update_working_hours.php" method="POST">
                <div class="modal-header">
                <h5 class="modal-title" id="workingHoursModalLabel">Update Working Hours</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <!-- Buttons to Select All or 24 hrs -->
                <div class="mb-3">
                    <button type="button" class="btn btn-secondary" id="selectAll">All hrs</button>
                    <button type="button" class="btn btn-secondary" id="select24">24 hrs</button>
                </div>

                <!-- Hour Buttons -->
                <div class="d-flex flex-wrap">
                    <?php
                    // Fetch existing hours from the database
                    include '../../../../db/db.php';
                    if (!isset($_SESSION['email'])) {
                        die('Email not found in session.');
                    }
                    $email = $_SESSION['email'];

                    $stmt = $conn->prepare("SELECT avail_hrs FROM about_me WHERE email = ?");
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $stmt->bind_result($availHrs);
                    $stmt->fetch();
                    $stmt->close();

                    $selectedHours = !empty($availHrs) ? explode(',', $availHrs) : [];

                    for ($i = 0; $i < 24; $i++):
                    $hour = $i % 12 === 0 ? 12 : $i % 12;
                    $period = $i < 12 ? 'AM' : 'PM';
                    ?>
                    <button type="button" class="btn m-1 hour-btn <?= in_array($i, $selectedHours) ? 'btn-primary' : 'btn-outline-primary' ?>" data-hour="<?= $i; ?>">
                        <?= sprintf("%02d:00 %s", $hour, $period); ?>
                    </button>
                    <?php endfor; ?>
                </div>

                <!-- Hidden Input to Store Selected Hours -->
                <input type="hidden" name="avail_hrs" id="availHrsInput">
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
            </div>
        </div>
        </div>
            </div>
      <div id="calendar"></div>
    </div>

    <!-- Booking Form and Map Section -->
    <div class="col-md-7 ">
    <div class="container booking-container">
  <h2 class="text-center mb-4">BOOKING REQUEST</h2>

  <!-- Booking Row Example -->
  <?php

require '../../../../db/db.php';

$query = "SELECT * FROM appointment 
WHERE email_uploader = '$email' 
AND status != 'Completed' 
ORDER BY 
    CASE 
        WHEN status = 'Pending' THEN 1
        WHEN status = 'Accepted' THEN 2
        ELSE 3
    END, 
    selected_date ASC"; 

$result = $conn->query($query);

?>




<div class="row mb-3 justify-content-center">
    <div class="col-12 d-flex flex-column confirm-button">
    <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Appointment</th>
                    <th>Date</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Event</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <?php   
                $counter = 1;
                while ($appointment = $result->fetch_assoc()): ?>
                    <tr>
                    <td class="text-center"><?php echo $counter++; ?></td>
                        <td>
                            <?php 
                                $date = new DateTime($appointment['selected_date']);
                                echo $date->format('M j, Y'); 
                            ?>
                        </td>
                        <td><?php echo $appointment['name']; ?></td>
                        <td><?php echo $appointment['email']; ?></td>
                      
                        <td><?php echo $appointment['event']; ?></td>
                        <td><?php echo $appointment['time']; ?></td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#bookingModal<?php echo $appointment['id']; ?>">
                                    View
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Pending')">Pending</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Accepted')">Accept</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Declined')">Declined</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $appointment['id']; ?>, 'Completed')">Completed</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal for Booking Details -->
                    <div class="modal fade" id="bookingModal<?php echo $appointment['id']; ?>" tabindex="-1" aria-labelledby="bookingModalLabel<?php echo $appointment['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="bookingModalLabel<?php echo $appointment['id']; ?>">Booking Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Display Date -->
                                <div class="mb-3">
                                    <strong>Date:</strong> <span id="bookingDate"><?php 
                                        $date = new DateTime($appointment['selected_date']);
                                        echo $date->format('M j, Y'); 
                                    ?></span>
                                </div>

                                <!-- Display Full Name -->
                                <div class="mb-3">
                                    <strong>Full Name:</strong> <span id="fullname"><?php echo $appointment['name']; ?></span>
                                </div>

                                <!-- Display Google Maps with Latitude and Longitude -->
                                <div class="mb-3">
                                    <div id="map<?php echo $appointment['id']; ?>" style="width: 100%; height: 400px;" 
                                        data-latitude="<?php echo $appointment['latitude']; ?>" 
                                        data-longitude="<?php echo $appointment['longitude']; ?>">
                                    </div> <!-- Map container -->
                                </div>

                                <!-- Google Maps API Script -->
                                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw"></script>
                                <script>
                                    function initMap(appointmentId) {
                                        var mapElement = document.getElementById('map' + appointmentId);
                                        var latitude = parseFloat(mapElement.getAttribute('data-latitude'));
                                        var longitude = parseFloat(mapElement.getAttribute('data-longitude'));

                                        var map = new google.maps.Map(mapElement, {
                                            center: { lat: latitude, lng: longitude },
                                            zoom: 15
                                        });

                                        var marker = new google.maps.Marker({
                                            position: { lat: latitude, lng: longitude },
                                            map: map,
                                            title: 'Appointment Location'
                                        });
                                    }

                                    // Initialize the map when the modal opens
                                    document.addEventListener('DOMContentLoaded', function () {
                                        document.getElementById('bookingModal<?php echo $appointment['id']; ?>').addEventListener('shown.bs.modal', function () {
                                            initMap(<?php echo $appointment['id']; ?>);
                                        });
                                    });
                                </script>

                                <!-- Display Event Type -->
                                <div class="mb-3">
                                    <strong>Event:</strong> <span id="eventType"><?php echo $appointment['event']; ?></span>
                                </div>

                                <!-- Display Time -->
                                <div class="mb-3">
                                    <strong>Time:</strong> <span id="bookingTime"><?php echo $appointment['time']; ?></span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No appointments available.</p>
<?php endif; ?>

    </div>
</div>
      
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
    var selectedDates = []; // Array to store active dates

    // Fetch existing available dates from the server
    fetch('../../function/php/get_dates.php') // Create this PHP file to return available dates
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const availableDates = data.dates; // Get dates array from the response
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

                // Toggle the active state of the clicked date
                if (selectedDates.includes(dateStr)) {
                    // If the date is already active, remove it
                    selectedDates = selectedDates.filter(date => date !== dateStr);
                    info.dayEl.classList.remove('active-date'); // Remove active class
                } else {
                    // If the date is not active, add it
                    selectedDates.push(dateStr);
                    info.dayEl.classList.add('active-date'); // Add active class
                }

                // Send the updated date to the server
                updateAvailableDate(dateStr);
            },
            // Render the available dates with the 'active-date' class
            eventDidMount: function(info) {
                if (availableDates.includes(info.event.startStr)) {
                    // Add the active class to the date element
                    info.el.classList.add('active-date');
                }
            }
        });

        // Add existing available dates as events for highlighting
        availableDates.forEach(date => {
            calendar.addEvent({
                title: 'Available',
                start: date,
                allDay: true,
                classNames: ['active-date'] // Ensure the class is used for styling
            });
        });

        calendar.render();

        // Store the calendar instance for later use
        window.calendarInstance = calendar; // Save reference to the calendar instance
    }

    function updateAvailableDate(date) {
        // Send the clicked date to the server to update
        fetch('../../function/php/update_date.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ date: date })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Date updated:', data);
            // Refresh available dates after each update
            refreshAvailableDates();
        })
        .catch((error) => {
            console.error('Error updating date:', error);
        });
    }

    function refreshAvailableDates() {
        fetch('../../function/php/get_dates.php') // Call the PHP file to get updated dates
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const updatedAvailableDates = data.dates; // Get updated dates array
                    // Clear the calendar and reinitialize it
                    window.calendarInstance.removeAllEvents(); // Remove existing events
                    initializeCalendar(updatedAvailableDates); // Reinitialize with updated dates
                } else {
                    console.error('Error fetching updated dates:', data.message);
                }
            })
            .catch(error => console.error('Fetch error:', error));
    }
});
  </script>

<script>
    function updateStatus(appointmentId, status) {
        // Send an AJAX request to update the status in the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../../function/php/update_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status == 200) {
                // Successfully updated
                alert('Status updated to ' + status);
                location.reload(); // Reload the page to reflect the changes
            } else {
                alert('Error updating status');
            }
        };
        xhr.send('appointmentId=' + appointmentId + '&status=' + status);
    }
</script>



<script>
  // JavaScript to Handle Hour Selection
  document.addEventListener("DOMContentLoaded", function () {
    const hourButtons = document.querySelectorAll(".hour-btn");
    const availHrsInput = document.getElementById("availHrsInput");
    const selectAllButton = document.getElementById("selectAll");
    const select24Button = document.getElementById("select24");
    let selectedHours = [];

    // Function to update hidden input
    const updateInput = () => {
      availHrsInput.value = selectedHours.join(",");
    };

    // Hour button click event
    hourButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const hour = this.getAttribute("data-hour");
        if (selectedHours.includes(hour)) {
          selectedHours = selectedHours.filter((h) => h !== hour);
          this.classList.remove("btn-primary");
          this.classList.add("btn-outline-primary");
        } else {
          selectedHours.push(hour);
          this.classList.remove("btn-outline-primary");
          this.classList.add("btn-primary");
        }
        updateInput();
      });
    });

    // Select all hours
    selectAllButton.addEventListener("click", function () {
      selectedHours = Array.from({ length: 24 }, (_, i) => i.toString());
      hourButtons.forEach((button) => {
        button.classList.remove("btn-outline-primary");
        button.classList.add("btn-primary");
      });
      updateInput();
    });

    // Select 24 hrs
    select24Button.addEventListener("click", function () {
      selectedHours = ["0", "24"];
      hourButtons.forEach((button) => {
        button.classList.remove("btn-primary");
        button.classList.add("btn-outline-primary");
      });
      updateInput();
    });
  });
</script>


</body>
</html>
