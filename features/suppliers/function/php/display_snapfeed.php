<?php


if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}
$email = $_SESSION['email'];
$role = $_SESSION['role']; 
$name = $_SESSION['name'];

require '../../../../db/db.php';


$sql = "SELECT id, img_title, card_img, card_text FROM snapfeed WHERE email = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); 
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
    
        echo '
    <div class="col-md-3 mb-3 gallery-item position-relative" id="gallery-item-' . $id . '">
        ' . 
        (in_array(strtolower(pathinfo($imgSrc, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']) ? 
            '<img src="../../../../assets/img/snapfeed/' . $imgSrc . '" class="img-fluid img-wh" alt="Image from Snapfeed" onclick="updateModalContent(this)">' : 
            (in_array(strtolower(pathinfo($imgSrc, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg']) ? 
            '<video class="img-fluid img-wh" controls>
                <source src="../../../../assets/img/snapfeed/' . $imgSrc . '" type="video/' . pathinfo($imgSrc, PATHINFO_EXTENSION) . '">
                Your browser does not support the video tag.
            </video>' : 
            '<p>Unsupported media type</p>'))
        . '
        <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" data-bs-toggle="modal" data-bs-target="#delete-modal-' . $id . '">
            <i class="fas fa-trash"></i>
        </button>
    </div>';


    
        echo '
        <div class="modal fade" id="modal-' . $id . '" tabindex="-1" aria-labelledby="modalLabel-' . $id . '" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img id="modal-main-img-' . $id . '" src="' . $imgSrc . '" class="img-fluid" alt="Image from Snapfeed">
                            </div>
                            <div class="col-md-6 d-flex flex-column">
                                <p id="modal-main-title-' . $id . '" class="img-title">' . htmlspecialchars($imgTitle) . '</p>
                                <p id="modal-main-text-' . $id . '" class="card-text">' . htmlspecialchars($cardText) . '</p>
                                <div class="container mt-auto">
                                    <div class="input-container">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-field" placeholder="Type something">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="fas fa-paper-plane"></i> <!-- Send icon -->
                                                </button>
                                            </div>
                                        </div>
                                        <div class="like-container">
                                            <button class="like-btn" type="button">
                                            <i class="fa-regular fa-heart"></i>
                                            </button>
                                            <span class="like-count" id="hearts-counts">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>' . htmlspecialchars($name) . '\'s Gallery</h5>
                                <div class="row">';
    
        $img_sql = "SELECT id, img_title, card_img, card_text FROM snapfeed WHERE email = ? ORDER BY id DESC";
        $img_stmt = $conn->prepare($img_sql);
        $img_stmt->bind_param("s", $email);
        $img_stmt->execute();
        $img_result = $img_stmt->get_result();
    
        if ($img_result->num_rows > 0) {
            while ($img_row = $img_result->fetch_assoc()) {
                if ($img_row['card_img'] == $imgSrc) {
                    continue; 
                }
    

                echo '
                <div class="col-md-4 mb-3 gallery-item" id="gallery-item-' . $img_row['id'] . '">
                    <img src="' . htmlspecialchars($img_row['card_img']) . '" class="img-fluid modal-img" alt="Additional Image from Snapfeed" 
                         data-img-src="' . htmlspecialchars($img_row['card_img']) . '" 
                         data-img-title="' . htmlspecialchars($img_row['img_title']) . '" 
                         data-img-text="' . htmlspecialchars($img_row['card_text']) . '"
                         data-modal-id="' . $id . '" 
                         onclick="updateModalContent(this)">
                </div>';
            }
        }
    
        echo '
                                </div>
                            </div>
                        </div>                         
                    </div>
                </div>
            </div>
        </div>';
    
        echo '
        <div class="modal fade" id="delete-modal-' . $id . '" tabindex="-1" aria-labelledby="deleteModalLabel-' . $id . '" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel-' . $id . '">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this image?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(' . $id . ')">Delete</button>
                </div>
            </div>
        </div>
    </div>';
    }
}
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







