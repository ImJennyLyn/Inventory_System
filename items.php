<?php
include 'db.php';

header("Content-Type: application/json");

$sql = "SELECT id, name, description, category, quantity, unit, image, status FROM inventory";
$result = $conn->query($sql);

$items = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

echo json_encode(["items" => $items]);

$conn->close();
?>
