<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Suppliers - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Supplier Management - Daily Collection Manager">
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
      <?php $activePage = 'supplier'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Suppliers</h2>
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
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto w-full max-w-full" style="-webkit-overflow-scrolling: touch;">
          <!-- Supplier Registration Form -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6 max-w-4xl">
            <h3 class="text-lg font-semibold text-heading-light mb-4">Add Supplier</h3>
            <form id="supplierForm">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="companyName" class="block text-sm font-medium text-heading-light mb-2">Company Name *</label>
                  <input type="text" id="companyName" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                <div>
                  <label for="personName" class="block text-sm font-medium text-heading-light mb-2">Supplier Person Name *</label>
                  <input type="text" id="personName" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                <div>
                  <label for="phone" class="block text-sm font-medium text-heading-light mb-2">Phone Number *</label>
                  <input type="tel" id="phone" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="07X XXX XXXX">
                </div>
                <div>
                  <label for="email" class="block text-sm font-medium text-heading-light mb-2">Email</label>
                  <input type="email" id="email" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="name@company.com">
                </div>
              </div>
              <div class="mt-6 flex items-center justify-end gap-4">
                <button type="reset" class="px-6 py-3 border border-border-light text-text-light rounded-lg hover:bg-gray-50 transition-colors">Clear</button>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                  <span class="material-icons">save</span>
                  Save Supplier
                </button>
              </div>
            </form>
          </div>

          <!-- Suppliers Table (Demo Data) -->
          <div class="bg-card-light rounded-lg border border-border-light overflow-hidden">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Suppliers List</h3>
              <div class="overflow-x-auto md:overflow-x-visible max-w-full w-full touch-pan-x" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[800px] md:min-w-0" id="supplierTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Company</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Contact Person</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Phone</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Email</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="supplierTableBody">
                    <tr class="border-b border-border-light hover:bg-gray-50">
                      <td class="py-3 px-4 text-heading-light font-medium">TechSource Pvt Ltd</td>
                      <td class="py-3 px-4 text-text-light">Nimal Perera</td>
                      <td class="py-3 px-4 text-text-light">077 123 4567</td>
                      <td class="py-3 px-4 text-text-light">sales@techsource.lk</td>
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
                      <td class="py-3 px-4 text-heading-light font-medium">GreenLeaf Trading</td>
                      <td class="py-3 px-4 text-text-light">Sithara Fernando</td>
                      <td class="py-3 px-4 text-text-light">071 555 8899</td>
                      <td class="py-3 px-4 text-text-light">contact@greenleaf.lk</td>
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
                      <td class="py-3 px-4 text-heading-light font-medium">SilverLine Imports</td>
                      <td class="py-3 px-4 text-text-light">Kasun Jayasinghe</td>
                      <td class="py-3 px-4 text-text-light">070 222 3344</td>
                      <td class="py-3 px-4 text-text-light">hello@silverline.lk</td>
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
    <script src="js/supplier.js?v=1" defer></script>
    <script>
      // Sidebar toggle functionality
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');

      sidebarToggle.addEventListener('click', function() {
        const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
        if (isExpanded) {
          mobileSidebar.classList.add('-translate-x-full');
          sidebarBackdrop.classList.add('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'false');
        } else {
          mobileSidebar.classList.remove('-translate-x-full');
          sidebarBackdrop.classList.remove('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'true');
        }
      });

      sidebarBackdrop.addEventListener('click', function() {
        mobileSidebar.classList.add('-translate-x-full');
        sidebarBackdrop.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
      });
    </script>
  </body>
  </html>


