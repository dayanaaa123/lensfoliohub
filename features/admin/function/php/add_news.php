<?php
require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $heading = $_POST['heading'];
    $context = $_POST['context'];
    $date = $_POST['date'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = $image; 
        move_uploaded_file($image_tmp, '../../../../assets/img/' . $image_path); 
    } else {
        $image_path = NULL;
    }

    $sql = "INSERT INTO news (heading, context, date, image) 
            VALUES ('$heading', '$context', '$date', '$image_path')";

    if ($conn->query($sql) === TRUE) {
        header("Location:../../web/api/announcement.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
