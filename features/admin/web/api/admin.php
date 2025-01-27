<?php 
session_start();
require '../../../../db/db.php';

$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$totalClientsQuery = "SELECT COUNT(*) AS total_clients FROM users WHERE role = 'customer'";
$totalClientsResult = $conn->query($totalClientsQuery);
$totalClients = $totalClientsResult->fetch_assoc()['total_clients'];

$newUsersQuery = "SELECT COUNT(*) AS new_users_this_week FROM users WHERE (role = 'customer' OR role = 'supplier') AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
$newUsersResult = $conn->query($newUsersQuery);
$newUsers = $newUsersResult->fetch_assoc()['new_users_this_week']; 

$totalSuppliersQuery = "SELECT COUNT(*) AS total_suppliers FROM users WHERE role = 'supplier'";
$totalSuppliersResult = $conn->query($totalSuppliersQuery);
$totalSuppliers = $totalSuppliersResult->fetch_assoc()['total_suppliers'];

$supplierData = array_fill(0, 12, 0); 
$customerData = array_fill(0, 12, 0); 


$supplierData = array_fill(0, 12, 0); 
$customerData = array_fill(0, 12, 0); 


$sql = "SELECT MONTH(created_at) AS month, COUNT(*) AS user_count, role 
        FROM users 
        WHERE role IN ('customer', 'supplier') 
        GROUP BY MONTH(created_at), role
        ORDER BY MONTH(created_at)";
$result = $conn->query($sql);


while ($row = $result->fetch_assoc()) {
    if ($row['role'] === 'supplier') {
        $supplierData[$row['month'] - 1] = $row['user_count']; 
    } elseif ($row['role'] === 'customer') {
        $customerData[$row['month'] - 1] = $row['user_count']; 
    }
}
$customerQuery = "SELECT COUNT(*) AS customer_count FROM users WHERE role = 'customer'";
$supplierQuery = "SELECT COUNT(*) AS supplier_count FROM users WHERE role = 'supplier'";

$customerResult = $conn->query($customerQuery);
$supplierResult = $conn->query($supplierQuery);

$customerCount = $customerResult->fetch_assoc()['customer_count'];
$supplierCount = $supplierResult->fetch_assoc()['supplier_count'];

