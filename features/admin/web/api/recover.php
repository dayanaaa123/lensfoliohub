<?php 
session_start();
require '../../../../db/db.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$sql_customers = "SELECT * FROM reports WHERE role = 'customer'";
$result_customers = $conn->query($sql_customers);

$sql_suppliers = "SELECT * FROM reports WHERE role = 'supplier'";
$result_suppliers = $conn->query($sql_suppliers);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Suppliers | Admin</title>
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
            <a href="announcement.php">
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
            <a href="#" class="navbar-highlight">
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
                <h2 class="text-center">Recovery</h2>

                <div class="row">
                    <div class="col-md-12 report-table" style="height: 72vh;">
                        <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-hover table-remove-borders" style="background-color: #2A2E32; color: white;">
                            <thead class="thead-light" style="background-color: #2A2E32; color: white;">
                                <tr>
                                    <th>#</th>
                                    <th>Email</th>
                                    <th>Recovery Reason</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT rr.email, rr.recovery_reason, rr.request_date, rr.status, r.reported_email 
                                FROM recovery_requests rr 
                                LEFT JOIN reports r ON rr.email = r.reported_email";
                                $result_recovery = $conn->query($sql);

                                $counter = 1; 

                                if ($result_recovery->num_rows > 0) {
                                    while ($row = $result_recovery->fetch_assoc()) {
                                        echo "<tr>
                                                <td>" . $counter++ . "</td> <!-- Incremented ID -->
                                                <td>" . htmlspecialchars($row['email']) . "</td>
                                                <td>" . htmlspecialchars($row['recovery_reason']) . "</td>
                                                <td>" . $row['request_date'] . "</td>
                                                <td>" . $row['status'] . "</td>
                                                <td>
                                                    <!-- Unban Button -->
                                                    <button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#unbanModal_" . $row['reported_email'] . "'>Unban</button>
                                                    <!-- Decline Button -->
                                                    <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#declineModal_" . $row['email'] . "'>Decline</button>
                                                </td>
                                            </tr>";

                                        // Unban Modal
                                        echo "<div class='modal fade' id='unbanModal_" . $row['reported_email'] . "' tabindex='-1' aria-labelledby='unbanModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title' id='unbanModalLabel'>Confirm Unban</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    Are you sure you want to unban the account associated with " . htmlspecialchars($row['reported_email']) . "?<br>
                                                    <strong>Debugging:</strong> Reported Email: " . htmlspecialchars($row['reported_email']) . "
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                    <!-- Unban Form -->
                                                    <form method='GET' action='../../function/php/unban_user.php'>
                                                        <input type='hidden' name='email' value='" . htmlspecialchars($row['reported_email']) . "'>
                                                        <button type='submit' class='btn btn-success'>Yes, Unban</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";

                                        // Decline Modal
                                        echo "<div class='modal fade' id='declineModal_" . $row['email'] . "' tabindex='-1' aria-labelledby='declineModalLabel' aria-hidden='true'>
                                                <div class='modal-dialog modal-dialog-centered'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <h5 class='modal-title' id='declineModalLabel'>Confirm Decline</h5>
                                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                        </div>
                                                        <div class='modal-body'>
                                                            Are you sure you want to decline the recovery request for the account associated with " . htmlspecialchars($row['email']) . "?
                                                        </div>
                                                        <div class='modal-footer'>
                                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                            <a href='../../function/php/decline_recovery.php?email=" . urlencode($row['reported_email']) . "' class='btn btn-danger'>Yes, Decline</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No recovery requests found.</td></tr>";
                                }
                                ?>
                            </tbody>

                        </table>


                       

                        </div>
                    </div>
                   
     

       

    

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../function/script/toggle_menu.js"></script>
</body>

</html>