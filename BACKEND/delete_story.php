<?php
header('Content-Type: application/json');
include "config.php"; // $conn must be defined here

$response = ["success" => false, "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $userid = $_POST['userid'] ?? null;

    if ($id && $userid) {
        $id = intval($id);
        $userid = intval($userid);

        // ✅ Change table & column names as per your DB
        $stmt = $conn->prepare("DELETE FROM generate_story WHERE id = ? AND userid = ?");
        $stmt->bind_param("ii", $id, $userid);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response["success"] = true;
                $response["message"] = "Story deleted successfully";
            } else {
                $response["message"] = "No story found with that ID for this user";
            }
        } else {
            $response["message"] = "Query failed: " . $conn->error;
        }
        $stmt->close();
    } else {
        $response["message"] = "Story ID or User ID not provided";
    }
} else {
    $response["message"] = "Invalid request method";
}

echo json_encode($response);
?>
