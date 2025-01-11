<?php 
session_start();
require '../../../../db/db.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Client | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../css/index.css">
</head>

<body style="min-height: 100vh;">
    <!--Navigation Links-->
    <div class="navbar flex-column shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <h5>LENSFOLIOHUB</h5>
            <a href="admin.php">
                <span>Dashboard</span>
            </a>
            <a href="announcement.php">
                <span>Announcement</span>
            </a>
            <a href="registered-client.php" class="navbar-highlight">
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
                <h2 class="text-center">Registered Client</h2>
                <?php 
                include '../../function/php/registered-client.php';
                ?>
            <div class="table-wrapper px-lg-5">

                <table class="table table-hover table-remove-borders" style="background-color: #2A2E32; color: white;">
                    <thead class="thead-light" style="background-color: #2A2E32; color: white;">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Last Login</th> 
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr data-bs-toggle="modal" data-bs-target="#viewUserModal_<?php echo $user['id']; ?>" style="cursor: pointer;">
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php 
                                            echo $user['last_login'] ? date('Y-m-d H:i:s', strtotime($user['last_login'])) : 'Never';
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo ($user['disable_status'] == 0) ? 'disabled' : 'active'; 
                                        ?>
                                    </td>
                                    <td>
                                        <!-- Delete Button -->
                                        <button class="btn btn-warning text-white  delete-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmDeleteModal" 
                                                data-id="<?php echo $user['id']; ?>">
                                            Disable
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="viewUserModal_<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="viewUserModalLabel_<?php echo $user['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewUserModalLabel_<?php echo $user['id']; ?>">User Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <img src="../../../../assets/img/profile/<?php echo htmlspecialchars($user['profile_img']); ?>" alt="Profile Picture" class="img-fluid" style="max-width: 100%; height: 30vh;">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                                            <p><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?> Yrs Old.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No suppliers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>


                </table>
            </div>
            </div>
        </div>

        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteLabel">Disable Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to disable this client?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteForm" method="POST" action="../../function/php/delete-client.php">
                            <input type="hidden" name="id" id="deleteId">
                            <button type="submit" class="btn btn-warning">Disable</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        </div>
        <script src="../../function/script/supplier-deletion.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../function/script/toggle_menu.js"></script>
</body>

</html>