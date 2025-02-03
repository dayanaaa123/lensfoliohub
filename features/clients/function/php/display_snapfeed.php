<?php

require '../../../../db/db.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];
$role = $_SESSION['role']; 
$name = $_SESSION['name'];

// Pagination Variables
$limit = 9; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Get total number of posts
$totalQuery = "SELECT COUNT(*) AS total FROM snapfeed";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

$sql = "
SELECT snapfeed.id, snapfeed.img_title, snapfeed.hearts_count, snapfeed.card_img, snapfeed.card_text, snapfeed.email, 
       users.name, users.profile_img 
FROM snapfeed 
LEFT JOIN users ON snapfeed.email = users.email 
ORDER BY snapfeed.id DESC
LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
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

        echo '<div class="col-md-4 mb-3 gallery-item position-relative" id="gallery-item-' . $id . '">';

        if (pathinfo($imgSrc, PATHINFO_EXTENSION) === 'mp4') {
            echo '<video src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" 
                        class="img-fluid img-wh" 
                        controls 
                        data-bs-toggle="modal" 
                        data-bs-target="#modal-' . $id . '"
                        data-video-src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" 
                        data-img-title="' . htmlspecialchars($imgTitle) . '" 
                        data-img-text="' . htmlspecialchars($cardText) . '"
                        data-email="' . htmlspecialchars($uploaderEmail) . '"
                        data-name="' . htmlspecialchars($name) . '"
                        data-profile-img="' . htmlspecialchars($profileImg) . '"
                        data-modal-id="' . $id . '" 
                        onclick="updateModalContent(this)">
                    Your browser does not support the video tag.
                </video>';
        } else {
            echo '<img src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" 
                        class="img-fluid img-wh" 
                        alt="Image from Snapfeed" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modal-' . $id . '"
                        data-img-src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" 
                        data-img-title="' . htmlspecialchars($imgTitle) . '" 
                        data-img-text="' . htmlspecialchars($cardText) . '"
                        data-email="' . htmlspecialchars($uploaderEmail) . '"
                        data-name="' . htmlspecialchars($name) . '"
                        data-profile-img="' . htmlspecialchars($profileImg) . '"
                        data-modal-id="' . $id . '" 
                        onclick="updateModalContent(this)">';
        }

        echo '</div>';


        // Modal structure (YOUR EXISTING MODAL CODE, UNTOUCHED)
         // Modal structure
         echo '<div class="modal fade" id="modal-' . $id . '" tabindex="-1" aria-labelledby="modalLabel-' . $id . '" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-xl">
             <div class="modal-content">
                 <div class="modal-header">
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                      <div class="col-md-6">
                             ' . (pathinfo($imgSrc, PATHINFO_EXTENSION) === 'mp4' ? '
                                 <video id="modal-main-video-' . $id . '" class="img-fluid w-100" controls>
                                     <source src="../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" type="video/mp4">
                                     Your browser does not support the video tag.
                                 </video>
                             ' : '
                                 <img id="modal-main-img-' . $id . '" src="../../../../../assets/img/snapfeed/' . htmlspecialchars($imgSrc) . '" class="img-fluid w-100" alt="Image from Snapfeed">
                             ') . '
                         </div>
                         <div class="col-md-6 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                
                                 <form action="about-me.php" method="POST" class="mb-0" onsubmit="saveUploaderEmail(\'' . htmlspecialchars($uploaderEmail) . '\')">
                                     <input type="hidden" name="uploader_email" value="' . htmlspecialchars($uploaderEmail) . '">
                                     <button type="submit" id="modal-main-name-' . htmlspecialchars($id) . '" class="mb-0" style="background: none; border: none; padding: 0; color: inherit; cursor: pointer;">
                                         ' . htmlspecialchars($name) . '
                                     </button>
                                 </form>
                             </div>
 
                             <p id="modal-main-title-' . $id . '" class="img-title">' . htmlspecialchars($imgTitle) . '</p>
                             <p id="modal-main-text-' . $id . '" class="card-text">' . htmlspecialchars($cardText) . '</p>
 
                             <div class="container comments" id="comments-section-' . $id . '">
                                 <div class="comments-box">';
 
                                 $comments_sql = "
                                 SELECT users.name, users.profile_img, comments.comments 
                                 FROM comments 
                                 JOIN snapfeed ON comments.card_img = snapfeed.card_img 
                                 JOIN users ON comments.session_email = users.email
                                 WHERE comments.card_img = ?"; // Filter by the specific card_img
                     
                             // Prepare and execute the query
                             $comments_stmt = $conn->prepare($comments_sql);
                             $comments_stmt->bind_param('s', $imgSrc); // Bind the specific card_img (or id) to the query
                             $comments_stmt->execute();
                             $comments_result = $comments_stmt->get_result();
                     
                             if ($comments_result->num_rows > 0) {
                                 while ($comment_row = $comments_result->fetch_assoc()) {
                                     $commentName = $comment_row['name'];
                                     $commentProfileImg = $comment_row['profile_img'] ? '../../../../assets/img/profile/' . $comment_row['profile_img'] : '../../../../default-profile.jpg';
                                     $commentText = $comment_row['comments'];
                     
                                     echo '<div class="comment d-flex align-items-center mb-2">
                                         <img src="' . htmlspecialchars($commentProfileImg) . '" alt="Profile Image" class="rounded-circle me-2" width="40" height="40">
                                         <strong>' . htmlspecialchars($commentName) . ':</strong> ' . htmlspecialchars($commentText) . '
                                     </div>';
                                 }
                             } else {
                                 echo '<p>No comments yet.</p>';
                             }
 
                                 echo '</div>
                                 <div class="d-flex gap-4">
                                     <div class="input-container d-flex align-items-center">
                                         <form action="../../function/php/post_comments.php" method="post">
                                             <div class="d-flex">
                                                 <input type="hidden" name="id" value="' . $id . '"> 
                                                 <input type="text" class="form-control input-field" name="comments" placeholder="Type something" required>
                                                 <button class="btn send" type="submit">
                                                     <i class="fas fa-paper-plane"></i>
                                                 </button>
                                             </div>
                                         </form>
                                     </div>
                                     <div class="d-flex align-items-center">
                                         <button class="heart-btn" data-id="' . $id . '" data-card-img="' . $imgSrc . '" data-email="' . $email . '">
                                             <i class="fas fa-heart"></i>
                                         </button>
                                         <span class="heart-count" id="heartCount-' . $id . '">' . htmlspecialchars($heartsCount) . '</span>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="row mt-3">
                         <div class="col-md-12">
                             <h5>Gallery of <span id="gallery-uploader-' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</span></h5>
                             <div class="row" id="uploader-images-' . $id . '">';
 
                             $img_sql = "SELECT id, img_title, card_img, card_text FROM snapfeed WHERE email = ? AND id != ? ORDER BY id DESC";
                             $img_stmt = $conn->prepare($img_sql);
                             $img_stmt->bind_param("si", $uploaderEmail, $id);
                             $img_stmt->execute();
                             $img_result = $img_stmt->get_result();
 
                             if ($img_result->num_rows > 0) {
                                 while ($img_row = $img_result->fetch_assoc()) {
                                     echo '
                                         <div class="col-md-4 mb-3 gallery-item" id="gallery-item-' . $img_row['id'] . '" style="display: flex; justify-content: center; align-items: center; overflow: hidden;">
                                             <img src="' . htmlspecialchars($img_row['card_img']) . '" class="modal-img" alt="Additional Image from Snapfeed" 
                                                 data-img-src="' . htmlspecialchars($img_row['card_img']) . '" 
                                                 data-img-title="' . htmlspecialchars($img_row['img_title']) . '" 
                                                 data-img-text="' . htmlspecialchars($img_row['card_text']) . '"
                                                 data-modal-id="' . $id . '" 
                                                 onclick="updateModalContent(this)"
                                                 style="max-width: 100%; max-height: 100%; object-fit: contain; flex-grow: 0;">
                                         </div>';

                                 }
                             } else {
                                 echo '<p>No additional images from this uploader.</p>';
                             }
 
                             echo '</div>
                         </div>
                     </div>
 
                    
                 </div>
             </div>
         </div>
     </div>';
    }

    echo '</div>'; // End of row

    // Pagination Buttons
    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-center">';

    if ($page > 1) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }

    if ($page < $totalPages) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
}

