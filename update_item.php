<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];
    $unit = $_POST['unit'];

    $imageName = '';

    // Check if new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $imageName);

        // Update including image
        $sql = "UPDATE inventory SET name = ?, description = ?, category = ?, quantity = ?, unit = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissi", $name, $desc, $cat, $stock, $unit, $imageName, $id);
    } else {
        // Update without changing image
        $sql = "UPDATE inventory SET name = ?, description = ?, category = ?, quantity = ?, unit = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisi", $name, $desc, $cat, $stock, $unit, $id);
    }

    if ($stmt->execute()) {
        echo "Item updated successfully.";
    } else {
        echo "Error updating item: " . $conn->error;
    }
}
?>
