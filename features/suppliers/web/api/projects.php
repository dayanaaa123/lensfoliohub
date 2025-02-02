<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: authentication/web/api/login.php");
    exit();
}

$email = $_SESSION['email'];
$role = $_SESSION['role']; 

require '../../../../db/db.php';

// Fetch profile image if not a guest
if ($role != 'guest' && !empty($email)) {
    require '../../../../db/db.php';

    $stmt = $conn->prepare("SELECT profile_image FROM about_me WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($profileImg);
    $stmt->fetch();


}


$sql = "SELECT id, template, profile_image, gallery_name FROM template1 WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
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
                        <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="Profile" class="profile-img">
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="about-me.php">Main Profile</a></li>
                            <li><a class="dropdown-item" href="../../../index/function/php/logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
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
                    <a href="projects.php"><button class="nav-link highlight">Projects</button></a>
                </li>
                <li class="nav-item">
                    <a href="calendar.php"><button class="nav-link calendar">Calendar</button></a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php"><button class="nav-link contacts">Message</button></a>
                </li>
            </ul>
        </div>

        <div class="about-me-section" style="display: none;">
            <div class="container mt-5 about-section">
                    <div class="col-md-8 d-flex flex-column justify-content-center">
                        <form enctype="multipart/form-data">
                            <div class="mb-3">

                                    <img src="../../../../assets/img/profile/profile.jpg" class="img-fluid rounded-circle mb-2" alt="Profile Picture">

                                <!-- Input field for image upload -->
                                <input class="form-control" type="file" id="imageUpload" accept="image/*" >
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" placeholder="Hi, I'm a photographer">Hey</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
            </div>
        </div>

        <div class="container mt-5">
           

        <div class="w-100 d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-warning text-white w-25" data-bs-toggle="modal" data-bs-target="#uploadModal">
        + Upload Image
    </button>
</div>

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../../function/php/template1.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="email" value="<?= $email ?>">

                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image" required>
                    </div>

                    <div class="mb-3">
                        <label for="gallery_name" class="form-label">Gallery Name</label>
                        <input type="text" class="form-control" id="gallery_name" name="gallery_name" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="grid-layout" class="projects">
    <div class="row g-3">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-3">
                <div class="card position-relative">
                    <img src="../../../../assets/img/template/<?= $row['profile_image'] ?>" 
                         class="img-fluid img-thumbnail w-100" style="height: 30vh; cursor: pointer;" 
                         alt="Template Image" 
                         data-bs-toggle="modal" 
                         data-bs-target="#templateModal<?= $row['id'] ?>">

                    <!-- Delete Icon -->
                    <button type="button" class="btn btn-danger position-absolute top-0 end-0 m-2" 
                            data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Modal for Template -->
            <div class="modal fade" id="templateModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $row['id'] ?> Gallery</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <!-- View Type Toggle Buttons -->
                            <div class="mb-3">
                                <form method="post" action="">
                                    <button type="submit" name="view_type" value="grid" class="btn <?= isset($currentView) && $currentView == 'grid' ? 'btn-active btn-pri' : 'btn-outline-primary' ?>">Grid</button>
                                    <button type="submit" name="view_type" value="carousel" class="btn <?= isset($currentView) && $currentView == 'carousel' ? 'btn-active btn-pri' : 'btn-outline-secondary' ?>">Carousel</button>
                                </form>
                            </div>

                            <?php

                            // Handle template update
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['view_type'])) {
                                $viewType = $_POST['view_type'];
                                
                                $updateQuery = "UPDATE template1 SET template = ? WHERE email = ?"; 
                                $stmtUpdate = $conn->prepare($updateQuery);
                                $stmtUpdate->bind_param("ss", $viewType, $email);
                                $stmtUpdate->execute();
                                $stmtUpdate->close();
                                $currentView = $viewType; 
                            } else {
                                // Fetch query for the user based on their email
                                $fetchQuery = "SELECT template FROM template1 WHERE email = ?";
                                $stmtFetch = $conn->prepare($fetchQuery);
                                $stmtFetch->bind_param("s", $email);
                                $stmtFetch->execute();
                                $resultFetch = $stmtFetch->get_result();
                                
                                // If there's a result, use the template from the database, otherwise default to 'grid'
                                $rowFetch = $resultFetch->fetch_assoc();
                                $currentView = $rowFetch['template'] ?? 'grid';
                                
                                $stmtFetch->close();
                            }
                            ?>


                            <!-- Gallery Content -->
                            <div class="row g-3">
                                <?php 
                                $galleryQuery = "SELECT image_name FROM gallery_images WHERE gallery_name = ?";
                                $stmtGallery = $conn->prepare($galleryQuery);
                                $stmtGallery->bind_param("s", $row['gallery_name']);
                                $stmtGallery->execute();
                                $galleryResult = $stmtGallery->get_result();

                                if ($currentView === 'grid'): ?>
                                    <!-- Grid View -->
                                    <div class="grid">
                                        <div class="row">
                                            <?php while ($galleryRow = $galleryResult->fetch_assoc()): ?>
                                                <div class="col-md-4">
                                                    <img src="../../../../assets/img/gallery/<?= $galleryRow['image_name'] ?>" 
                                                         class="img-fluid img-thumbnail w-100" 
                                                         alt="Gallery Image" style="height: 30vh;">
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                <?php elseif ($currentView === 'carousel'): ?>
                                    <!-- Carousel View -->
                                    <div id="galleryCarousel<?= $row['gallery_name'] ?>" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php 
                                            $isActive = true; 
                                            while ($galleryRow = $galleryResult->fetch_assoc()): ?>
                                                <div class="carousel-item <?= $isActive ? 'active' : '' ?>">
                                                    <img src="../../../../assets/img/gallery/<?= $galleryRow['image_name'] ?>" 
                                                         class="d-block w-100" 
                                                         alt="Gallery Image" style="height: 60vh;">
                                                </div>
                                                <?php $isActive = false; ?>
                                            <?php endwhile; ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel<?= $row['gallery_name'] ?>" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel<?= $row['gallery_name'] ?>" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php $stmtGallery->close(); ?>
                            </div>

                            <!-- Add Image Form -->
                            <form action="../../function/php/add_to_gallery.php" method="POST" enctype="multipart/form-data" class="mt-4">
                                <input type="hidden" name="gallery_name" value="<?= $row['gallery_name'] ?>">
                                <div class="mb-3">
                                    <label for="gallery_image" class="form-label">Add Image to <?= $row['gallery_name'] ?> Gallery</label>
                                    <input type="file" class="form-control" id="gallery_image" name="gallery_image" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Add to Gallery</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>



            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Delete Template</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this template?
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="../../function/php/delete_template.php">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>"> 
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

    </section>




    
    <script src="../../function/script/slider-img.js"></script>
    <script src="../../function/script/pre-loadall.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    


</body>
</html>