$conn->close();

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
document.addEventListener("DOMContentLoaded", function () {
    const heartButtons = document.querySelectorAll('.heart-btn');

    heartButtons.forEach(function (heartButton) {
        const id = heartButton.getAttribute('data-id'); // Get the unique ID for the heart button
        const email = heartButton.getAttribute('data-email');
        const cardImg = heartButton.getAttribute('data-card-img');

        // Make an AJAX request to check if the heart is already active
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../../function/php/check_heart_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                if (response.action === 'active') {
                    heartButton.classList.add('active');
                }
            } else {
                console.error('Error:', response.message);
            }
        };

        xhr.send(`email=${encodeURIComponent(email)}&card_img=${encodeURIComponent(cardImg)}`);
        
        // Existing heart button click functionality
        heartButton.addEventListener('click', function () {
            const isActive = heartButton.classList.contains('active');
            const action = isActive ? 'inactive' : 'active';

            // Send AJAX request to update heart
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../../function/php/update_heart_count.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    const heartCountElement = document.getElementById('heartCount-' + id);

                    // Update UI
                    if (response.action === 'active') {
                        heartButton.classList.add('active');
                    } else {
                        heartButton.classList.remove('active');
                    }

                    // Update heart count
                    heartCountElement.textContent = response.hearts_count;
                } else {
                    console.error('Error:', response.message);
                }
            };

            xhr.send(`email=${encodeURIComponent(email)}&card_img=${encodeURIComponent(cardImg)}&action=${action}`);
        });
    });
});




</script>


