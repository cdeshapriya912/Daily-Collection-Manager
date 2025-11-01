<?php
// Admin-only page - requires admin role
require_once __DIR__ . '/config/admin-auth.php';

// Get user information from session
$full_name = $_SESSION['full_name'] ?? 'User';
?>
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
                <p class="font-semibold text-heading-light" id="userName"><?php echo htmlspecialchars($full_name); ?></p>
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
                    <tr>
                      <td colspan="6" class="py-8 text-center text-text-light">
                        <div class="flex flex-col items-center gap-2">
                          <span class="material-icons text-4xl animate-spin">refresh</span>
                          <p>Loading customers...</p>
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
    
    <!-- Add Customer Modal -->
    <div id="addCustomerModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-border-light px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h3 class="text-xl font-bold text-heading-light">Add New Customer</h3>
          <button id="closeAddModal" class="text-text-light hover:text-heading-light">
            <span class="material-icons">close</span>
          </button>
        </div>
        
        <form id="addCustomerForm" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Name -->
            <div class="md:col-span-2">
              <label for="addCustomerName" class="block text-sm font-medium text-text-light mb-2">
                Customer Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                id="addCustomerName" 
                name="full_name" 
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter customer name"
              >
            </div>
            
            <!-- Email -->
            <div>
              <label for="addCustomerEmail" class="block text-sm font-medium text-text-light mb-2">
                Email
              </label>
              <input 
                type="email" 
                id="addCustomerEmail" 
                name="email"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="customer@example.com"
              >
            </div>
            
            <!-- Mobile -->
            <div>
              <label for="addCustomerMobile" class="block text-sm font-medium text-text-light mb-2">
                Mobile <span class="text-red-500">*</span>
              </label>
              <input 
                type="tel" 
                id="addCustomerMobile" 
                name="mobile" 
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="+94 77 123 4567"
              >
            </div>
            
            <!-- Address -->
            <div class="md:col-span-2">
              <label for="addCustomerAddress" class="block text-sm font-medium text-text-light mb-2">
                Address
              </label>
              <textarea 
                id="addCustomerAddress" 
                name="address" 
                rows="3"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                placeholder="Enter customer address"
              ></textarea>
            </div>
            
            <!-- Status -->
            <div>
              <label for="addCustomerStatus" class="block text-sm font-medium text-text-light mb-2">
                Status
              </label>
              <select 
                id="addCustomerStatus" 
                name="status"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="blocked">Blocked</option>
              </select>
            </div>
          </div>
          
          <div class="flex gap-3 mt-6">
            <button 
              type="button" 
              id="cancelAddBtn"
              class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="flex-1 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition-colors"
            >
              Add Customer
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Edit Customer Modal -->
    <div id="editCustomerModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-border-light px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h3 class="text-xl font-bold text-heading-light">Edit Customer</h3>
          <button id="closeEditModal" class="text-text-light hover:text-heading-light">
            <span class="material-icons">close</span>
          </button>
        </div>
        
        <form id="editCustomerForm" class="p-6">
          <input type="hidden" id="editCustomerId" name="id">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Name -->
            <div class="md:col-span-2">
              <label for="editCustomerName" class="block text-sm font-medium text-text-light mb-2">
                Customer Name <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                id="editCustomerName" 
                name="full_name" 
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter customer name"
              >
            </div>
            
            <!-- Email -->
            <div>
              <label for="editCustomerEmail" class="block text-sm font-medium text-text-light mb-2">
                Email
              </label>
              <input 
                type="email" 
                id="editCustomerEmail" 
                name="email"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="customer@example.com"
              >
            </div>
            
            <!-- Mobile -->
            <div>
              <label for="editCustomerMobile" class="block text-sm font-medium text-text-light mb-2">
                Mobile <span class="text-red-500">*</span>
              </label>
              <input 
                type="tel" 
                id="editCustomerMobile" 
                name="mobile" 
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="+94 77 123 4567"
              >
            </div>
            
            <!-- Address -->
            <div class="md:col-span-2">
              <label for="editCustomerAddress" class="block text-sm font-medium text-text-light mb-2">
                Address
              </label>
              <textarea 
                id="editCustomerAddress" 
                name="address" 
                rows="3"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                placeholder="Enter customer address"
              ></textarea>
            </div>
            
            <!-- Status -->
            <div>
              <label for="editCustomerStatus" class="block text-sm font-medium text-text-light mb-2">
                Status
              </label>
              <select 
                id="editCustomerStatus" 
                name="status"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="blocked">Blocked</option>
              </select>
            </div>
          </div>
          
          <div class="flex gap-3 mt-6">
            <button 
              type="button" 
              id="cancelEditBtn"
              class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit"
              class="flex-1 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition-colors"
            >
              Update Customer
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <!-- View Customer Modal -->
    <div id="viewCustomerModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-border-light px-6 py-4 flex items-center justify-between rounded-t-2xl">
          <h3 class="text-xl font-bold text-heading-light">Customer Details</h3>
          <button id="closeViewModal" class="text-text-light hover:text-heading-light">
            <span class="material-icons">close</span>
          </button>
        </div>
        
        <div class="p-6">
          <!-- Customer Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Customer Code -->
            <div>
              <label class="block text-sm font-medium text-text-light mb-1">Customer Code</label>
              <p class="text-heading-light font-semibold text-lg" id="viewCustomerCode">-</p>
            </div>
            
            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-text-light mb-1">Status</label>
              <span id="viewCustomerStatus" class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">Active</span>
            </div>
            
            <!-- Full Name -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-text-light mb-1">Full Name</label>
              <p class="text-heading-light font-semibold text-lg" id="viewCustomerName">-</p>
            </div>
            
            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-text-light mb-1">Email</label>
              <p class="text-heading-light" id="viewCustomerEmail">-</p>
            </div>
            
            <!-- Mobile -->
            <div>
              <label class="block text-sm font-medium text-text-light mb-1">Mobile</label>
              <p class="text-heading-light" id="viewCustomerMobile">-</p>
            </div>
            
            <!-- Address -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-text-light mb-1">Address</label>
              <p class="text-heading-light" id="viewCustomerAddress">-</p>
            </div>
          </div>
          
          <!-- Financial Summary -->
          <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-border-light">
            <h4 class="font-semibold text-heading-light mb-4 flex items-center gap-2">
              <span class="material-icons">account_balance_wallet</span>
              Financial Summary
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Total Purchased -->
              <div>
                <label class="block text-sm font-medium text-text-light mb-1">Total Purchased</label>
                <p class="text-heading-light font-semibold" id="viewCustomerTotalPurchased">Rs. 0.00</p>
              </div>
              
              <!-- Total Paid -->
              <div>
                <label class="block text-sm font-medium text-text-light mb-1">Total Paid</label>
                <p class="text-heading-light font-semibold" id="viewCustomerTotalPaid">Rs. 0.00</p>
              </div>
              
              <!-- Remaining Balance -->
              <div>
                <label class="block text-sm font-medium text-text-light mb-1">Remaining Balance</label>
                <p id="viewCustomerBalance" class="text-red-600 font-bold text-lg">Rs. 0.00</p>
              </div>
            </div>
          </div>
          
          <div class="flex gap-3 mt-6">
            <button 
              type="button" 
              id="closeViewBtn"
              class="flex-1 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-primary/90 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <script src="js/app.js?v=15" defer></script>
    <script src="assets/js/notification-dialog.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/confirmation-dialog.js?v=<?php echo time(); ?>"></script>
    <script src="js/customer.js?v=<?php echo time(); ?>"></script>
    <script>
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


