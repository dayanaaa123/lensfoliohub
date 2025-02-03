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

// Modify the query to select the latest message for each email
$stmt = $conn->prepare("
    SELECT c.email, c.text, c.is_seen
    FROM chat c
    INNER JOIN (
        SELECT email, MAX(id) AS max_id
        FROM chat
        WHERE uploader_email = ?
        GROUP BY email
    ) latest_chat ON c.id = latest_chat.max_id
    ORDER BY c.id DESC
");
$stmt->bind_param("s", $session_email);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any messages
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = htmlspecialchars($row['email']);
        $text = htmlspecialchars($row['text']);
        $isSeen = $row['is_seen'];
        
        // Query the 'users' table to get the name and profile image based on the email
        $userQuery = "SELECT name, profile_img FROM users WHERE email = '$email'";
        $userResult = $conn->query($userQuery);
        
        if ($userResult->num_rows > 0) {
            // Fetch user data
            $userRow = $userResult->fetch_assoc();
            $name = htmlspecialchars($userRow['name']);
            $profileImage = htmlspecialchars($userRow['profile_img']);
        } else {
            // Set default values if user data is not found
            $name = 'Unknown User';
            $profileImage = 'default-profile.png';
        }

        // If message is unseen, set font-weight to bold
        $fontWeight = $isSeen == 0 ? 'bold' : 'normal';

        // Output the HTML with the added bold styling
        echo '
        <div class="messenger-item" data-email="' . $email . '" onclick="markAsSeenAndToggleBold(this, \'' . $email . '\'); document.getElementById(\'click-email\').value = this.getAttribute(\'data-email\'); showMessengerContainer(); loadChat(\'' . $email . '\')">
            <img src="../../../../assets/img/profile/' . $profileImage . '" alt="Receiver" class="profile-pic">
            <div class="details">
                <div class="name">' . $name . '</div>
                <div class="message" style="font-weight: ' . $fontWeight . ';">' . $text . '</div>
            </div>
        </div>';
    }
} else {
    echo '<p>No messages found.</p>';
}

$stmt->close();
$conn->close();
?>


<script>
    function markAsSeenAndToggleBold(element, email) {
    const messageText = element.querySelector('.message');
    const currentWeight = messageText.style.fontWeight;

    // Toggle bold or normal
    messageText.style.fontWeight = currentWeight === 'bold' ? 'normal' : 'bold';

    // If the message was bold and is now clicked, mark it as seen in the database
    if (messageText.style.fontWeight === 'normal') {
        // Send AJAX request to update the 'is_seen' field
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../../function/update_seen_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('email=' + email);

        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log('Message marked as seen');
            }
        };
    }
}

</script>

</div>



<div class="messenger-containers" style="display: none;">
    <div class="messenger-containerss">
     
        <!-- Messages will be appended here -->
    </div>
    <div class="input-container w-100">
        <form id="chat-form" method="POST">
            <input type="hidden" name="email" id="email">
            <input type="hidden" name="click_email" id="click-email">
            <input type="hidden" id="recipient-email" name="recipient_email" />
            <div class="d-flex gap-1">
                <input type="text" name="text" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </div>
        </form>
        <div id="status-message"></div> <!-- Status message container -->
    </div>
</div>

<script>
   document.getElementById('chat-form').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent form from submitting normally

    var formData = new FormData(this); // Get form data

    var statusMessage = document.getElementById('status-message');
    statusMessage.innerHTML = ''; // Clear previous messages

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../../function/php/submit_chat.php', true);


    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            if (response.error) {
                // Handle error
                statusMessage.innerHTML = 'Error: ' + response.error;
            } else {
                // Handle success, dynamically add the message
                var messageDiv = document.createElement('div');
                messageDiv.classList.add('message', 'sent'); // Customize this as needed

                messageDiv.innerHTML = `
                    <div class="message-text">
                        ${response.text}
                    </div>
                `;

                // Append the message to the chat container
                document.querySelector('.messenger-containerss').appendChild(messageDiv);
            }
        } else {
            // Handle network or server error
            statusMessage.innerHTML = 'Error: Could not send message. Please try again.';
        }
    };

    xhr.onerror = function () {
        // Handle network error
        statusMessage.innerHTML = 'Network error. Please try again.';
    };

    xhr.send(formData); // Send the form data via AJAX
});


