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
    <title>Products - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Product Management - Daily Collection Manager">
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
      <?php $activePage = 'product'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Products</h2>
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
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto w-full max-w-full" style="-webkit-overflow-scrolling: touch;">
          <!-- Search Section -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-heading-light">Search Products</h3>
              <button 
                id="addProductBtn" 
                class="bg-primary text-white w-10 h-10 md:w-auto md:px-4 md:py-2 rounded-full md:rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center"
                title="Add Product"
              >
                <span class="material-icons text-lg">add</span>
                <span class="ml-2 hidden md:inline">Add Product</span>
              </button>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="w-full sm:w-48">
                <label for="categorySelect" class="block text-sm font-medium text-text-light mb-2">Category</label>
                <div class="relative">
                  <select 
                    id="categorySelect" 
                    class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white bg-none cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="">All Categories</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Cables">Cables</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Clothing">Clothing</option>
                  </select>
                  <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-text-light">
                    <span class="material-icons">expand_more</span>
                  </span>
                </div>
              </div>
              <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-text-light mb-2">Product Name or ID</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Enter product name or ID..." 
                    class="w-full px-4 py-3 pl-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                  >
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">
                    <span class="material-icons text-lg">search</span>
                  </span>
                </div>
              </div>
              <div class="flex items-end justify-center">
                <button 
                  id="searchBtn" 
                  class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                >
                  <span class="material-icons text-lg">search</span>
                  Search
                </button>
              </div>
            </div>
          </div>

          <!-- Product Table -->
          <div class="bg-card-light rounded-lg border border-border-light overflow-hidden">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Product List</h3>
              <div class="overflow-x-auto md:overflow-x-visible max-w-full w-full touch-pan-x" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[800px] md:min-w-0" id="productTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">ID</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Photo</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Product Name</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Regular Price</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Selling Price</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Quantity</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="productTableBody">
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">P001</td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop&crop=center" alt="Product Image" class="w-full h-full object-cover">
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium">Wireless Headphones</td>
                      <td class="py-3 px-4 text-text-light">Rs. 120.00</td>
                      <td class="py-3 px-4 text-primary font-semibold">Rs. 99.99</td>
                      <td class="py-3 px-4 text-text-light">25</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
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
                      <td class="py-3 px-4 text-text-light">P002</td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100&h=100&fit=crop&crop=center" alt="Laptop Stand" class="w-full h-full object-cover">
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium">Laptop Stand</td>
                      <td class="py-3 px-4 text-text-light">Rs. 60.00</td>
                      <td class="py-3 px-4 text-primary font-semibold">Rs. 49.99</td>
                      <td class="py-3 px-4 text-text-light">12</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
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
                      <td class="py-3 px-4 text-text-light">P003</td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=100&h=100&fit=crop&crop=center" alt="USB-C Cable" class="w-full h-full object-cover">
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium">USB-C Cable</td>
                      <td class="py-3 px-4 text-text-light">Rs. 25.00</td>
                      <td class="py-3 px-4 text-primary font-semibold">Rs. 19.99</td>
                      <td class="py-3 px-4 text-text-light">50</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
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
                      <td class="py-3 px-4 text-text-light">P004</td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=100&h=100&fit=crop&crop=center" alt="Bluetooth Speaker" class="w-full h-full object-cover">
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium">Bluetooth Speaker</td>
                      <td class="py-3 px-4 text-text-light">Rs. 95.00</td>
                      <td class="py-3 px-4 text-primary font-semibold">Rs. 79.99</td>
                      <td class="py-3 px-4 text-text-light">8</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
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
                      <td class="py-3 px-4 text-text-light">P005</td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                          <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=100&h=100&fit=crop&crop=center" alt="Phone Case" class="w-full h-full object-cover">
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium">Phone Case</td>
                      <td class="py-3 px-4 text-text-light">Rs. 35.00</td>
                      <td class="py-3 px-4 text-primary font-semibold">Rs. 29.99</td>
                      <td class="py-3 px-4 text-text-light">30</td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
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
    <script src="js/product.js?v=15" defer></script>
    <script>
      // submenu handled in partials/menu.php
      // Responsive table: add data-labels from headers for mobile cards
      (function() {
        const table = document.getElementById('productTable');
        if (!table) return;
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        table.querySelectorAll('tbody tr').forEach(row => {
          Array.from(row.children).forEach((cell, idx) => {
            if (cell.tagName.toLowerCase() !== 'td') return;
            const label = headers[idx] || '';
            cell.setAttribute('data-label', label);
            
            // Add regular price data for mobile layout
            if (idx === 4) { // Regular Price column
              const regularPriceCell = row.children[4];
              const sellingPriceCell = row.children[3];
              if (regularPriceCell && sellingPriceCell) {
                sellingPriceCell.setAttribute('data-regular-price', regularPriceCell.textContent.trim());
              }
            }
          });
        });
      })();

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
