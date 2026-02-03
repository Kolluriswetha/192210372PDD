<?php
header('Content-Type: application/json');
include 'config.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($userid > 0 && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE id = ? AND userid = ?");
        $stmt->bind_param("ii", $id, $userid);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Favorite removed successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Failed to remove favorite"
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid userid or id"
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
}
