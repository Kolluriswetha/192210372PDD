<?php
header('Content-Type: application/json; charset=utf-8');
include 'config.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$response = ["name" => ""];

if ($id > 0) {
    $sql = "SELECT name FROM create_acc WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $response["name"] = $row["name"];
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>
