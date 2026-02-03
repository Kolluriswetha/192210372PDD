<?php
header('Content-Type: application/json');
include "config.php"; // DB connection

$userid = $_POST['userid'] ?? null;

if (!$userid) {
    echo json_encode(["success" => false, "message" => "User ID is required"]);
    exit;
}

$sql = "SELECT id, story_words AS title, content, created_at 
        FROM generate_story
        WHERE userid = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

$stories = [];
while ($row = $result->fetch_assoc()) {
    // Make sure null values don’t break Android parsing
    $stories[] = [ 
        "id" => (int)$row["id"],
        "title" => $row["title"] ?? "",
        "content" => $row["content"] ?? "",
        "created_at" => $row["created_at"] ?? ""
    ];
}

echo json_encode([
    "success" => true,
    "stories" => $stories
]);

$stmt->close();
$conn->close();
?>
