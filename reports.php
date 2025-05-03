<?php
// Include database connection
include 'db.php';

// Set content type based on the request
$isApiRequest = isset($_GET['api']); // Check if the request is an API request (for JSON output)

// If it's an API request, process it and return JSON
if ($isApiRequest) {
    // Set content type to JSON for API response
    header('Content-Type: application/json');

    // Check database connection
    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
    }

    // Get date and category filters from the request
    $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

    // Prepare SQL query with optional filters
    $sql = "SELECT created_at, item, type, quantity FROM stock WHERE 1=1";
    $params = [];

    if ($dateFilter) {
        // Validate date format (YYYY-MM-DD)
        if (DateTime::createFromFormat('Y-m-d', $dateFilter) !== false) {
            $sql .= " AND created_at >= ?";
            $params[] = $dateFilter;
        } else {
            echo json_encode(["error" => "Invalid date format. Use YYYY-MM-DD."]);
            exit;
        }
    }

    if ($categoryFilter) {
        $sql .= " AND type = ?";
        $params[] = $categoryFilter;
    }

    $sql .= " ORDER BY created_at DESC"; // Optional: Order by creation date

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Failed to prepare SQL query.", "details" => $conn->error]);
        exit;
    }

    // Bind parameters to the query
    if ($params) {
        $types = str_repeat('s', count($params)); // Assuming all parameters are strings
        $stmt->bind_param($types, ...$params);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the results
    $reports = [];
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }

    // Return the results as JSON
    echo json_encode($reports);

    // Close connection
    $stmt->close();
    $conn->close();

    exit; // End execution here for API requests
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reports</title>
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
      <h2>Reports</h2>
      <label>Filter by Date:</label>
      <input type="date" v-model="dateFilter" @change="fetchReports" />

      <label>Filter by Category:</label>
      <select v-model="categoryFilter" @change="fetchReports">
        <option value="">All</option>
        <option v-for="cat in categories" :value="cat">{{ cat }}</option>
      </select>

      <button @click="exportCSV">Export CSV</button>

      <table border="1" cellpadding="5">
        <thead>
          <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Type</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in reports" :key="r.created_at">
            <td>{{ r.created_at }}</td>
            <td>{{ r.item }}</td>
            <td>{{ r.type }}</td>
            <td>{{ r.quantity }}</td>
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
          reports: [],
          categories: ['Food', 'Electronics', 'Supplies'],
          dateFilter: '',
          categoryFilter: ''
        };
      },
      methods: {
        fetchReports() {
          // Fetch data from PHP script (API)
          fetch(window.location.href + '?api=true&date=' + this.dateFilter + '&category=' + this.categoryFilter)
            .then(res => res.json())
            .then(data => {
              this.reports = data;
            });
        },
        exportCSV() {
          alert("Export CSV clicked!");
        }
      },
      mounted() {
        this.fetchReports();
      }
    }).mount('#app');
  </script>
</body>
</html>
