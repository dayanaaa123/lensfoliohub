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
    
        $stmt = $conn->prepare("SELECT profile_img FROM users WHERE email = ?");
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
    
    <link rel="icon" href="assets/img/profile.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

</head>

<body>
  
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
                    <a href="calendar.php"><button class="nav-link calendar">Calendar</button></a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php"><button class="nav-link contacts highlight">Message</button></a>
                </li>
            </ul>
        </div>
        

        <div class="messenger-container">
    <?php
    require '../../../../db/db.php';
    $session_email = $_SESSION['email'];
    $stmt = $conn->prepare("
        SELECT email, text 
        FROM chat 
        WHERE uploader_email = ? 
        GROUP BY email 
        ORDER BY MAX(id) DESC
    ");
    $stmt->bind_param("s", $session_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any messages
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $email = htmlspecialchars($row['email']);
            $text = htmlspecialchars($row['text']);
            echo '
                <div class="messenger-item" data-email="' . $email . '" onclick="document.getElementById(\'click-email\').value = this.getAttribute(\'data-email\'); console.log(this.getAttribute(\'data-email\')); showMessengerContainer(); loadChat(\'' . $email . '\')">
                <img src="https://via.placeholder.com/40" alt="User">
                <div class="details">
                    <div class="name">' . $email . '</div>
                    <div class="message">' . $text . '</div>
                </div>
            </div>';
        }
    } else {
        echo '<p>No messages found.</p>';
    }

    $stmt->close();
    $conn->close();
    ?>
</div>




<div class="messenger-containers" style="display: none;">
    <div class="messenger-containerss" style="height: 50vh; overflow-y: auto;">
        <!-- Messages will be appended here -->
    </div>
    <div class="input-container w-100">
    <form method="POST" action="../../function/php/submit_chat.php">
        <input type="hidden" name="email" id="email">
        <input type="hidden" name="uploader_email" id="uploader_email">
        <input type="hidden" name="click_email" id="click-email">
        <div class="d-flex gap-1">
            <input type="text" name="text" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </div>
    </form>
    </div>
</div>

<!-- Input field -->



            <script>
               function showMessengerContainer() {
                    // Hide the initial container
                    document.querySelector('.messenger-container').style.display = 'none';
                    
                    // Show the chat messages container
                    document.querySelector('.messenger-containers').style.display = 'block';
                }
            </script>

<script>
    function loadChat(email, event) {
    // Correctly getting the data-email from the clicked element
    const clickedEmail = event.target.getAttribute('data-email');
    
    // Set the click email to the hidden input
    document.getElementById('click-email').value = clickedEmail;

    const messengerContainers = document.querySelector('.messenger-containerss');
    messengerContainers.style.display = 'block';

    // Clear existing messages, keeping the input-container untouched
    messengerContainers.querySelectorAll('.message').forEach(el => el.remove());

    // Fetch chat details using AJAX
    fetch('../../function/php/fetch_chat.php?email=' + email)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Create a document fragment to append messages
                const fragment = document.createDocumentFragment();
                data.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message', message.type); // Assuming 'received' or 'sent' based on message type
                    messageDiv.innerHTML = `
                        <div class="message-text">
                            ${message.text}
                        </div>
                    `;
                    fragment.appendChild(messageDiv);
                });

                // Append messages to the container
                messengerContainers.appendChild(fragment);
            } else {
                messengerContainers.innerHTML = '<p>No messages found.</p>';
            }
        })
        .catch(error => {
            console.error('Error fetching chat:', error);
        });
}



</script>




   

    <script src="../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


  <script>
    // Retrieve uploader_email from localStorage
    const uploaderEmail = localStorage.getItem("uploader_email");
    if (uploaderEmail) {
        document.getElementById('uploader_email').value = uploaderEmail;
    }

    // Set session email using PHP
    document.getElementById('email').value = '<?php echo htmlspecialchars($_SESSION["email"]); ?>';
</script>

</body>
</html>
