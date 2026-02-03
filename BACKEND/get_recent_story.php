<?php
header('Content-Type: application/json; charset=UTF-8');
include __DIR__ . '/config.php';   // Load database connection

$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;

if ($userid <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user id"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT tone, content, genre 
                            FROM generate_story 
                            WHERE userid = ? 
                            ORDER BY created_at DESC 
                            LIMIT 1");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $story = $result->fetch_assoc();

    if ($story) {
        echo json_encode(["success" => true, "story" => $story]);
    } else {
        echo json_encode(["success" => false, "message" => "No stories yet"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
