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
    
        $profileImg = '' . $profileImg;
    }

if (isset($_POST['uploader_email']) && !empty($_POST['uploader_email'])) {
    $uploaderEmail = htmlspecialchars($_POST['uploader_email']);
    
    echo '<script>
        // Store the uploaderEmail in localStorage so it persists across pages
        localStorage.setItem("uploader_email", "' . $uploaderEmail . '");
    </script>';
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="../../../../assets/logo.jpg" type="image/png">
    <title>LENSFOLIOHUB</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

</head>

<body>
  
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
                        <a class="nav-link" href="#">Snapfeed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about-me.php">Profile</a>
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
                    <a href="contacts.php?email_uploader=<?php echo urlencode($uploaderEmail); ?>"><button class="nav-link contacts highlight">Contacts</button></a>
                </li>
            </ul>
        </div>

        <div class="messenger-container">
    <!-- Conversation area -->
    <div class="messenger-containerss" style="height: 70vh; overflow-y: auto;"></div>

    <!-- Input field for sending a message -->
    <div class="input-container w-100">
        <form method="POST" action="../../function/php/submit_chat.php">
            <input type="hidden" name="email" id="email">
            <input type="hidden" name="uploader_email" id="uploader_email">
            <div class="d-flex gap-1">
                <input type="text" name="text" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
</div>

<script>
function loadChat(recipientEmail, uploaderEmail) {
    // Set recipient email from function argument
    document.getElementById('email').value = recipientEmail;
    document.getElementById('uploader_email').value = uploaderEmail;

    console.log('Recipient Email:', recipientEmail); // Debugging the recipient email
    console.log('Uploader Email:', uploaderEmail);   // Debugging the uploader email

    const messengerContainers = document.querySelector('.messenger-containerss');
    messengerContainers.innerHTML = ''; // Clear previous messages

    // Send the request using POST method instead of GET
    const formData = new FormData();
    formData.append('uploader_email', uploaderEmail); // Add uploader_email to the FormData

    fetch('../../function/php/fetch_chat_messages.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Chat Data:', data);  // Debugging chat data response
        
        // If there are messages
        if (data.length > 0) {
            data.forEach(message => {
                const messageDiv = document.createElement('div');
                
                // Reverse the class for sent and received messages
                if (message.type === 'sent') {
                    // Sent message will now have 'sent' class (aligned to the right)
                    messageDiv.classList.add('message', 'received');  
                    messageDiv.innerHTML = `
                        <div class="message-text">${message.text}</div>
                    `;
                } else {
                    // Received message will now have 'received' class (aligned to the left)
                    messageDiv.classList.add('message', 'sent');  
                    messageDiv.innerHTML = `
                        <div class="message-text">${message.text}</div>
                    `;
                }

                // Fetch sender's profile image dynamically using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '../../function/php/fetch_profile_picture.php?email=' + encodeURIComponent(message.sender), true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const profileImage = xhr.responseText; // Get the profile image path from the response
                        if (profileImage) {
                            const img = document.createElement('img');
                            img.src = "../../../../assets/img/profile/" + profileImage;
                            img.alt = "Sender";
                            img.classList.add('profile-pic');
                            
                            messageDiv.insertBefore(img, messageDiv.firstChild); // Add profile picture before message text
                        }
                    }
                };
                xhr.send();

                // Append the message div to the container
                messengerContainers.appendChild(messageDiv);
            });

            // Scroll to the bottom of the message container
            messengerContainers.scrollTop = messengerContainers.scrollHeight;
        } else {
            messengerContainers.innerHTML = '<p>No messages found.</p>';
        }
    })
    .catch(error => {
        console.error('Error loading chat messages:', error);
        messengerContainers.innerHTML = '<p>Error loading messages. Please try again.</p>';
    });
}

// Get uploader_email from PHP and pass it to the loadChat function
const uploaderEmail = '<?php echo isset($_POST['uploader_email']) ? $_POST['uploader_email'] : ''; ?>';
const recipientEmail = '<?php echo $_SESSION["email"]; ?>'; // Assuming session email for recipient

if (uploaderEmail) {
    loadChat(recipientEmail, uploaderEmail);
} else {
    console.error('Uploader email is missing!');
}





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
