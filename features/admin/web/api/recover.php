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
    <title>Recovery | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
</head>

<body>
    <div class="navbar flex-column shadow-sm p-3 collapse d-md-flex" id="navbar">
        <div class="navbar-links">
            <h5>LENSFOLIOHUB</h5>
            <a href="admin.php"><span>Dashboard</span></a>
            <a href="announcement.php"><span>Announcement</span></a>
            <a href="registered-client.php"><span>Registered Client</span></a>
            <a href="registered-supplier.php"><span>Registered Supplier</span></a>
            <a href="reports.php"><span>Reports</span></a>
            <a href="#" class="navbar-highlight"><span>Recovery</span></a>
            <a href="../../../../authentication/web/api/logout.php" style="margin-top: 25vh;"><span>Logout</span></a>
        </div>
    </div>
    
    <div class="content flex-grow-1">
        <div class="header">
            <button class="navbar-toggler d-block d-md-none" type="button" onclick="toggleMenu()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="stroke: black; fill: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
        
        <div class="container supplier-reg">
            <div class="table-wrapper px-lg-5">
                <h2 class="text-center">Recovery</h2>
                <div class="table-responsive" style="overflow-x: auto; height: 72vh;">
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
                            $sql = "SELECT rr.email, rr.recovery_reason, rr.request_date, rr.status, MAX(r.reported_email) as reported_email 
                                    FROM recovery_requests rr 
                                    LEFT JOIN reports r ON rr.email = r.reported_email 
                                    GROUP BY rr.email, rr.recovery_reason, rr.request_date, rr.status";
                            $result_recovery = $conn->query($sql);
                            
                            $counter = 1; 
                            
                            if ($result_recovery->num_rows > 0) {
                                while ($row = $result_recovery->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . $counter++ . "</td>
                                            <td>" . htmlspecialchars($row['email']) . "</td>
                                            <td>" . htmlspecialchars($row['recovery_reason']) . "</td>
                                            <td>" . $row['request_date'] . "</td>
                                            <td>" . $row['status'] . "</td>
                                            <td>
                                                <button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#unbanModal_" . $row['email'] . "'>Unban</button>
                                                <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#declineModal_" . $row['email'] . "'>Decline</button>
                                            </td>
                                        </tr>";
                                    
                                    echo "<div class='modal fade' id='unbanModal_" . $row['email'] . "' tabindex='-1'>
                                            <div class='modal-dialog modal-dialog-centered'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h5 class='modal-title'>Confirm Unban</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        Are you sure you want to unban " . htmlspecialchars($row['email']) . "?
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                        <form method='GET' action='../../function/php/unban_user.php'>
                                                            <input type='hidden' name='email' value='" . htmlspecialchars($row['email']) . "'>
                                                            <button type='submit' class='btn btn-success'>Yes, Unban</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No recovery requests found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../function/script/toggle_menu.js"></script>
</body>
</html>
