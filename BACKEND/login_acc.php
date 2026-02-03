<?php
session_start(); // Start PHP session

header("Content-Type: application/json");
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => 405, "message" => "Only POST method allowed"]);
    exit;
}

if (!isset($_POST['email'], $_POST['password'])) {
    echo json_encode(["status" => 400, "message" => "Email or password missing"]);
    exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Use prepared statements instead of raw query to prevent SQL injection
$stmt = $conn->prepare("SELECT id, name, email, password FROM create_acc WHERE (email = ? OR name = ?) LIMIT 1");
if (!$stmt) {
    echo json_encode(["status" => 500, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password (assuming password stored as plain text - better to hash in production)
    if ($password === $user['password']) {
        // Login successful - set session user id
        $_SESSION['userid'] = $user['id'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
             "userid" => $user['id'], 
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email']
            ]
        ]);
    } else {
        // Password mismatch
        echo json_encode(["status" => 401, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => 401, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