</script>



<script>
    function showMessengerContainer() {
        document.querySelector('.messenger-container').style.display = 'none';
        document.querySelector('.messenger-containers').style.display = 'block';
    }

    function goBack() {
        // Hide the messenger containers
        document.querySelector('.messenger-containers').style.display = 'none';

        // Show the initial messenger container
        document.querySelector('.messenger-container').style.display = 'block';
    }

    function loadChat(email) {
        document.getElementById('recipient-email').value = email;
        const messengerContainers = document.querySelector('.messenger-containerss');
        messengerContainers.style.display = 'block';

        // Clear previous messages
        messengerContainers.innerHTML = '';

        // Fetch the user's name and profile picture based on the email
        fetch('../../function/php/fetch_user_name.php?email=' + encodeURIComponent(email))
            .then(response => response.text()) // We expect plain text now
            .then(data => {
                const [userName, profilePicture] = data.split('|'); // Split the response by the delimiter '|'
                const profileImage = profilePicture || 'https://via.placeholder.com/40'; 

                // Create and add the header (Back button + "Chat with..." text)
                const headerDiv = document.createElement('div');
                headerDiv.classList.add('chat-header');
                headerDiv.innerHTML = `
                    <button class="back-button" onclick="goBack()"><i class="fa fa-arrow-left fw-bold"></i></button>
                    <span>Chat with ${userName || email}</span>
                    <hr>
                `;
                messengerContainers.appendChild(headerDiv);

                // Create the wrapper div for messages
                const messagesWrapperDiv = document.createElement('div');
                messagesWrapperDiv.classList.add('messagess');

                // Create a div to hold the messages
                const messagesDiv = document.createElement('div');
                messagesDiv.classList.add('messages');

                // Append the messages div to the wrapper div
                messagesWrapperDiv.appendChild(messagesDiv);

                // Append the wrapper div to the messenger container
                messengerContainers.appendChild(messagesWrapperDiv);

                // Fetch combined and sorted chat messages
                fetch('../../function/php/fetch_chat_messages.php?email=' + encodeURIComponent(email))
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(message => {
                                const messageDiv = document.createElement('div');

                                // Create the structure for the sent or received message
                                if (message.type === 'sent') {
                                    messageDiv.classList.add('message', 'received');
                                    messageDiv.innerHTML = ` 
                                        <img src="../../../../assets/img/profile/${profileImage}" alt="Receiver" class="profile-pic">
                                        <div class="message-text">${message.text}</div>
                                    `;
                                } else {
                                    messageDiv.classList.add('message', 'sent');
                                    messageDiv.innerHTML = `
                                        <div class="message-text">${message.text}</div>
                                    `;
                                }

                                messagesDiv.appendChild(messageDiv);
                            });
                            messagesDiv.scrollTop = messagesDiv.scrollHeight; // Scroll to bottom
                        } else {
                            messagesDiv.innerHTML = '<p>No messages found.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading chat messages:', error);
                        messagesDiv.innerHTML = '<p>Error loading messages. Please try again.</p>';
                    });
            })
            .catch(error => {
                console.error('Error fetching user name:', error);
                const headerDiv = document.createElement('div');
                headerDiv.classList.add('chat-header');
                headerDiv.innerHTML = `
                    <button class="back-button" onclick="goBack()"><i class="fa fa-arrow-left fw-bold"></i></button>
                    <span>Chat with ${email}</span>
                    <hr>
                `;
                messengerContainers.appendChild(headerDiv);
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
