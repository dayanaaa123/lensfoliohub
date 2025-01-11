<?php
$servername = "localhost";  
$username = "u373116035_diana";         
$password = "#Bakitako23";             
$dbname = "u373116035_LENSFOLIOHUB";   


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
