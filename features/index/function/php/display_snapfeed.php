<?php

require '../../../../db/db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Query to get snapfeed data and join with users table
$sql = "
SELECT snapfeed.id, snapfeed.img_title, snapfeed.hearts_count, snapfeed.card_img, snapfeed.card_text, snapfeed.email, 
       users.name, users.profile_img 
FROM snapfeed 
LEFT JOIN users ON snapfeed.email = users.email 
ORDER BY snapfeed.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result === FALSE) {
    echo "Error executing query: " . $conn->error;
    exit();
}

if ($result->num_rows > 0) {
    echo '<div class="row">';

    while ($row = $result->fetch_assoc()) {
        
        $id = $row['id'];
        $imgTitle = $row['img_title'];
        $imgSrc = $row['card_img'];
        $cardText = $row['card_text'];
        $uploaderEmail = $row['email'];
        $name = $row['name'] ?? 'Unknown'; 
        $profileImg = $row['profile_img'] ? '../../../../assets/img/profile/' . $row['profile_img'] : '../../../../default-profile.jpg'; 
        $heartsCount = $row['hearts_count'] ? $row['hearts_count'] : 0;

        echo '

        <div class="col-md-4 mb-3 gallery-item position-relative" id="gallery-item-' . $id . '">
            <a href="../../../../authentication/web/api/login.php"><img src="../../../../assets/img/snapfeed/' . $imgSrc . '" class="img-fluid img-wh" alt="Image from Snapfeed" 
                 data-bs-toggle="modal" data-bs-target="#modal-' . $id . '"
                 data-img-src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" 
                 data-img-title="' . htmlspecialchars($imgTitle) . '" 
                 data-img-text="' . htmlspecialchars($cardText) . '"
                 data-email="' . htmlspecialchars($uploaderEmail) . '"
                 data-name="' . htmlspecialchars($name) . '"
                 data-profile-img="' . htmlspecialchars($profileImg) . '"
                 data-modal-id="' . $id . '" 
                 onclick="updateModalContent(this)"></a>
        </div>';

        // Modal structure
       
    }
    echo '</div>'; // Closing row div
}

?>


<script>
var previouslyHiddenGalleryItem = null;

function updateModalContent(imageElement) {
    var modalId = imageElement.getAttribute('data-modal-id');

    var newImgSrc = imageElement.getAttribute('data-img-src');
    var newImgTitle = imageElement.getAttribute('data-img-title');
    var newImgText = imageElement.getAttribute('data-img-text');
    var uploaderName = imageElement.getAttribute('data-name') || 'Unknown User'; 
    var uploaderProfileImg = imageElement.getAttribute('data-profile-img') || '../../../../default-profile.jpg'; 

    var modalImg = document.getElementById('modal-main-img-' + modalId);
    var modalTitle = document.getElementById('modal-main-title-' + modalId);
    var modalText = document.getElementById('modal-main-text-' + modalId);
    var modalUploaderName = document.getElementById('modal-main-name-' + modalId);
    var modalUploaderProfileImg = document.querySelector('#modal-' + modalId + ' .rounded-circle');

    if (modalImg) {
        modalImg.src = newImgSrc;
    }

    if (modalTitle) {
        modalTitle.textContent = newImgTitle;
    }

    if (modalText) {
        modalText.textContent = newImgText;
    }

    if (modalUploaderName) {
        modalUploaderName.textContent = uploaderName;
    }

    if (modalUploaderProfileImg) {
        modalUploaderProfileImg.src = uploaderProfileImg;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item img');

    galleryItems.forEach(function(item) {
        item.addEventListener('click', function() {
            updateModalContent(this);
        });
    });
});

function saveUploaderEmail(email) {
        localStorage.setItem('uploader_email', email);
    }


    
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Select all heart buttons using the class name
    const heartButtons = document.querySelectorAll('.heart-btn');
    
    heartButtons.forEach(function(heartButton) {
        const heartCount = heartButton.closest('.modal').querySelector('.heart-count');
        
        heartButton.addEventListener('click', function() {
            const id = heartButton.getAttribute('data-id');
            const cardImg = heartButton.getAttribute('data-card-img');
            const email = heartButton.getAttribute('data-email');
            
            // Toggle the heart button state
            heartButton.classList.toggle('active');
            
            // Determine the action (active or inactive)
            const action = heartButton.classList.contains('active') ? 'active' : 'inactive';
            
            // Update the heart count based on the current state
            let count = parseInt(heartCount.textContent);
            count = action === 'active' ? count + 1 : count - 1;
            heartCount.textContent = count;

            // Send an AJAX request to update the heart count
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../function/php/update_heart_count.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'error') {
                    // If there was an error, revert the count and heart state
                    heartCount.textContent = action === 'active' ? count - 1 : count + 1;
                    heartButton.classList.toggle('active');
                }
            };
            
            // Send the data to the server
            xhr.send('id=' + encodeURIComponent(id) + '&card_img=' + encodeURIComponent(cardImg) + '&email=' + encodeURIComponent(email) + '&action=' + encodeURIComponent(action));
        });
    });
});

</script>


