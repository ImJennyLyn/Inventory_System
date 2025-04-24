<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];
    $stock = $_POST['stock'];
    $unit = $_POST['unit'];

    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $imageName);
    }

    $sql = "INSERT INTO inventory (name, description, category, quantity, unit, image)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $name, $desc, $cat, $stock, $unit, $imageName);
    $stmt->execute();

    echo "Item added successfully.";
}
?>
