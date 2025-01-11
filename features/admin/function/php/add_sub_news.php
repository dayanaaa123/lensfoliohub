<?php
require '../../../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $img = $_FILES['img']['name'];
    $title = $_POST['title'];
    $date = $_POST['date'];

    if ($img) {
        $img_tmp = $_FILES['img']['tmp_name'];
        $img_path = '../../../../assets/img/sub-news/' . $img;
        move_uploaded_file($img_tmp, $img_path); 
    }

    $sql = "INSERT INTO sub_news (img, title, date) VALUES ('$img', '$title', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo "Sub News added successfully!";
        header("Location: ../../web/api/announcement.php"); 
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
