<?php
include 'config.php'; // your connection file

$id = $_POST['id'];
$name = $_POST['name'];

$response = array();

if (!empty($id) && !empty($name)) {
    $sql = "UPDATE create_acc SET name='$name' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        $response['status'] = 'success';
        $response['message'] = 'Name updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Database update failed';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid input';
}

echo json_encode($response);
?>
