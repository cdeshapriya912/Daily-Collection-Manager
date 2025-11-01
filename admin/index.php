<?php
// Admin-only page - requires admin role
// Staff users will be redirected to collection panel
require_once __DIR__ . '/config/admin-auth.php';

// Get user information from session
$full_name = $_SESSION['full_name'] ?? 'User';
$username = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="PWA Dashboard UI - Daily Collection Manager">
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
    <?php echo getDeveloperBanner(); ?>
    <div class="flex h-screen">
      <?php $activePage = 'dashboard'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Dashboard</h2>
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
        <main class="flex-1 p-6 lg:p-8">
          <!-- KPI cards row (Earnings, Remaining Balance, Users, Products) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Earnings -->
            <div class="rounded-lg border border-border-light bg-green-50">
              <div class="flex items-start justify-between p-6">
                <div>
                  <p class="text-sm font-semibold text-green-700">Earning Amount</p>
                  <p class="mt-2 text-3xl font-bold text-green-800">Rs. 12,450</p>
                  <p class="mt-1 text-xs text-green-700/80">Updated just now</p>
                </div>
                <div class="shrink-0 bg-green-600/10 text-green-700 rounded-lg p-3">
                  <span class="material-icons">attach_money</span>
                </div>
              </div>
            </div>

            <!-- Remaining Balance -->
            <div class="rounded-lg border border-border-light bg-cyan-50">
              <div class="flex items-start justify-between p-6">
                <div>
                  <p class="text-sm font-semibold text-cyan-700">Remaining Balance</p>
                  <p class="mt-2 text-3xl font-bold text-cyan-800">Rs. 3,120</p>
                  <p class="mt-1 text-xs text-cyan-700/80">After settlements</p>
                </div>
                <div class="shrink-0 bg-cyan-600/10 text-cyan-700 rounded-lg p-3">
                  <span class="material-icons">account_balance_wallet</span>
                </div>
              </div>
            </div>

            <!-- Users -->
            <div class="rounded-lg border border-border-light bg-fuchsia-50">
              <div class="flex items-start justify-between p-6">
                <div>
                  <p class="text-sm font-semibold text-fuchsia-700">Users</p>
                  <p class="mt-2 text-3xl font-bold text-fuchsia-800">3</p>
                  <p class="mt-1 text-xs text-fuchsia-700/80">Total registered</p>
                </div>
                <div class="shrink-0 bg-fuchsia-600/10 text-fuchsia-700 rounded-lg p-3">
                  <span class="material-icons">people</span>
                </div>
              </div>
            </div>

            <!-- Products -->
            <div class="rounded-lg border border-border-light bg-blue-50">
              <div class="flex items-start justify-between p-6">
                <div>
                  <p class="text-sm font-semibold text-blue-700">Products</p>
                  <p class="mt-2 text-3xl font-bold text-blue-800">24</p>
                  <p class="mt-1 text-xs text-blue-700/80">Active items</p>
                </div>
                <div class="shrink-0 bg-blue-600/10 text-blue-700 rounded-lg p-3">
                  <span class="material-icons">inventory_2</span>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-card-light p-6 rounded-lg border border-border-light col-span-1 md:col-span-2">
              <h3 class="text-lg font-semibold text-heading-light">Overview</h3>
              <p class="mt-2 text-text-light">Welcome to the Daily Collection Manager dashboard PWA demo.</p>
            </div>
            <div class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light">Quick Stats</h3>
              <ul class="mt-4 space-y-3 text-text-light">
                <li class="flex justify-between items-center">
                  <span>Products</span>
                  <span class="font-semibold text-heading-light">24</span>
                </li>
                <li class="flex justify-between items-center">
                  <span>Collections</span>
                  <span class="font-semibold text-heading-light">6</span>
                </li>
                <li class="flex justify-between items-center">
                  <span>Users</span>
                  <span class="font-semibold text-heading-light">3</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Info cards row -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <div class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-sm font-semibold text-heading-light">Orders Status</h3>
              <ul class="mt-4 space-y-3 text-text-light">
                <li class="flex items-center justify-between">
                  <span class="inline-flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500"></span> Processing</span>
                  <span class="font-semibold text-heading-light">128</span>
                </li>
                <li class="flex items-center justify-between">
                  <span class="inline-flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span> Completed</span>
                  <span class="font-semibold text-heading-light">960</span>
                </li>
                <li class="flex items-center justify-between">
                  <span class="inline-flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
                  <span class="font-semibold text-heading-light">24</span>
                </li>
              </ul>
            </div>

            <div class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-sm font-semibold text-heading-light">Revenue Breakdown</h3>
              <ul class="mt-4 space-y-3 text-text-light">
                <li class="flex items-center justify-between">
                  <span>Online</span>
                  <span class="font-semibold text-heading-light">Rs. 8,730</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>Retail</span>
                  <span class="font-semibold text-heading-light">Rs. 2,940</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>Wholesale</span>
                  <span class="font-semibold text-heading-light">Rs. 780</span>
                </li>
              </ul>
            </div>

            <div class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-sm font-semibold text-heading-light">Collections Health</h3>
              <ul class="mt-4 space-y-3 text-text-light">
                <li class="flex items-center justify-between">
                  <span>On time</span>
                  <span class="font-semibold text-green-600">92%</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>Overdue</span>
                  <span class="font-semibold text-amber-600">6%</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>Failed</span>
                  <span class="font-semibold text-red-600">2%</span>
                </li>
              </ul>
            </div>

            <div class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-sm font-semibold text-heading-light">User Activity</h3>
              <ul class="mt-4 space-y-3 text-text-light">
                <li class="flex items-center justify-between">
                  <span>Active today</span>
                  <span class="font-semibold text-heading-light">36</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>New signups</span>
                  <span class="font-semibold text-heading-light">12</span>
                </li>
                <li class="flex items-center justify-between">
                  <span>Churn (30d)</span>
                  <span class="font-semibold text-red-600">1.2%</span>
                </li>
              </ul>
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


