<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>UI Kit - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="UI Components - Daily Collection Manager">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
  </head>
  <body class="bg-background-light">
    <div class="flex h-screen">
      <?php $activePage = 'ui-kit'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">UI Kit</h2>
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
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Buttons -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Buttons <span class="text-xs text-text-light">(.btn .btn-primary .btn-outline)</span></h3>
              <div class="flex flex-wrap gap-3">
                <button class="btn bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">Primary</button>
                <button class="btn bg-gray-100 text-text-light px-4 py-2 rounded-lg hover:bg-gray-200">Default</button>
                <button class="btn btn-outline border border-border-light text-text-light px-4 py-2 rounded-lg hover:bg-gray-50">Outline</button>
                <button class="btn bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Danger</button>
              </div>
              <p class="mt-3 text-xs text-text-light">Classes: <code class="bg-gray-100 px-2 py-1 rounded">btn bg-primary text-white px-4 py-2 rounded-lg</code></p>
            </section>

            <!-- Badges -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Badges <span class="text-xs text-text-light">(.badge .badge-primary .badge-danger)</span></h3>
              <div class="flex flex-wrap gap-2 items-center">
                <span class="badge bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">Primary</span>
                <span class="badge bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Success</span>
                <span class="badge bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm">Warning</span>
                <span class="badge bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Danger</span>
              </div>
            </section>

            <!-- Alerts -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Alerts <span class="text-xs text-text-light">(.alert .alert-success .alert-warning .alert-danger)</span></h3>
              <div class="space-y-2">
                <div class="alert bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">Success! Your action completed.</div>
                <div class="alert bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-lg">Warning! Please check the details.</div>
                <div class="alert bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">Error! Something went wrong.</div>
              </div>
            </section>

            <!-- Inputs & Selects -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Inputs & Selects <span class="text-xs text-text-light">(.input .select .custom-select-wrapper)</span></h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" class="input w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none" placeholder="Text input">
                <div class="custom-select-wrapper">
                  <select class="select w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white cursor-pointer">
                    <option>Option A</option>
                    <option>Option B</option>
                  </select>
                  <span class="select-arrow material-icons">expand_more</span>
                </div>
              </div>
              <p class="mt-3 text-xs text-text-light">Classes: <code class="bg-gray-100 px-2 py-1 rounded">input w-full px-4 py-3 border border-border-light rounded-lg</code></p>
            </section>

            <!-- Cards -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Cards <span class="text-xs text-text-light">(.card .card-hover)</span></h3>
              <div class="card product-card bg-white rounded-lg border border-border-light overflow-hidden">
                <div class="p-4">
                  <h4 class="font-semibold text-heading-light">Card Title</h4>
                  <p class="text-text-light mt-1">Card supporting text with subtle description.</p>
                </div>
              </div>
            </section>

            <!-- Table -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Table <span class="text-xs text-text-light">(.table)</span></h3>
              <div class="overflow-x-auto">
                <table class="table w-full">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4">Name</th>
                      <th class="text-left py-3 px-4">Role</th>
                      <th class="text-left py-3 px-4">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b border-border-light">
                      <td class="py-3 px-4">Jane Cooper</td>
                      <td class="py-3 px-4">Admin</td>
                      <td class="py-3 px-4"><span class="badge bg-green-100 text-green-700 px-2 py-1 rounded">Active</span></td>
                    </tr>
                    <tr class="border-b border-border-light">
                      <td class="py-3 px-4">Guy Hawkins</td>
                      <td class="py-3 px-4">Staff</td>
                      <td class="py-3 px-4"><span class="badge bg-amber-100 text-amber-700 px-2 py-1 rounded">Pending</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>

            <!-- Tabs -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Tabs <span class="text-xs text-text-light">(.tabs .tab)</span></h3>
              <div class="tabs flex border-b border-border-light">
                <button class="tab text-primary bg-primary/10 border-b-2 border-primary px-4 py-2 rounded-t">Tab A</button>
                <button class="tab text-text-light hover:text-primary hover:bg-primary/5 px-4 py-2">Tab B</button>
              </div>
            </section>

            <!-- Pagination -->
            <section class="bg-card-light p-6 rounded-lg border border-border-light">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Pagination <span class="text-xs text-text-light">(.pagination .page)</span></h3>
              <div class="pagination inline-flex items-center gap-2">
                <button class="page px-3 py-2 border border-border-light rounded hover:bg-gray-50">Prev</button>
                <button class="page px-3 py-2 bg-primary text-white rounded">1</button>
                <button class="page px-3 py-2 border border-border-light rounded hover:bg-gray-50">2</button>
                <button class="page px-3 py-2 border border-border-light rounded hover:bg-gray-50">Next</button>
              </div>
            </section>
          </div>
        </main>
      </div>
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
  </body>
</html>
