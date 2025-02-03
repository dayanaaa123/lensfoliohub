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
                         class="img-fluid img-wh w-100" 
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
                         class="img-fluid img-wh w-100" 
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

    console.log('Modal ID:', modalId);
    console.log('New Image Src:', newImgSrc);
    console.log('New Image Title:', newImgTitle);
    console.log('New Image Text:', newImgText);

    var modalImg = document.getElementById('modal-main-img-' + modalId);
    var modalTitle = document.getElementById('modal-main-title-' + modalId);
    var modalText = document.getElementById('modal-main-text-' + modalId);

    if (modalImg && modalTitle && modalText) {
        modalImg.src = newImgSrc;
        modalTitle.textContent = newImgTitle;
        modalText.textContent = newImgText;
    } else {
        console.error('Failed to find modal content elements');
    }

    var galleryItem = imageElement.parentElement;

    if (galleryItem) {
        galleryItem.style.display = 'none';
        
        if (previouslyHiddenGalleryItem) {
            previouslyHiddenGalleryItem.style.display = 'block';
        }
        previouslyHiddenGalleryItem = galleryItem;
    }
}

function confirmDelete(imageId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../../function/php/delete_image.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            window.location.reload();
        } else {
            console.error('An error occurred while deleting the image.');
        }
        var deleteModal = bootstrap.Modal.getInstance(document.getElementById('delete-modal-' + imageId));
        if (deleteModal) {
            deleteModal.hide();
        }
    };
    xhr.send('id=' + imageId);
}

</script>







