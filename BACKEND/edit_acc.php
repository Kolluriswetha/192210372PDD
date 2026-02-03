<?php
header("Content-Type: application/json");

include "config.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "$method Method Not Allowed"]);
    exit;
}

// Validate required fields
if (!isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Missing required fields (id, name, email, password)"]);
    exit;
}

$id       = intval($_POST['id']);
$name     = $_POST['name'];
$email    = $_POST['email'];
$password = $_POST['password'];

if (empty($id) || empty($name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "All fields are required and cannot be empty"]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE create_acc SET name = ?, email = ?, password = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssi", $name, $email, $password, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["status" => 404, "message" => "No account found or no changes made"]);
    } else {
        echo json_encode(["status" => "success", "message" => "Account updated successfully"]);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => $e->getMessage()]);
}

$conn->close();
?>
