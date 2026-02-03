<?php
header("Content-Type: application/json");

include "config.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "$method Method Not Allowed"]);
    exit;
}

// Check required field
if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Missing 'id' parameter"]);
    exit;
}

$id = intval($_POST['id']);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Invalid user ID"]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM create_acc WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "User not found or already deleted"]);
        exit;
    }

    echo json_encode(["status" => "success", "message" => "Account deleted successfully"]);
    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => $e->getMessage()]);
}

$conn->close();
?>
