<?php
header('Content-Type: application/json');
include "config.php"; // connection file

if (isset($_POST['userid'], $_POST['title'], $_POST['content'])) {
    $userid = intval($_POST['userid']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $query = "INSERT INTO favorites (userid, title, content, created_at) 
              VALUES ('$userid', '$title', '$content', NOW())";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Favorite added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing userid, title, or content"]);
}
?>
