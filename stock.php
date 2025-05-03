<?php
include 'db.php';
// Handle API endpoints
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'items') {
  $result = $conn->query("SELECT id, name FROM inventory");
  $items = [];
  while ($row = $result->fetch_assoc()) {
    $items[] = $row;
  }
  echo json_encode($items);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);
  $itemId = $conn->real_escape_string($data['item_id']);
  $type = $conn->real_escape_string($data['type']);
  $quantity = (int)$data['quantity'];
  $note = $conn->real_escape_string($data['note']);

  // Check if the item_id exists in the inventory table
  $checkItemQuery = "SELECT id FROM inventory WHERE id = '$itemId'";
  $checkResult = $conn->query($checkItemQuery);

  if ($checkResult->num_rows > 0) {
    // Item exists, proceed with the insertion
    $sql = "INSERT INTO stock (item_id, type, quantity, note) VALUES ('$itemId', '$type', '$quantity', '$note')";
    if ($conn->query($sql)) {
      echo json_encode(['message' => 'Stock recorded successfully.']);
    } else {
      echo json_encode(['message' => 'Error: ' . $conn->error]);
    }
  } else {
    // Item doesn't exist
    echo json_encode(['message' => 'Error: The specified item_id does not exist in the inventory table.']);
  }
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Stock In/Out</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
         .content {
      margin-left: 260px; 
      padding: 20px;
      transition: margin-left 0.3s ease; 
    }
    .content.full-width {
      margin-left: 3rem; 
    }
  </style>
</head>
<body>
<div id="sidebar-placeholder"></div>

  <div id="app">
    <div class="content">
      <h2>Stock In / Stock Out</h2>
      <form @submit.prevent="submitStock">
        <label>Item:</label>
        <select v-model="itemId">
          <option v-for="item in items" :value="item.id">{{ item.name }}</option>
        </select><br><br>

        <label>Type:</label>
        <select v-model="type">
          <option value="in">Stock In</option>
          <option value="out">Stock Out</option>
        </select><br><br>

        <label>Quantity:</label>
        <input type="number" v-model="quantity" required /><br><br>

        <label>Note:</label>
        <input v-model="note" /><br><br>

        <button type="submit">Submit</button>
      </form>
    </div>
  </div>

  <script>
    // Load the sidebar content from an external file
    fetch('sidebar.html')
      .then(res => res.text())
      .then(data => {
        document.getElementById('sidebar-placeholder').innerHTML = data;

        // Sidebar toggle logic
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content'); // Content container

        toggleBtn.addEventListener('click', () => {
          sidebar.classList.toggle('collapsed'); // Toggle the collapsed state for the sidebar
          content.classList.toggle('full-width'); // Adjust the content width based on the sidebar state

          // Toggle icon for collapsed state
          if (sidebar.classList.contains('collapsed')) {
            toggleBtn.innerHTML = '☰';  // Hamburger icon for collapsed sidebar
          } else {
            toggleBtn.innerHTML = '×';  // Close icon for expanded sidebar
          }
        });
      });

    const { createApp } = Vue;
    createApp({
      data() {
        return {
          itemId: '',
          type: 'in',
          quantity: '',
          note: '',
          items: []
        };
      },
      created() {
        fetch('stock.php?action=items')
          .then(res => res.json())
          .then(data => {
            this.items = data;
          });
      },
      methods: {
        submitStock() {
          fetch('stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              item_id: this.itemId,
              type: this.type,
              quantity: this.quantity,
              note: this.note
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.message.includes('successfully')) {
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonText: 'OK'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonText: 'Try Again'
              });
            }
          });
        }
      }
    }).mount('#app');
  </script>
</body>
</html>
