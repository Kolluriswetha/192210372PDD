<?php
header("Content-Type: application/json");
include "config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "Only POST method allowed"]);
    exit;
}

// Validate input
if (!isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Missing required fields"]);
    exit;
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

if (empty($name) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Fields cannot be empty"]);
    exit;
}

try {
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM create_acc WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO create_acc (name, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $name, $email, $password);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $newUserId = $stmt->insert_id; // ✅ Get the inserted user's ID
    $stmt->close();

    // ✅ Return userid in response
    echo json_encode([
        "status" => "success",
        "message" => "Account created successfully",
        "user" => [
            "id" => $newUserId,
            "name" => $name,
            "email" => $email
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => 500, "message" => $e->getMessage()]);
}

$conn->close();
?>
