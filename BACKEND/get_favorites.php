<?php
header('Content-Type: application/json');
include "config.php";

if (isset($_GET['userid'])) {
    $userid = intval($_GET['userid']);
    $query = "SELECT id, title, content, created_at FROM favorites WHERE userid = '$userid' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    $favorites = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $favorites[] = $row;
    }

    echo json_encode($favorites);
} else {
    echo json_encode([]);
}
?>
