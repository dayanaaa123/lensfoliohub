<?php
require '../../../../db/db.php';

// Get the filter and query parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$query = isset($_GET['query']) ? $_GET['query'] : '';

if (!empty($filter) && !empty($query)) {
    $sql = "";
    
    // Create SQL query based on the selected filter
    if ($filter == 'name') {
        // Search in the 'users' table for 'name' and 'profile_img'
        $sql = "SELECT u.name, u.profile_img, a.location_text AS location, a.profession, a.price 
                FROM users u
                JOIN about_me a ON u.email = a.email
                WHERE u.role = 'supplier' AND u.name LIKE ?";
    } elseif ($filter == 'location') {
        // Search in the 'about_me' table for 'location_text'
        $sql = "SELECT u.name, u.profile_img, a.location_text AS location, a.profession, a.price 
                FROM users u
                JOIN about_me a ON u.email = a.email
                WHERE u.role = 'supplier' AND a.location_text LIKE ?";
    } elseif ($filter == 'profession') {
        // Search in the 'about_me' table for 'profession'
        $sql = "SELECT u.name, u.profile_img, a.location_text AS location, a.profession, a.price 
                FROM users u
                JOIN about_me a ON u.email = a.email
                WHERE u.role = 'supplier' AND a.profession LIKE ?";
    } elseif ($filter == 'pricing') {
        // Search in the 'about_me' table for 'price'
        $sql = "SELECT u.name, u.profile_img, a.location_text AS location, a.profession, a.price 
                FROM users u
                JOIN about_me a ON u.email = a.email
                WHERE u.role = 'supplier' AND a.price LIKE ?";
    }
    
    if ($sql != "") {
        $stmt = $conn->prepare($sql);
        $searchQuery = "%" . $query . "%"; 
        $stmt->bind_param("s", $searchQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch results and return as JSON
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();
?>
