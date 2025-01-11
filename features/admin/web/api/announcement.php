<?php 
session_start();
require '../../../../db/db.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';


$sql = "SELECT * FROM news ORDER BY date DESC";
$result = $conn->query($sql);

$sql = "SELECT * FROM news ORDER BY date DESC LIMIT 1"; 
$results = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/index.css">
</head>

<body>
    <!--Navigation Links-->
    <div class="navbar flex-column shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <h5>LENSFOLIOHUB</h5>
            <a href="admin.php">
                <span>Dashboard</span>
            </a>
            <a href="#" class="navbar-highlight">
                <span>Announcement</span>
            </a>
            <a href="registered-client.php">
                <span>Registered Client</span>
            </a>
            <a href="registered-supplier.php">
                <span>Registered Supplier</span>
            </a>
            <a href="reports.php">
                <span>Reports</span>
            </a>
            <a href="recover.php">
                <span>Recovery</span>
            </a>
        </div>

    </div>
    </div>
    <!--Navigation Links End-->
    <div class="content flex-grow-1">
        <div class="header">
            <button class="navbar-toggler d-block d-md-none" type="button" onclick="toggleMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    style="stroke: black; fill: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>

            <!--Notification and Profile Admin-->
            <div class="profile-admin">
                <div class="dropdown">
                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../../../../assets/img/profile/profile.png"
                            style="width: 40px; height: 40px; object-fit: cover;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../../../../authentication/web/api/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        
            <div class="col-md-12 d-flex justify-content-center mx-auto">
            <div class="container supplier-reg">
               
            <div class="table-wrapper px-lg-5">
                <h2 class="text-center">Announcements</h2>
                <div class="row">
                    <div class="col-md-8 report-table">
                    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addNewsModal" style=" display: flex; margin-left: auto;">
                        Add News
                    </button>
                        <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover table-remove-borders" style="background-color: #2A2E32; color: white;">
                                <thead class="thead-light" style="background-color: #2A2E32; color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th>Img</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>heading</th>
                                        <th>context</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";  
                                            echo "<td><img src='../../../../assets/img/" . $row['image'] . "' alt='Image' style='width: 50px; height: 50px;'></td>";  
                                            echo "<td>" . $row['uploader'] . "</td>";  
                                            echo "<td>" . $row['date'] . "</td>";  
                                            echo "<td>" . $row['heading'] . "</td>";  
                                            echo "<td>" . $row['context'] . "</td>";  
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No news articles found</td></tr>";
                                    }

                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>  
                    
                    <div class="col-md-4">
                    <?php
                        if ($results->num_rows > 0) {
                            $row = $results->fetch_assoc();

                            $formatted_date = date("F j, Y", strtotime($row['date']));
                            echo "<div class='card' style='background-color: #a67b5b; color: white;'>";
                            
                            if ($row['image']) {
                                echo "<img src='../../../../assets/img/" . $row['image'] . "' class='card-img-top' alt='News Image' style='height: 200px; object-fit: cover;'>";
                            }
                    
                            echo "<div class='card-body'>";
                            

                            echo "<p class='card-text'>" . $formatted_date . "</p>";
                            
                            echo "<h5 class='card-title'>" . $row['heading'] . "</h5>";
                            echo "<p>" . $row['context'] . "</p>";
                            echo "</div>";
                            echo "</div>";
                        } else {
                            echo "<p>No news available</p>";
                        }

                        ?>
                    </div>
                    <div class="col-md-12 report-table">
                    
                        <div class="table-responsive" style="overflow-x: auto;">
                        <h2>Top 3</h2>
                        <table class="table table-hover table-remove-borders" style="background-color: #2A2E32; color: white;">
                                <thead class="thead-light" style="background-color: #2A2E32; color: white;">
                                    <tr>
                                        <th>Count</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Img</th>

                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    include '../../../../db/db.php';

                                    $sql = "
                                        SELECT supplier_email, name, AVG(CASE WHEN rating <= 5 THEN rating ELSE 5 END) AS average_rating
                                        FROM ratings
                                        GROUP BY supplier_email
                                        ORDER BY average_rating DESC
                                        LIMIT 3
                                    ";

                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                      

                                        $rank = 1; 
                                        while ($row = $result->fetch_assoc()) {
                                            $supplier_email = $row['supplier_email'];
                                            $name = $row['name'];
                                            $average_rating = $row['average_rating'];

                                            // Fetch profile image from the `users` table
                                            $users_sql = "SELECT profile_img FROM users WHERE email = '$supplier_email'";
                                            $users_result = $conn->query($users_sql);
                                            $image = $users_result->num_rows > 0 ? $users_result->fetch_assoc()['profile_img'] : 'No Image';

                                            echo '<tr>';
                                            echo '<td>' . $average_rating . '</td>';
                                            echo '<td>' . htmlspecialchars($name) . '</td>';
                                            echo '<td>' . htmlspecialchars($supplier_email) . '</td>';
                                            echo '<td><img src="../../../../assets/img/profile/' . $image . '" alt="Image" style="width: 50px; height: 50px;"></td>';
                                            echo '</tr>';

                                            $rank++; 
                                        }

                                        echo '</tbody>';
                                        echo '</table>';
                                    } else {
                                        echo '<p>No ratings available.</p>';
                                    }
                                    ?>


                                    
                                </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="col-md-12 report-table">
                    
                        <div class="table-responsive" style="overflow-x: auto;">
                        <h2>Sub News</h2>
                        <button class=" btn btn-primary d-flex mb-2" style="margin-left: auto" data-bs-toggle="modal" data-bs-target="#addSubNewsModal">Add Sub News</button>
                        <?php
                            require '../../../../db/db.php';

                            $sql = "SELECT * FROM sub_news ORDER BY date DESC";
                            $result = $conn->query($sql);

                            echo "<table class='table table-hover table-remove-borders' style='background-color: #2A2E32; color: white;'>";
                            echo "<thead class='thead-light' style='background-color: #2A2E32; color: white;'>";
                            echo "<tr>";
                            echo "<th>Img</th>";
                            echo "<th>Date</th>";
                            echo "<th>Title</th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $formatted_date = date("M j, Y", strtotime($row['date'])); 
                                    echo "<tr>";
                                    echo "<td><img src='../../../../assets/img/sub-news/" . $row['img'] . "' alt='Image' style='width: 50px; height: 50px;'></td>";
                                    echo "<td>" . $formatted_date . "</td>";
                                    echo "<td>" . $row['title'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No records found</td></tr>";
                            }

                            echo "</tbody>";
                            echo "</table>";

                            $conn->close();
                        ?>

                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsModalLabel">Add News</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNewsForm" action="../../function/php/add_news.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="heading" class="form-label">Heading</label>
                        <input type="text" class="form-control" id="heading" name="heading" required>
                    </div>
                    <div class="mb-3">
                        <label for="context" class="form-label">Context</label>
                        <textarea class="form-control" id="context" name="context" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save News</button>
                    </form>
                </div>
                </div>
            </div>
            </div>

            <div class="modal fade" id="addSubNewsModal" tabindex="-1" aria-labelledby="addSubNewsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubNewsModalLabel">Add Sub News</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../../function/php/add_sub_news.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="img" class="form-label">Image</label>
                            <input type="file" class="form-control" id="img" name="img" required>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Sub News</button>
                        </form>
                    </div>
                    </div>
                </div>
                </div>

        <script src="../../function/script/supplier-deletion.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../function/script/toggle_menu.js"></script>

</body>

</html>