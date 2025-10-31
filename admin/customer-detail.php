<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Customer Detail - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Customer Detail - Daily Collection Manager">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#10b981",
              "background-light": "#f7fafc",
              "background-dark": "#1a202c",
              "card-light": "#ffffff",
              "card-dark": "#2d3748",
              "text-light": "#4a5568",
              "text-dark": "#a0aec0",
              "heading-light": "#1a202c",
              "heading-dark": "#f7fafc",
              "border-light": "#e2e8f0",
              "border-dark": "#4a5568",
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
            },
            borderRadius: {
              lg: "0.75rem",
            },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
  </head>
  <body class="bg-background-light">
    <div class="flex h-screen">
      <?php $activePage = 'customer-detail'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
              <span class="material-icons">menu</span>
            </button>
            <button onclick="history.back()" class="flex items-center gap-2 text-text-light hover:text-primary transition-colors">
              <span class="material-icons">arrow_back</span>
              <span>Back</span>
            </button>
          </div>
          <h2 class="text-2xl font-bold text-heading-light">Customer Detail</h2>
          <div class="flex items-center gap-4">
            <button class="text-text-light">
              <span class="material-icons">notifications</span>
            </button>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                <span class="material-icons text-text-light">person</span>
              </div>
              <div>
                <p class="font-semibold text-heading-light" id="userName">Demo User</p>
                <p class="text-sm text-text-light">Admin</p>
              </div>
            </div>
          </div>
        </header>
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
          <!-- Customer Info -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-2xl font-bold text-heading-light" id="customerName">Robert Anderson</h3>
                <p class="text-text-light">Customer ID: <span id="customerId">C001</span></p>
                <p class="text-text-light">Email: <span id="customerEmail">robert.a@email.com</span></p>
                <p class="text-text-light">Mobile: <span id="customerMobile">+1 555-0101</span></p>
              </div>
              <div class="text-right">
                <p class="text-sm text-text-light">Remaining Balance</p>
                <p class="text-2xl font-bold text-red-600" id="remainingBalance">Rs. 250.00</p>
              </div>
            </div>
          </div>

          <!-- Products Purchased -->
          <div class="bg-card-light rounded-lg border border-border-light mb-6">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Products Purchased</h3>
              <div class="overflow-x-auto">
                <table class="w-full" id="productTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Product</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Price</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Quantity</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Total</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Purchase Date</th>
                    </tr>
                  </thead>
                  <tbody id="productTableBody">
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                          <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100&h=100&fit=crop" alt="Product" class="w-full h-full object-cover">
                          </div>
                          <div>
                            <p class="font-medium text-heading-light">Wireless Headphones</p>
                            <p class="text-sm text-text-light">P001</p>
                          </div>
                        </div>
                      </td>
                      <td class="py-3 px-4 text-text-light">Rs. 1,250.00</td>
                      <td class="py-3 px-4 text-text-light">1</td>
                      <td class="py-3 px-4 text-heading-light font-semibold">Rs. 1,250.00</td>
                      <td class="py-3 px-4 text-text-light">2024-01-15</td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                          <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop" alt="Product" class="w-full h-full object-cover">
                          </div>
                          <div>
                            <p class="font-medium text-heading-light">Smart Watch</p>
                            <p class="text-sm text-text-light">P002</p>
                          </div>
                        </div>
                      </td>
                      <td class="py-3 px-4 text-text-light">Rs. 2,500.00</td>
                      <td class="py-3 px-4 text-text-light">1</td>
                      <td class="py-3 px-4 text-heading-light font-semibold">Rs. 2,500.00</td>
                      <td class="py-3 px-4 text-text-light">2024-01-20</td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                          <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop" alt="Product" class="w-full h-full object-cover">
                          </div>
                          <div>
                            <p class="font-medium text-heading-light">Bluetooth Speaker</p>
                            <p class="text-sm text-text-light">P003</p>
                          </div>
                        </div>
                      </td>
                      <td class="py-3 px-4 text-text-light">Rs. 800.00</td>
                      <td class="py-3 px-4 text-text-light">2</td>
                      <td class="py-3 px-4 text-heading-light font-semibold">Rs. 1,600.00</td>
                      <td class="py-3 px-4 text-text-light">2024-01-25</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="mt-4 pt-4 border-t border-border-light">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-heading-light">Total Amount:</span>
                  <span class="text-xl font-bold text-primary">Rs. 5,350.00</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-lg font-semibold text-heading-light">Amount Paid:</span>
                  <span class="text-xl font-bold text-green-600">Rs. 5,100.00</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-lg font-semibold text-heading-light">Remaining Balance:</span>
                  <span class="text-xl font-bold text-red-600">Rs. 250.00</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 60-Day Payment Schedule -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">60-Day Payment Schedule</h3>
              <div class="payment-schedule">
                <table class="w-full">
                  <thead class="sticky top-0 bg-white">
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Day</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Date</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Amount Due</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Status</th>
                    </tr>
                  </thead>
                  <tbody id="paymentScheduleBody">
                    <!-- Payment schedule will be generated by JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
      </div>
      <!-- mobile backdrop -->
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
    <script>
      // Generate 60-day payment schedule
      function generatePaymentSchedule() {
        const tbody = document.getElementById('paymentScheduleBody');
        const totalAmount = 250.00; // Remaining balance
        const dailyAmount = (totalAmount / 60).toFixed(2);
        const startDate = new Date();
        
        for (let i = 1; i <= 60; i++) {
          const currentDate = new Date(startDate);
          currentDate.setDate(startDate.getDate() + i - 1);
          
          const row = document.createElement('tr');
          row.className = 'border-b border-border-light hover:bg-gray-50';
          
          const isPaid = Math.random() > 0.7; // Random payment status
          const statusClass = isPaid ? 'text-green-600' : 'text-red-600';
          const statusText = isPaid ? 'Paid' : 'Pending';
          
          row.innerHTML = `
            <td class="py-3 px-4 text-text-light">${i}</td>
            <td class="py-3 px-4 text-text-light">${currentDate.toLocaleDateString()}</td>
            <td class="py-3 px-4 text-heading-light font-semibold">Rs. ${dailyAmount}</td>
            <td class="py-3 px-4 ${statusClass} font-medium">${statusText}</td>
          `;
          
          tbody.appendChild(row);
        }
      }
      
      // Load customer data and initialize payment schedule when page loads
      document.addEventListener('DOMContentLoaded', function() {
        loadCustomerData();
        generatePaymentSchedule();
      });
      
      // Load customer data from localStorage
      function loadCustomerData() {
        const customerData = localStorage.getItem('selectedCustomer');
        if (customerData) {
          const customer = JSON.parse(customerData);
          document.getElementById('customerName').textContent = customer.name;
          document.getElementById('customerId').textContent = customer.id;
          document.getElementById('customerEmail').textContent = customer.email;
          document.getElementById('customerMobile').textContent = customer.mobile;
          document.getElementById('remainingBalance').textContent = `Rs. ${customer.balance}`;
        }
      }

      // Sidebar toggle functionality
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');

      sidebarToggle.addEventListener('click', function() {
        const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
          // Close sidebar
          mobileSidebar.classList.add('-translate-x-full');
          sidebarBackdrop.classList.add('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'false');
        } else {
          // Open sidebar
          mobileSidebar.classList.remove('-translate-x-full');
          sidebarBackdrop.classList.remove('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'true');
        }
      });

      // Close sidebar when clicking backdrop
      sidebarBackdrop.addEventListener('click', function() {
        mobileSidebar.classList.add('-translate-x-full');
        sidebarBackdrop.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
          const isSidebarOpen = sidebarToggle.getAttribute('aria-expanded') === 'true';
          const isClickInsideSidebar = mobileSidebar.contains(event.target);
          const isClickOnToggle = sidebarToggle.contains(event.target);
          
          if (isSidebarOpen && !isClickInsideSidebar && !isClickOnToggle) {
            mobileSidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
            sidebarToggle.setAttribute('aria-expanded', 'false');
          }
        }
      });
    </script>
  </body>
</html>
