<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Activity Logs</title>
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
      <h2>Activity Logs</h2>
      <table border="1" cellpadding="5">
        <thead>
          <tr>
            <th>Date</th>
            <th>User</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs">
            <td>{{ log.date }}</td>
            <td>{{ log.user }}</td>
            <td>{{ log.action }}</td>
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
          logs: []
        };
      },
      mounted() {
        fetch('/api/logs.php')
          .then(res => res.json())
          .then(data => {
            this.logs = data;
          });
      }
    }).mount('#app');
  </script>
</body>
</html>
