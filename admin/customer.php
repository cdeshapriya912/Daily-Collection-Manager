<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Customers - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Customer Management - Daily Collection Manager">
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
      <?php $activePage = 'customer'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Customers</h2>
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
          <!-- Search Section -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-heading-light">Search Customers</h3>
              <button 
                id="addCustomerBtn" 
                class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
              >
                <span class="material-icons text-lg">add</span>
                Add Customer
              </button>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-text-light mb-2">Customer Name, ID or Mobile</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Enter customer name, ID or mobile..." 
                    class="w-full px-4 py-3 pl-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                  >
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">
                    <span class="material-icons text-lg">search</span>
                  </span>
                </div>
              </div>
              <div class="flex items-end">
                <button 
                  id="searchBtn" 
                  class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                >
                  <span class="material-icons text-lg">search</span>
                  Search
                </button>
              </div>
            </div>
          </div>

          <!-- Customer Table -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Customer List</h3>
              <div class="overflow-x-auto">
                <table class="w-full" id="customerTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Customer ID</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Customer Name</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Email</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Mobile</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Remaining Balance</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="customerTableBody">
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C001</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Robert Anderson</td>
                      <td class="py-3 px-4 text-text-light">robert.a@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0101</td>
                      <td class="py-3 px-4 text-red-600 font-semibold">Rs. 250.00</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C002</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Emily Martinez</td>
                      <td class="py-3 px-4 text-text-light">emily.m@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0102</td>
                      <td class="py-3 px-4 text-green-600 font-semibold">Rs. 0.00</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C003</td>
                      <td class="py-3 px-4 text-heading-light font-medium">David Thompson</td>
                      <td class="py-3 px-4 text-text-light">david.t@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0103</td>
                      <td class="py-3 px-4 text-red-600 font-semibold">Rs. 125.50</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C004</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Sarah Wilson</td>
                      <td class="py-3 px-4 text-text-light">sarah.wilson@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0104</td>
                      <td class="py-3 px-4 text-green-600 font-semibold">Rs. 0.00</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C005</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Michael Brown</td>
                      <td class="py-3 px-4 text-text-light">michael.b@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0105</td>
                      <td class="py-3 px-4 text-red-600 font-semibold">Rs. 575.25</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">C006</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Jessica Davis</td>
                      <td class="py-3 px-4 text-text-light">jessica.d@email.com</td>
                      <td class="py-3 px-4 text-text-light">+1 555-0106</td>
                      <td class="py-3 px-4 text-red-600 font-semibold">Rs. 89.99</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                            <span class="material-icons text-lg">visibility</span>
                          </button>
                          <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
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
      // Function to navigate to customer detail page
      function viewCustomerDetail(customerId, customerName, email, mobile, balance) {
        // Store customer data in localStorage for the detail page
        localStorage.setItem('selectedCustomer', JSON.stringify({
          id: customerId,
          name: customerName,
          email: email,
          mobile: mobile,
          balance: balance
        }));
        
        // Navigate to customer detail page
        window.location.href = 'customer-detail.php';
      }
      
      // Update all view buttons with click handlers
      document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('button[title="View"]');
        const customerData = [
          { id: 'C001', name: 'Robert Anderson', email: 'robert.a@email.com', mobile: '+1 555-0101', balance: '250.00' },
          { id: 'C002', name: 'Emily Martinez', email: 'emily.m@email.com', mobile: '+1 555-0102', balance: '0.00' },
          { id: 'C003', name: 'David Thompson', email: 'david.t@email.com', mobile: '+1 555-0103', balance: '125.50' },
          { id: 'C004', name: 'Sarah Wilson', email: 'sarah.wilson@email.com', mobile: '+1 555-0104', balance: '0.00' },
          { id: 'C005', name: 'Michael Brown', email: 'michael.b@email.com', mobile: '+1 555-0105', balance: '575.25' },
          { id: 'C006', name: 'Jessica Davis', email: 'jessica.d@email.com', mobile: '+1 555-0106', balance: '89.99' }
        ];
        
        viewButtons.forEach((button, index) => {
          if (customerData[index]) {
            const customer = customerData[index];
            button.setAttribute('onclick', `viewCustomerDetail('${customer.id}', '${customer.name}', '${customer.email}', '${customer.mobile}', '${customer.balance}')`);
          }
        });
      });

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


