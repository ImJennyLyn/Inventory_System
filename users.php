<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Users</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
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
  </style>
</head>
<body>
  <div id="sidebar-placeholder"></div>

  <div id="app">
    <div class="content">
      <h2>Users</h2>
      <table border="1" cellpadding="5">
        <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users">
            <td>{{ user.name }}</td>
            <td>{{ user.role }}</td>
            <td>
              <button @click="editUser(user)">Edit</button>
              <button @click="deleteUser(user.id)">Delete</button>
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
          users: []
        };
      },
      mounted() {
        fetch('/api/users.php')
          .then(res => res.json())
          .then(data => {
            this.users = data;
          });
      },
      methods: {
        editUser(user) {
          alert("Edit user: " + user.name);
        },
        deleteUser(id) {
          if (confirm("Delete this user?")) {
            fetch('/api/delete_user.php?id=' + id)
              .then(() => {
                this.users = this.users.filter(u => u.id !== id);
              });
          }
        }
      }
    }).mount('#app');
  </script>
</body>
</html>
