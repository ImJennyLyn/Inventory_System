<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

$response = [];

if ($stmt->execute()) {
    $response['status'] = "success";
} else {
    $response['status'] = "error";
    $response['message'] = $conn->error;
}

echo json_encode($response);
?>


