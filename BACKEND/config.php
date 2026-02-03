<?php
// Database configuration
$host = "127.0.0.1";
$port = 3307; // Custom MySQL port
$user = "root";
$pass = "";
$db   = "magic_words";

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create mysqli connection
    $conn = new mysqli($host, $user, $pass, $db, $port);

    // Set charset
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error"   => $e->getMessage()
    ]);
    exit;
}
?>
