<?php 
include 'db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");

$uploadDir = 'uploads/'; // NOT ../uploads/ because this runs in the API folder

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true); // Make sure folder exists
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
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
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $cat = $_POST['category'];
        $stock = $_POST['stock'];
        $unit = $_POST['unit'];
        $imageName = '';

        // Upload image if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $originalName = basename($_FILES['image']['name']);
            $imageName = uniqid() . '_' . $originalName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        }

        $sql = "INSERT INTO inventory (name, description, category, quantity, unit, image, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Available')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $name, $desc, $cat, $stock, $unit, $imageName);
        $stmt->execute();

        echo "Item added successfully.";

    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $desc = $_POST['description'];
        $cat = $_POST['category'];
        $stock = $_POST['stock'];
        $unit = $_POST['unit'];

        // Handle image if updated
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $originalName = basename($_FILES['image']['name']);
            $imageName = uniqid() . '_' . $originalName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);

            $sql = "UPDATE inventory SET name = ?, description = ?, category = ?, quantity = ?, unit = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissi", $name, $desc, $cat, $stock, $unit, $imageName, $id);
        } else {
            $sql = "UPDATE inventory SET name = ?, description = ?, category = ?, quantity = ?, unit = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssisi", $name, $desc, $cat, $stock, $unit, $id);
        }

        $stmt->execute();
        echo "Item updated successfully.";

    } elseif ($action === 'delete') {
        $id = $_POST['id'];

        // Optional: delete image file (if needed)
        $getOld = $conn->prepare("SELECT image FROM inventory WHERE id = ?");
        $getOld->bind_param("i", $id);
        $getOld->execute();
        $result = $getOld->get_result();
        if ($row = $result->fetch_assoc()) {
            $oldImage = $row['image'];
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }

        $sql = "DELETE FROM inventory WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo "Item deleted successfully.";
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Inventory</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .body {
     background-color: rgb(255, 255, 255);

    }
      .content {
      margin-left: 260px; /* Default margin when sidebar is expanded */
      padding: 20px;
      transition: margin-left 0.3s ease; /* Smooth transition */


    }
    .content.full-width {
      margin-left: 3rem; /* When sidebar is collapsed, content takes full width */

    }

    table { width: 100%; 
      border-collapse: collapse;
       margin-top: 20px;
       color: black;
     }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background-color:rgb(255, 255, 255); }
    input, button { margin: 5px; padding: 6px; }
  </style>
</head>
<body >
  <div id="sidebar-placeholder"></div>

<div id="app">
  <!-- Add/Edit Modal -->
  <div v-if="showModal" style="position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center;">
    <div style="background:white; padding:20px; border-radius:10px; width:400px;">
      <h3>{{ isEditing ? 'Edit Item' : 'Add New Item' }}</h3>
      <form @submit.prevent="submitForm">
        <label>Name:</label>
        <input type="text" v-model="form.name" required><br>
        <label>Description:</label>
        <input type="text" v-model="form.description"><br>
        <label>Category:</label>
        <input type="text" v-model="form.category"><br>
        <label>Stock:</label>
        <input type="number" v-model="form.stock" required><br>
        <label>Unit:</label>
        <input type="text" v-model="form.unit"><br>
        <label>Image:</label>
        <input type="file" @change="handleFile"><br><br>
        <button type="submit">Save</button>
        <button type="button" @click="closeModal">Cancel</button>
      </form>
    </div>
  </div>

  <div class="content">
    <h2>Inventory</h2>
    <input v-model="search" placeholder="Search item...">
    <button @click="loadItems">Search</button>
    <button @click="openAddModal">Add Item</button>

    <table class="table">
      <thead>
        <tr>
          <th>Name</th><th>Description</th><th>Category</th><th>Quantity</th><th>Unit</th><th>Image</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredItems" :key="item.id">
          <td>{{ item.name }}</td>
          <td>{{ item.description }}</td> 
          <td>{{ item.category }}</td>
          <td>{{ item.quantity }}</td>
          <td>{{ item.unit }}</td>
          <td>
  <img :src="'uploads/' + item.image" alt="Item Image" style="width: 100px; height: 70px; object-fit: cover;" v-if="item.image">
</td>

          <td>{{ item.status }}</td>
          <td>
            <button @click="openEditModal(item)">Edit</button>
            <button @click="confirmDelete(item.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
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
      items: [],
      form: {
        id: null, name: '', description: '', category: '', stock: '', unit: '', image: null
      },
      search: '',
      showModal: false,
      isEditing: false
    };
  },
  computed: {
    filteredItems() {
      const keyword = this.search.toLowerCase();
      return this.items.filter(item => item.name.toLowerCase().includes(keyword));
    }
  },
  methods: {
    loadItems() {
      fetch('inventory.php', {
        headers: { 'Accept': 'application/json' }
      })
      .then(res => res.json())
      .then(data => this.items = data.items);
    },
    openAddModal() {
      this.resetForm();
      this.isEditing = false;
      this.showModal = true;
    },
    openEditModal(item) {
      this.form = { ...item, stock: item.quantity };
      this.isEditing = true;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.resetForm();
    },
    handleFile(e) {
      this.form.image = e.target.files[0];
    },
    submitForm() {
      const formData = new FormData();
      for (const key in this.form) {
        if (this.form[key] !== null) {
          formData.append(key, this.form[key]);
        }
      }
      formData.append('action', this.isEditing ? 'update' : 'add');

      fetch('inventory.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(() => {
        Swal.fire('Success!', this.isEditing ? 'Item updated' : 'Item added', 'success');
        this.closeModal();
        this.loadItems();
      });
    },
    confirmDelete(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the item.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(result => {
        if (result.isConfirmed) {
          this.deleteItem(id);
        }
      });
    },
    deleteItem(id) {
      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', id);

      fetch('inventory.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(() => {
        Swal.fire('Deleted!', 'Item has been deleted.', 'success');
        this.loadItems();
      });
    },
    resetForm() {
      this.form = { id: null, name: '', description: '', category: '', stock: '', unit: '', image: null };
    }
  },
  mounted() {
    this.loadItems();
  }
}).mount('#app');
</script>
</body>
</html>
