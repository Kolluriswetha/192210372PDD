<?php
header('Content-Type: application/json');
include "config.php"; // your DB connection

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if ($title && $content) {
    $stmt = $conn->prepare("INSERT INTO favorites (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Favorite saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save favorite"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing title or content"]);
}
$conn->close();
?>