// Pass data to JavaScript
$chartData = [
    'customer' => $customerCount,
    'supplier' => $supplierCount,
];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin</title>
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
            <a href="#" class="navbar-highlight">
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
            <a href="recover.php">
                <span>Recovery</span>
            </a>
            <a href="../../../../authentication/web/api/logout.php" style="margin-top: 25vh;">
                <span>Logout</span>
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
            
        </div>
        <div class="dashboard mt-4">
            <h3>Dashboard</h3>
            <div class="container total">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card total-users">
                            <div class="card-body d-flex userss">  
                                <div class="d-flex gap-2">
                                    <i class="fas fa-user d-flex align-items-center"></i>
                                </div>
                                <div class="total-number">
                                    <p class="total-num"><?php echo $totalClients; ?></p>
                                    <p class="total-title">Client Users</p>
                                </div>
                            </div>
                            <div class="line"><hr></div>
                            <div class="card-body d-flex plus-users">  
                                <div class="d-flex gap-2">
                                <i class="fas fa-plus d-flex align-items-center"></i>
                                </div>
                                <div class="total-number">
                                    <p class="total-num"><?php echo $newUsers; ?></p>
                                    <p class="total-title">New Users</p>
                                </div>
                            </div> 
                            <div class="line"><hr></div>  
                            <div class="card-body d-flex plus-users">  
                                <div class="d-flex gap-2">
                                <i class="fas fa-box d-flex align-items-center"></i>
                                </div>
                                <div class="total-number">
                                    <p class="total-num"><?php echo $totalSuppliers; ?></p>
                                    <p class="total-title">Total Suppliers</p>
                                </div>
                            </div>                   
                        </div>
                            <div class="chart-container mt-3" style="position: relative; height: 300px; width: 100%;">
                                    <h5 class="chart-title">Total Users Pie Chart</h5>
                                    <canvas id="pieChart" style="max-height: 300px;"></canvas>
                                    <script>
                                     document.addEventListener("DOMContentLoaded", function () {
    var ctx = document.getElementById('pieChart').getContext('2d');

    var chartData = <?php echo json_encode($chartData); ?>;

    var data = {
        labels: ['Customer', 'Supplier'],
        datasets: [{
            label: 'User Distribution',
            data: [chartData.customer, chartData.supplier],
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)', // Customer color
                'rgba(255, 159, 64, 0.6)'  // Supplier color
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };

    new Chart(ctx, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            var label = context.label || '';
                            var value = context.raw;
                            var total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                            var percentage = ((value / total) * 100).toFixed(2);

                            if (label) {
                                label += ': ';
                            }
                            label += value + ' users (' + percentage + '%)';
                            return label;
                        }
                    }
                },
                legend: {
                    display: true,
                    labels: {
                        color: 'black'
                    }
                },
                datalabels: { // Configuration for displaying percentages on the chart
                    color: 'white',
                    formatter: (value, context) => {
                        var total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                        var percentage = ((value / total) * 100).toFixed(2);
                        return percentage + '%';
                    },
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // Register the datalabels plugin
    });
});


                                    </script>
                                </div>
                    </div>
                    <div class="col-md-9">
                        <div class="chart-container">
                            <h5 class="chart-title">Total Users Line Graph</h5>
                            <canvas id="lineChart"></canvas>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    var ctx = document.getElementById('lineChart').getContext('2d');

                                    var supplierData = <?php echo json_encode($supplierData); ?>;
                                    var customerData = <?php echo json_encode($customerData); ?>;

                                    var chartData = {
                                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                        datasets: [
                                            {
                                                label: 'Supplier Users',
                                                data: supplierData,
                                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 2,
                                                fill: true,
                                                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                                                pointBorderColor: '#fff',
                                                pointHoverBackgroundColor: '#fff',
                                                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                                            },
                                            {
                                                label: 'Client Users',
                                                data: customerData,
                                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                                borderColor: 'rgba(255, 99, 132, 1)',
                                                borderWidth: 2,
                                                fill: true,
                                                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                                                pointBorderColor: '#fff',
                                                pointHoverBackgroundColor: '#fff',
                                                pointHoverBorderColor: 'rgba(255, 99, 132, 1)'
                                            }
                                        ]
                                    };

                                    new Chart(ctx, {
                                        type: 'line',
                                        data: chartData,
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    max: 500,
                                                    ticks: {
                                                        color: 'white',
                                                        callback: function (value) {
                                                            return value + ' users';
                                                        }
                                                    },
                                                    grid: {
                                                        color: 'rgba(255, 255, 255, 0.2)'
                                                    }
                                                },
                                                x: {
                                                    ticks: {
                                                        color: 'white'
                                                    },
                                                    grid: {
                                                        color: 'rgba(255, 255, 255, 0.2)'
                                                    }
                                                }
                                            },
                                            plugins: {
                                                tooltip: {
                                                    callbacks: {
                                                        label: function (context) {
                                                            var label = context.dataset.label || '';
                                                            if (label) {
                                                                label += ': ';
                                                            }
                                                            label += context.raw + ' users';
                                                            return label;
                                                        }
                                                    },
                                                    bodyColor: 'white',
                                                    titleColor: 'white',
                                                    backgroundColor: 'rgba(0, 0, 0, 0.7)'
                                                },
                                                legend: {
                                                    labels: {
                                                        color: 'white'
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="row d-flex justify-content-center mt-4">
                        <?php
                            include '../../../../db/db.php';

                            $sql = "
                                SELECT supplier_email, name, AVG(CASE WHEN rating <= 5 THEN rating ELSE 5 END) AS average_rating
                                FROM ratings
                                GROUP BY supplier_email
                                ORDER BY average_rating DESC
                                LIMIT 5
                            ";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                echo '<div class="col-md-6">';
                                echo '<div class="card total-users">';
                                echo '<div class="card-body">';
                                echo '<p class="text-center text-black">Top 5 Suppliers with Highest Average Ratings</p>';
                                echo '<hr class="mt-2">';
                                echo '<ul class="list-group list-group-flush">';

                                $rank = 1; 
                                while ($row = $result->fetch_assoc()) {
                                    $supplier_email = $row['supplier_email'];
                                    $name = $row['name'];
                                    $average_rating = $row['average_rating']; 

                                    $medal = '';
                                    if ($rank == 1) {
                                        $medal = '<img src="../../../../assets/img/medal/gold.png" alt="Gold Medal" style="width: 40px; height: 40px;">';
                                    } elseif ($rank == 2) {
                                        $medal = '<img src="../../../../assets/img/medal/silver.png" alt="Silver Medal" style="width: 40px; height: 40px;">';
                                    } elseif ($rank == 3) {
                                        $medal = '<img src="../../../../assets/img/medal/bronze.png" alt="Bronze Medal" style="width: 40px; height: 40px;">';
                                    }

                                    echo '<li class="list-group-item">';
                                    echo '<div class="d-flex justify-content-between align-items-center">'; 
                                    echo '<div>' . $medal . '</div>'; 
                                    echo '<div class="d-flex justify-content-between w-100">'; 
                                    echo '<div class="">';
                                    echo '<p class="mb-0">' . htmlspecialchars($name) . '</p>';
                                    echo '<p>' . htmlspecialchars($supplier_email) . '</p>';
                                    echo '</div>';
                                    echo '<p class="mb-0 mt-3">' . htmlspecialchars(number_format($average_rating, 1)) . ' <i class="fas fa-star" style="color: yellow;"></i></p>'; // Formatting to 1 decimal place
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</li>';

                                    $rank++; 
                                }

                                echo '</ul>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p>No ratings available.</p>';
                            }
                            ?>



                            <?php
                            include '../../../../db/db.php';

                            // Query to fetch the 5 lowest-rated suppliers based on average rating
                            $sql = "
                                SELECT supplier_email, name, AVG(rating) AS average_rating
                                FROM ratings
                                GROUP BY supplier_email
                                ORDER BY average_rating ASC
                                LIMIT 5
                            ";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                echo '<div class="col-md-6">';
                                echo '<div class="card total-users">';
                                echo '<div class="card-body">';
                                echo '<p class="text-center text-black">Top 5 Lowest Rated Suppliers</p>';
                                echo '<hr class="mt-2">';
                                echo '<ul class="list-group list-group-flush">';

                                $rank = 1; 
                                while ($row = $result->fetch_assoc()) {
                                    $supplier_email = $row['supplier_email'];
                                    $name = $row['name'];
                                    $average_rating = min(5, $row['average_rating']);  // Ensures no rating exceeds 5

                                    echo '<li class="list-group-item">';
                                    echo '<div class="d-flex justify-content-between align-items-center">'; 
                                    echo '<div>' . $medal . '</div>'; 
                                    echo '<div class="d-flex justify-content-between w-100">'; 
                                    echo '<div class="">';
                                    echo '<p class="mb-0">' . htmlspecialchars($name) . '</p>';
                                    echo '<p>' . htmlspecialchars($supplier_email) . '</p>';
                                    echo '</div>';
                                    echo '<p class="mb-0 mt-3">' . htmlspecialchars(number_format($average_rating, 1)) . ' <i class="fas fa-star" style="color: yellow;"></i></p>'; // Format to 1 decimal place and capping at 5
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</li>';

                                    $rank++;
                                }

                                echo '</ul>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p>No ratings available.</p>';
                            }
                            ?>


                        </div>
                    </div>
                  


                
                </div>
            </div>
        
            <div class="col-md-12 d-flex justify-content-center mx-auto">
            <div class="container supplier-reg">
                <h5>SUPPLIERS REGISTRATION REQUEST</h5>
                <?php 
                    include '../../../../db/db.php';
                    $query = "SELECT * FROM users WHERE role = 'supplier' AND verify_status = 2";
                    $result = $conn->query($query);
                ?>
                <div class="table-wrapper px-lg-5">
                <table class="table table-hover table-remove-borders" style="background-color: #2A2E32; color: white;">
                    <thead class="thead-light" style="background-color: #2A2E32; color: white;">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if ($result->num_rows > 0): ?>
                            <?php $counter = 1;?>
                            <?php while ($user = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++;?></td>
                                    <td><?php echo $user['name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td class="d-flex gap-1 justify-content-center">
                                    <form method="POST" action="../../function/php/supplier-req.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="accept" class="btn btn-success">Accept</button>
                                    </form>
                                    <form method="POST" action="../../function/php/supplier-req.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                    </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No inactive suppliers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
            </div>
        </div>

        

        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="../../function/script/toggle_menu.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</body>

</html>