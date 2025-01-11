<?php
require '../../../../db/db.php';

$query = "
    SELECT 
        users.*, 
        about_me.about_me, 
        about_me.profession, 
        about_me.age
    FROM 
        users 
    LEFT JOIN 
        about_me 
    ON 
        users.email = about_me.email
    WHERE 
        users.role = 'supplier'
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>