<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Stock In/Out</title>
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
        fetch('/api/items.php')
          .then(res => res.json())
          .then(data => {
            this.items = data;
          });
      },
      methods: {
        submitStock() {
          fetch('/api/stock.php', {
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
          .then(data => alert(data.message));
        }
      }
    }).mount('#app');
  </script>
</body>
</html>
