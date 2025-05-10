<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <style>
    /* Content styling */
    .content {
      margin-left: 260px; /* Default width when sidebar is expanded */
      padding: 20px;
      transition: margin-left 0.3s ease; /* Smooth transition */
    }

    .content.full-width {
      margin-left: 3rem; /* When sidebar is collapsed, remove the left margin */
    }
  </style>
</head>
<body>
  <!-- Placeholder for the Sidebar -->
  <!-- <button id="toggleSidebarBtn" style="position: fixed; top: 20px; left: 20px; z-index: 1000;">☰</button> -->
  <div id="sidebar-placeholder"></div>
  <!-- Main Content -->
  <div id="app">
    <div class="content">
      <h2>Dashboard</h2>
      <div>
        <p>Total Items: {{ totalItems }}</p>
        <p>Items Low in Stock: {{ lowStock }}</p>
        <p>Recent Activity: {{ recentActivity }}</p>
      </div>
    </div>
  </div>

  <script>
    // Load the sidebar from the external sidebar.html file
    fetch('sidebar.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('sidebar-placeholder').innerHTML = data;

        // Sidebar toggle logic
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content'); // Content container

        toggleBtn.addEventListener('click', () => {
          sidebar.classList.toggle('collapsed');  // Toggle the collapsed class for the sidebar
          content.classList.toggle('full-width'); // Toggle the full-width class for the content

          // Toggle icon for collapsed state
          if (sidebar.classList.contains('collapsed')) {
            toggleBtn.innerHTML = '☰';  // Hamburger icon
          } else {
            toggleBtn.innerHTML = '×';  // Close icon
          }
        });
      });

    // Vue.js logic for the dashboard data
    const { createApp } = Vue;
    createApp({
      data() {
        return {
          totalItems: 0,
          lowStock: 0,
          recentActivity: []
        };
      },
      created() {
        this.fetchDashboardData();
      },
      methods: {
        fetchDashboardData() {
          fetch('/api/dashboard.php')
            .then(res => res.json())
            .then(data => {
              this.totalItems = data.totalItems;
              this.lowStock = data.lowStock;
              this.recentActivity = data.recentActivity;
            });
        }
      }
    }).mount('#app');
  </script>
</body>
</html>
