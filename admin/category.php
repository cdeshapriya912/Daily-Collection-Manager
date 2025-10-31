<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Category - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <link rel="icon" href="img/package.png" type="image/png">
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
              "card-light": "#ffffff",
              "text-light": "#4a5568",
              "heading-light": "#1a202c",
              "border-light": "#e2e8f0",
            },
            fontFamily: { sans: ["Inter", "sans-serif"] },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
  </head>
  <body class="bg-background-light">
    <div class="flex h-screen">
      <?php $activePage = 'category'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Category</h2>
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
          <!-- Add Category Form -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <h3 class="text-lg font-semibold text-heading-light mb-4">Add New Category</h3>
            <form id="categoryForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
              <div>
                <label for="categoryName" class="block text-sm font-medium text-heading-light mb-2">Category Name *</label>
                <input type="text" id="categoryName" required placeholder="Enter category name" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
              </div>
              <div>
                <label for="categoryDescription" class="block text-sm font-medium text-heading-light mb-2">Description</label>
                <input type="text" id="categoryDescription" placeholder="Enter description" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
              </div>
              <div>
                <button type="submit" class="w-full bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                  <span class="material-icons">add</span>
                  Add Category
                </button>
              </div>
            </form>
          </div>

          <!-- Category Table -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">All Categories</h3>
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">ID</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Category Name</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Description</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Products</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-text-light">CAT001</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Electronics</td>
                      <td class="py-3 px-4 text-text-light">Electronic devices and gadgets</td>
                      <td class="py-3 px-4 text-primary font-semibold">15</td>
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
                      <td class="py-3 px-4 text-text-light">CAT002</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Accessories</td>
                      <td class="py-3 px-4 text-text-light">Tech accessories and peripherals</td>
                      <td class="py-3 px-4 text-primary font-semibold">8</td>
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
                      <td class="py-3 px-4 text-text-light">CAT003</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Cables</td>
                      <td class="py-3 px-4 text-text-light">Various types of cables and adapters</td>
                      <td class="py-3 px-4 text-primary font-semibold">12</td>
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
                      <td class="py-3 px-4 text-text-light">CAT004</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Furniture</td>
                      <td class="py-3 px-4 text-text-light">Office and home furniture items</td>
                      <td class="py-3 px-4 text-primary font-semibold">6</td>
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
                      <td class="py-3 px-4 text-text-light">CAT005</td>
                      <td class="py-3 px-4 text-heading-light font-medium">Clothing</td>
                      <td class="py-3 px-4 text-text-light">Apparel and fashion items</td>
                      <td class="py-3 px-4 text-primary font-semibold">20</td>
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
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
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


