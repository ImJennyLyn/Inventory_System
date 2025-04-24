<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Inventory</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Content styling */
    .content {
      margin-left: 260px; /* Default margin when sidebar is expanded */
      padding: 20px;
      transition: margin-left 0.3s ease; /* Smooth transition */
    }

    .content.full-width {
      margin-left: 3rem; /* When sidebar is collapsed, content takes full width */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    button {
      padding: 6px 12px;
      margin: 2px;
    }

    input {
      padding: 6px;
      margin-right: 10px;
    }
  </style>
</head>
<body>
  <div id="sidebar-placeholder"></div>

  <div id="app">
     <!-- Edit Modal -->
<div v-if="showEditModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center;">
  <div style="background: white; padding: 20px; border-radius: 8px; width: 400px;">
    <h3>Edit Item</h3>
    <form @submit.prevent="submitEdit">
      <label>Item Name:</label>
      <input type="text" v-model="editItemData.name" required><br>

      <label>Description:</label>
      <input type="text" v-model="editItemData.description"><br>

      <label>Category:</label>
      <input type="text" v-model="editItemData.category"><br>

      <label>Stock:</label>
      <input type="number" v-model="editItemData.stock" required><br>

      <label>Unit:</label>
      <input type="text" v-model="editItemData.unit"><br><br>

      <button type="submit">Save</button>
      <button @click="showEditModal = false" type="button">Cancel</button>
    </form>
  </div>
</div>

  <!-- Modal for Add Item -->
<div v-if="showModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center;">
<div style="background: white; padding: 20px; border-radius: 8px; width: 400px;">
  <h3>Add New Item</h3>
  <form @submit.prevent="submitForm" enctype="multipart/form-data">
    <label>Item Name:</label>
    <input type="text" v-model="newItem.name" required><br>

    <label>Description:</label>
    <input type="text" v-model="newItem.description"><br>

    <label>Category:</label>
    <input type="text" v-model="newItem.category"><br>

    <label>Stock:</label>
    <input type="number" v-model="newItem.stock" required><br>

    <label>Unit:</label>
    <input type="text" v-model="newItem.unit"><br>

    <label>Image:</label>
    <input type="file" @change="handleImage"><br><br>

    <button type="submit">Save</button>
    <button @click="showModal = false" type="button">Cancel</button>
  </form>
</div>
</div>
    <div class="content">
      <h2>Inventory</h2>
      <input v-model="search" placeholder="Search items" />
      <button @click="loadItems">Search</button>
      <button @click="showModal = true">Add Item</button>

      <table>
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>{{ item.name }}</td>
            <td>{{ item.category }}</td>
            <td>{{ item.quantity }}</td>
            <td>{{ item.status }}</td>
            <td>
              <button @click="editItem(item.id)">Edit</button>
              <button @click="deleteItem(item.id)">Delete</button>
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
  
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content');
  
        toggleBtn.addEventListener('click', () => {
          sidebar.classList.toggle('collapsed');
          content.classList.toggle('full-width');
  
          toggleBtn.innerHTML = sidebar.classList.contains('collapsed') ? '☰' : '×';
        });
      });
  
    const { createApp } = Vue;
    createApp({
      data() {
        return {
          items: [],
          search: '',
          page: 1,
          showModal: false,
          showEditModal: false, // 🆕 Edit Modal toggle
          newItem: {
            name: '',
            description: '',
            category: '',
            stock: '',
            unit: '',
            image: null
          },
          editItemData: {} // 🆕 Edit item holder
        };
      },
      methods: {
        loadItems() {
          fetch(`http://localhost/Inventory%20System/items.php?page=${this.page}&search=${this.search}`)
            .then(res => res.json())
            .then(data => {
              this.items = data.items;
            });
        },
        handleImage(e) {
          this.newItem.image = e.target.files[0];
        },
        submitForm() {
          const formData = new FormData();
          for (const key in this.newItem) {
            formData.append(key, this.newItem[key]);
          }
  
          fetch('http://localhost/Inventory%20System/add_item.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.text())
          .then(data => {
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Item has been added successfully.',
              confirmButtonColor: '#3085d6'
            });
            this.showModal = false;
            this.loadItems();
          });
        },
  
        // 🔁 Edit Functionality
        editItem(id) {
          const item = this.items.find(i => i.id === id);
          if (item) {
            this.editItemData = { ...item };
            this.showEditModal = true;
          }
        },
        submitEdit() {
          const formData = new FormData();
          for (const key in this.editItemData) {
            formData.append(key, this.editItemData[key]);
          }
  
          fetch('http://localhost/Inventory%20System/update_item.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.text())
          .then(() => {
            Swal.fire('Updated!', 'Item has been updated.', 'success');
            this.showEditModal = false;
            this.loadItems();
          });
        },
  
        // ❌ Delete from table only
        deleteItem(id) {
          this.items = this.items.filter(item => item.id !== id);
          Swal.fire('Deleted!', 'Item removed from table view.', 'info');
        }
      },
      created() {
        this.loadItems();
      }
    }).mount('#app');
  </script>
  
</body>

</html>
