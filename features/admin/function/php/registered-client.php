<?php
require '../../../../db/db.php';

$query = "SELECT * FROM users WHERE role = 'customer'";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
