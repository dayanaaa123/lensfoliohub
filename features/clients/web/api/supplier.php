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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/supplier.css">

</head>
<body>
    <div id="preloader">
        <div class="line"></div>
        <div class="left"></div>
        <div class="right"></div>
    </div>

    <nav class="navbar navbar-expand-lg ">
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
                        <a class="nav-link" href="#">Supplier</a>
                    </li>

                  
                </ul>
                <div class="d-flex ml-auto">
                    <?php if ($role != 'guest') { ?>
                        <div class="dropdown">
                            <button class="btn btn-theme dropdown-toggle" type=" button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img" style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid #000;" >
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="profile.php">Main Profile</a></li>
                                    <li><a class="dropdown-item" href="status.php">Booking Status</a></li>
                                    <li><a class="dropdown-item" href="history.php">History</a></li>
                                    <li><a class="dropdown-item" href="notification.php">Notifications</a></li>
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
    
    <div class="container ">
    <!-- Search Bar Section -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-8 col-10">
                <div class="search-container">
                    <div class="search-bar">
                        <div class="position-relative" style="margin: auto;">
                            <input type="text" class="form-control search-bars" id="searchInput" placeholder="Search" aria-label="Search" style="padding-right: 40px;">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="filter-buttons d-flex flex-column">
                            <div class="btn-group-vertical" role="group" aria-label="Filter options">
                                <button type="button" class="btn search-button name" data-filter="name">Name</button>
                                <button type="button" class="btn search-button location" data-filter="location">Location</button>
                                <button type="button" class="btn search-button profession" data-filter="profession">Profession</button> 
                                <button type="button" class="btn search-button pricing" data-filter="pricing">Pricing</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Display Results -->
                <div id="searchResults"></div>
            </div>
        </div>
    </div>

    <h4>Recent Searches</h4>
    <?php
    require '../../../../db/db.php';

    $sql = "SELECT 
                u.name,
                u.email,
                u.profile_img,
                a.location_text,
                a.profession,
                a.age,
                a.price,
                AVG(r.rating) AS average_rating
            FROM 
                users u
            JOIN 
                about_me a ON u.email = a.email
            LEFT JOIN 
                ratings r ON u.email = r.supplier_email
            WHERE 
                u.role = 'supplier'
            GROUP BY
                u.email, a.location_text, a.profession, a.age, a.price";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="recent-searches"><div class="row" id="recentSearchResults">'; 

        while($user = $result->fetch_assoc()) {
            $filledStars = round($user['average_rating']) ?: 0;
            $emptyStars = 5 - $filledStars; 

            echo '<div class="col-md-6 search-result" data-name="' . strtolower(htmlspecialchars($user['name'])) . '" data-location="' . strtolower(htmlspecialchars($user['location_text'])) . '" data-profession="' . strtolower(htmlspecialchars($user['profession'])) . '" data-price="' . strtolower(htmlspecialchars($user['price'])) . '">
                    <div class="card">
                        <div class="top p-0"></div>
                        <div class="d-flex">
                            <div class="col-md-4">
                                <img src="../../../../assets/img/profile/' . htmlspecialchars($user['profile_img']) . '" class="img-fluid profile" alt="Photographer">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <p class="card-title"><strong>Name:</strong> ' . htmlspecialchars($user['name']) . '</p>
                                       <p class="card-title"><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>
                                    <p class="card-text"><strong>Location:</strong> ' . htmlspecialchars($user['location_text']) . '</p>
                                    <p class="card-text"><strong>Profession:</strong> ' . htmlspecialchars($user['profession']) . '</p>
                                    <p class="card-text"><strong>Age:</strong> ' . htmlspecialchars($user['age']) . ' Yrs Old</p>
                                    <p class="card-text"><strong>Price:</strong> $' . htmlspecialchars($user['price']) . ' / Hr</p>
                                   
                                    <div class="card-rating justify-content-start">';

                                    for ($i = 0; $i < $filledStars; $i++) {
                                        echo '<span class="star">★</span>';
                                    }

                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<span class="star">☆</span>';
                                    }

                                    echo '  </div>
                                    <form action="about-me.php" method="POST" class="mb-0">
                                        <input type="hidden" name="uploader_email" value="' . htmlspecialchars($user['email']) . '">
                                        <button type="submit" class="btn btn-primary w-50 mt-3 mx-auto d-flex justify-content-center">Hire me!</button>
                                    </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>'; 
                                }

                                echo '</div></div>'; 
                            } else {
                                echo "No suppliers found.";
                            }

                            $conn->close();
                            ?>   


</div>

    <div class="wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 250" style="margin-bottom: -5px;">
          <path fill="#a67b5b" fill-opacity="1"
            d="M0,128L60,138.7C120,149,240,171,360,170.7C480,171,600,149,720,133.3C840,117,960,107,1080,112C1200,117,1320,139,1380,149.3L1440,160L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z">
          </path>
        </svg>
      </div>
  
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
    <script>
// JavaScript for handling search filtering
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchButtons = document.querySelectorAll('.search-button');
    let selectedFilter = 'name';  // Default filter is by name

    // Set the filter based on the button clicked
    searchButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedFilter = this.getAttribute('data-filter');
            searchInput.placeholder = `Search by ${selectedFilter.charAt(0).toUpperCase() + selectedFilter.slice(1)}`;
            searchInput.value = '';  // Clear the search input when changing filter
            searchInput.focus();  // Focus on the input field
        });
    });

    // Function to fetch and display search results based on the input value and selected filter
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim().toLowerCase();
        
        if (query !== '') {
            // Show only results that match the search query
            const results = document.querySelectorAll('.search-result');
            results.forEach(result => {
                let match = result.getAttribute(`data-${selectedFilter}`).toLowerCase().includes(query);
                if (match) {
                    result.style.display = '';  // Show result
                } else {
                    result.style.display = 'none';  // Hide result
                }
            });
        } else {
            const results = document.querySelectorAll('.search-result');
            results.forEach(result => {
                result.style.display = '';  
            });
        }
    });
});
</script>
</body>
</html>
