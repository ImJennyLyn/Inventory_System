<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$response = [];

if ($user && password_verify($password, $user['password'])) {
    $response['status'] = "success";
    $response['username'] = $user['username'];
} else {
    $response['status'] = "error";
    $response['message'] = "Invalid login";
}

echo json_encode($response);
?>


