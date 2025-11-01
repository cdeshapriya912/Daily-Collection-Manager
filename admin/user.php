<?php
// Prevent caching to avoid stale HTML/JS being served
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Admin-only page - requires admin role
// Staff users will be redirected to collection panel
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
    <title>Staff - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="User Management - Daily Collection Manager">
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
      <?php $activePage = 'user'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Staff</h2>
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
              <h3 class="text-lg font-semibold text-heading-light">Search Staff</h3>
              <div class="flex items-center gap-2">
                <button 
                  id="refreshBtn" 
                  class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2 font-medium"
                  title="Refresh staff list"
                >
                  <span class="material-icons text-lg">refresh</span>
                  Refresh
                </button>
                <button 
                  id="addStaffBtn" 
                  class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                >
                  <span class="material-icons text-lg">add</span>
                  Add Staff
                </button>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="w-full sm:w-48">
                <label for="roleSelect" class="block text-sm font-medium text-text-light mb-2">Role</label>
                <div class="relative">
                  <select 
                    id="roleSelect" 
                    class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="">All Roles</option>
                    <!-- Options will be loaded dynamically -->
                  </select>
                  <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-text-light">
                    <span class="material-icons">expand_more</span>
                  </span>
                </div>
              </div>
              <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-text-light mb-2">Staff Name or ID</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Enter staff name or ID..." 
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

          <!-- Staff Table -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Staff List</h3>
              <div class="overflow-x-auto">
                <table class="w-full" id="staffTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">ID</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Photo</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Name</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Email</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Phone</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Role</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Status</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="staffTableBody">
                    <!-- Table rows will be loaded dynamically -->
                    <tr id="loadingRow">
                      <td colspan="8" class="py-8 text-center text-text-light">
                        <div class="flex flex-col items-center justify-center gap-2">
                          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                          <span>Loading staff members...</span>
                        </div>
                      </td>
                    </tr>
                    <tr id="emptyRow" class="hidden">
                      <td colspan="8" class="py-8 text-center text-text-light">
                        No staff members found.
                      </td>
                    </tr>
                    <?php
                      // Server-side fallback rendering: show current database users immediately
                      try {
                        require_once __DIR__ . '/config/db.php';
                        $stmt = $pdo->query(
                          "SELECT u.id, u.username, u.full_name, u.email, u.mobile, u.role_id, r.name AS role_name, u.status
                           FROM users u
                           LEFT JOIN roles r ON u.role_id = r.id
                           ORDER BY u.full_name ASC"
                        );
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($users && count($users) > 0) {
                          foreach ($users as $u) {
                            $id = (int)$u['id'];
                            $fullName = htmlspecialchars($u['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
                            $email = htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8');
                            $mobile = htmlspecialchars($u['mobile'] ?? '', ENT_QUOTES, 'UTF-8');
                            $roleName = htmlspecialchars($u['role_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
                            $status = htmlspecialchars($u['status'] ?? 'active', ENT_QUOTES, 'UTF-8');
                            $roleLower = strtolower($u['role_name'] ?? '');
                            $roleClass = $roleLower === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800';
                            $statusClass = 'bg-gray-100 text-gray-800';
                            if ($status === 'active') { $statusClass = 'bg-green-100 text-green-800'; }
                            elseif ($status === 'suspended') { $statusClass = 'bg-amber-100 text-amber-800'; }
                    ?>
                    <tr class="border-b border-border-light hover:bg-gray-50" data-user-id="<?php echo $id; ?>">
                      <td class="py-3 px-4 text-text-light"><?php echo $id; ?></td>
                      <td class="py-3 px-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                          <span class="material-icons text-gray-400">person</span>
                        </div>
                      </td>
                      <td class="py-3 px-4 text-heading-light font-medium"><?php echo $fullName; ?></td>
                      <td class="py-3 px-4 text-text-light"><?php echo $email !== '' ? $email : '-'; ?></td>
                      <td class="py-3 px-4 text-text-light"><?php echo $mobile !== '' ? $mobile : '-'; ?></td>
                      <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $roleClass; ?>">
                          <?php echo ucfirst($roleName); ?>
                        </span>
                      </td>
                      <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                          <?php echo ucfirst($status); ?>
                        </span>
                      </td>
                      <td class="py-3 px-4">
                        <div class="flex items-center gap-2">
                          <button class="edit-user-btn text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit" data-user-id="<?php echo $id; ?>">
                            <span class="material-icons text-lg">edit</span>
                          </button>
                          <button class="delete-user-btn text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete" data-user-id="<?php echo $id; ?>">
                            <span class="material-icons text-lg">delete</span>
                          </button>
                        </div>
                      </td>
                    </tr>
                    <?php
                          }
                        }
                      } catch (Throwable $e) {
                        // Silent fail for server-side fallback; JS will load via API
                      }
                    ?>
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

    <!-- Add/Edit Staff Modal -->
    <div id="staffModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="modal-backdrop absolute inset-0" id="modalBackdrop"></div>
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative z-10">
        <div class="p-6 border-b border-border-light">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-heading-light" id="modalTitle">Add Staff Member</h3>
            <button id="closeModal" class="text-text-light hover:text-heading-light transition-colors">
              <span class="material-icons">close</span>
            </button>
          </div>
        </div>
        <form id="staffForm" class="p-6 space-y-4">
          <input type="hidden" id="userId" name="id">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="staffUsername" class="block text-sm font-medium text-heading-light mb-2">Username *</label>
              <input 
                type="text" 
                id="staffUsername" 
                name="username"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter username"
              >
            </div>
            
            <div>
              <label for="staffFullName" class="block text-sm font-medium text-heading-light mb-2">Full Name *</label>
              <input 
                type="text" 
                id="staffFullName" 
                name="full_name"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter full name"
              >
            </div>
            
            <div>
              <label for="staffEmail" class="block text-sm font-medium text-heading-light mb-2">Email</label>
              <input 
                type="email" 
                id="staffEmail" 
                name="email"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter email"
              >
            </div>
            
            <div>
              <label for="staffMobile" class="block text-sm font-medium text-heading-light mb-2">Phone</label>
              <input 
                type="tel" 
                id="staffMobile" 
                name="mobile"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter phone number"
              >
            </div>
            
            <div>
              <label for="staffRole" class="block text-sm font-medium text-heading-light mb-2">Role *</label>
              <select 
                id="staffRole" 
                name="role_id"
                required
                class="role-select w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white"
              >
                <!-- Options will be populated by JavaScript -->
              </select>
            </div>
            
            <div>
              <label for="staffStatus" class="block text-sm font-medium text-heading-light mb-2">Status *</label>
              <select 
                id="staffStatus" 
                name="status"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white"
              >
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label for="staffPassword" class="block text-sm font-medium text-heading-light mb-2">
                Password <span id="passwordRequired" class="text-red-500">*</span>
                <span class="text-xs text-text-light font-normal ml-2 hidden" id="passwordHint">(Leave blank to keep current password)</span>
              </label>
              <input 
                type="password" 
                id="staffPassword" 
                name="password"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter password"
                minlength="6"
              >
              <p class="text-xs text-text-light mt-1">Minimum 6 characters</p>
            </div>
          </div>
          
          <div class="flex items-center justify-end gap-4 pt-4 border-t border-border-light">
            <button 
              type="button" 
              id="cancelBtn"
              class="px-6 py-3 border border-border-light rounded-lg text-text-light hover:bg-gray-50 transition-colors font-medium"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              id="saveStaffBtn"
              class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center gap-2"
            >
              <span class="material-icons text-lg">save</span>
              <span id="saveBtnText">Add Staff</span>
            </button>
          </div>
        </form>
      </div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    
    <!-- Custom Confirmation Dialog Module -->
    <script src="assets/js/confirmation-dialog.js?v=<?php echo time(); ?>"></script>
    
    <script src="js/app.js?v=15" defer></script>
    <script>
      // Force immediate execution - don't wait for anything
      (function() {
        console.log('🚀 Script executing at:', new Date().toISOString());
        
        // Clear any existing table data immediately
        setTimeout(function() {
          const tbody = document.getElementById('staffTableBody');
          if (tbody) {
            // Remove all rows except loadingRow and emptyRow
            const allRows = Array.from(tbody.querySelectorAll('tr'));
            allRows.forEach(row => {
              if (row.id !== 'loadingRow' && row.id !== 'emptyRow' && !row.hasAttribute('data-user-id')) {
                // If it's not one of our special rows and doesn't have data-user-id, remove it
                const hasUserData = row.querySelector('[data-user-id]');
                if (!hasUserData && row.id !== 'loadingRow' && row.id !== 'emptyRow') {
                  console.log('Removing unexpected row:', row);
                  row.remove();
                }
              }
            });
          }
        }, 50);
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

      // Staff Management Variables
      let roles = [];
      let currentEditUserId = null;

      // Load roles on page load
      async function loadRoles() {
        try {
          const url = `api/get-roles.php?_t=${Date.now()}&_r=${Math.random()}`;
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: {
              'Cache-Control': 'no-cache',
              'Pragma': 'no-cache'
            }
          });
          console.log('Load roles response:', response);
          const result = await response.json();
          
          if (result.success) {
            roles = result.roles;
            const roleSelect = document.getElementById('roleSelect');
            
            // Clear existing options except "All Roles"
            roleSelect.innerHTML = '<option value="">All Roles</option>';
            
            // Add role options
            roles.forEach(role => {
              const option = document.createElement('option');
              option.value = role.id;
              option.textContent = role.name.charAt(0).toUpperCase() + role.name.slice(1);
              roleSelect.appendChild(option);
            });
            
            // Also populate role select in modals
            populateRoleSelects();
          }
        } catch (error) {
          console.error('Failed to load roles:', error);
        }
      }

      // Populate role selects in add/edit modals
      function populateRoleSelects() {
        const roleSelects = document.querySelectorAll('.role-select');
        roleSelects.forEach(select => {
          const currentValue = select.value;
          select.innerHTML = '';
          roles.forEach(role => {
            const option = document.createElement('option');
            option.value = role.id;
            option.textContent = role.name.charAt(0).toUpperCase() + role.name.slice(1);
            if (option.value == currentValue) {
              option.selected = true;
            }
            select.appendChild(option);
          });
        });
      }

      // Force refresh table with fresh database data
      async function refreshStaffTable(search = '', roleId = '') {
        console.log('🔄 Force refreshing staff table with fresh database data...');
        
        const tbody = document.getElementById('staffTableBody');
        const loadingRow = document.getElementById('loadingRow');
        
        // Show loading indicator
        if (loadingRow) {
          loadingRow.classList.remove('hidden');
        }
        
        // Clear the entire table first (except loading/empty rows)
        if (tbody) {
          const allRows = Array.from(tbody.querySelectorAll('tr'));
          let clearedCount = 0;
          allRows.forEach(row => {
            if (row.id !== 'loadingRow' && row.id !== 'emptyRow') {
              row.remove();
              clearedCount++;
            }
          });
          console.log(`🧹 Cleared ${clearedCount} rows before refresh`);
        }
        
        // Small delay to ensure database transaction is complete and show loading briefly
        await new Promise(resolve => setTimeout(resolve, 150));
        
        // Reload with cache-busting to force fresh data from server
        console.log('📡 Fetching fresh data from database...');
        await loadStaff(search, roleId, true);
        
        console.log('✅ Table refreshed with latest database data');
      }
      
      // Load staff list
      async function loadStaff(search = '', roleId = '', bustCache = false) {
        try {
          const params = new URLSearchParams();
          if (search) params.append('search', search);
          if (roleId) params.append('role_id', roleId);
          
          // ALWAYS add timestamp to prevent any caching
          params.append('_t', Date.now());
          params.append('_r', Math.random()); // Extra randomness
          
          const url = `api/get-users.php?${params.toString()}`;
          console.log('Fetching users from:', url);
          
          const tbody = document.getElementById('staffTableBody');
          const loadingRow = document.getElementById('loadingRow');
          const emptyRow = document.getElementById('emptyRow');
          
          if (!tbody) {
            console.error('Table body not found!');
            return;
          }
          
          // Show loading state only if there are no existing rows
          const hasExistingRows = document.querySelectorAll('#staffTableBody tr[data-user-id]').length > 0;
          if (loadingRow) {
            if (hasExistingRows) {
              loadingRow.classList.add('hidden');
            } else {
              loadingRow.classList.remove('hidden');
            }
          }
          if (emptyRow) emptyRow.classList.add('hidden');
          
          // Force no-cache in fetch request
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store', // Never use cached response
            headers: {
              'Cache-Control': 'no-cache',
              'Pragma': 'no-cache'
            }
          });
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          const result = await response.json();
          console.log('📥 Users API response received:', {
            success: result.success,
            count: result.users?.length || 0,
            timestamp: new Date().toISOString(),
            cacheBusted: bustCache
          });
          
          if (bustCache) {
            console.log('✨ Fresh data loaded from database (cache bypassed)');
          }
          
          // Hide loading row immediately after getting response
          if (loadingRow) loadingRow.classList.add('hidden');
          
          // Clear ALL existing rows (remove everything including any mock data)
          // Keep only loadingRow and emptyRow by ID
          const allRows = Array.from(tbody.querySelectorAll('tr'));
          let removedCount = 0;
          allRows.forEach(row => {
            // Only keep loadingRow and emptyRow
            if (row.id !== 'loadingRow' && row.id !== 'emptyRow') {
              // Double check - remove anything that looks like data rows
              const hasUserData = row.hasAttribute('data-user-id');
              const hasEditButton = row.querySelector('.edit-user-btn');
              const hasDeleteButton = row.querySelector('.delete-user-btn');
              const hasExampleEmail = row.textContent.includes('@example.com');
              const hasMockData = row.textContent.includes('John Doe') || 
                                  row.textContent.includes('Jane Smith') ||
                                  row.textContent.includes('Mike Johnson') ||
                                  row.textContent.includes('Sarah Williams') ||
                                  row.textContent.includes('S001') ||
                                  row.textContent.includes('S002') ||
                                  row.textContent.includes('S003') ||
                                  row.textContent.includes('S004');
              
              // Remove if it's a data row (has user data or action buttons) or contains mock data
              if (hasUserData || hasEditButton || hasDeleteButton || hasExampleEmail || hasMockData) {
                console.log('🗑️ Removing row with mock/old data:', row.textContent.substring(0, 50));
                row.remove();
                removedCount++;
              }
            }
          });
          
          console.log(`🧹 Cleared ${removedCount} existing rows, preparing to add new data...`);
          
          if (result.success && result.users && result.users.length > 0) {
            // Hide empty row, show data
            if (emptyRow) emptyRow.classList.add('hidden');
            
            // Add user rows
            let addedCount = 0;
            console.log(`Processing ${result.users.length} users from API...`);
            
            result.users.forEach((user, index) => {
              console.log(`Creating row ${index + 1} for user:`, user);
              const row = createStaffRow(user);
              if (row) {
                tbody.appendChild(row);
                addedCount++;
                console.log(`✅ Row ${index + 1} added successfully`);
              } else {
                console.error(`❌ Failed to create row for user:`, user);
              }
            });
            
            console.log(`✅ Successfully loaded and displayed ${addedCount} of ${result.users.length} staff members`);
            
            // Force a visual update
            tbody.style.display = 'none';
            tbody.offsetHeight; // Trigger reflow
            tbody.style.display = '';
          } else {
            // Show empty state
            if (emptyRow) {
              emptyRow.classList.remove('hidden');
              emptyRow.innerHTML = '<td colspan="8" class="py-8 text-center text-text-light">No staff members found.</td>';
            }
            console.log('No staff members found in database');
            
            // If there was an error message, log it
            if (result.error) {
              console.error('API returned error:', result.error);
            }
          }
        } catch (error) {
          console.error('Failed to load staff:', error);
          showNotification('Failed to load staff members: ' + error.message, 'error');
          
          const loadingRow = document.getElementById('loadingRow');
          const emptyRow = document.getElementById('emptyRow');
          
          if (loadingRow) loadingRow.classList.add('hidden');
          if (emptyRow) {
            emptyRow.classList.remove('hidden');
            emptyRow.innerHTML = '<td colspan="8" class="py-8 text-center text-red-600">Error loading staff members. Please refresh the page.</td>';
          }
        }
      }

      // Create staff table row
      function createStaffRow(user) {
        if (!user || !user.id) {
          console.error('Invalid user data:', user);
          return null;
        }
        
        // Log the exact data being displayed
        console.log(`📊 Creating row for User ID ${user.id}:`, {
          id: user.id,
          name: user.full_name,
          email: user.email,
          mobile: user.mobile,
          role: user.role_name,
          status: user.status
        });
        
        const tr = document.createElement('tr');
        tr.className = 'border-b border-border-light hover:bg-gray-50';
        tr.dataset.userId = user.id;
        
        // Status badge colors
        const statusColors = {
          'active': 'bg-green-100 text-green-800',
          'disabled': 'bg-gray-100 text-gray-800',
          'suspended': 'bg-amber-100 text-amber-800'
        };
        
        // Role badge colors
        const roleColors = {
          'admin': 'bg-purple-100 text-purple-800',
          'staff': 'bg-blue-100 text-blue-800'
        };
        
        const statusClass = statusColors[user.status] || 'bg-gray-100 text-gray-800';
        const roleName = (user.role_name || 'Unknown').toLowerCase();
        const roleClass = roleColors[roleName] || 'bg-gray-100 text-gray-800';
        const displayRoleName = user.role_name ? (user.role_name.charAt(0).toUpperCase() + user.role_name.slice(1)) : 'Unknown';
        
        tr.innerHTML = `
          <td class="py-3 px-4 text-text-light">${user.id}</td>
          <td class="py-3 px-4">
            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
              <span class="material-icons text-gray-400">person</span>
            </div>
          </td>
          <td class="py-3 px-4 text-heading-light font-medium">${escapeHtml(user.full_name)}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(user.email || '-')}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(user.mobile || '-')}</td>
          <td class="py-3 px-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${roleClass}">
              ${escapeHtml(displayRoleName)}
            </span>
          </td>
          <td class="py-3 px-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
              ${escapeHtml((user.status || '').charAt(0).toUpperCase() + (user.status || '').slice(1))}
            </span>
          </td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <button class="edit-user-btn text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit" data-user-id="${user.id}">
                <span class="material-icons text-lg">edit</span>
              </button>
              <button class="delete-user-btn text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete" data-user-id="${user.id}">
                <span class="material-icons text-lg">delete</span>
              </button>
            </div>
          </td>
        `;
        
        // Attach event listeners
        const editBtn = tr.querySelector('.edit-user-btn');
        const deleteBtn = tr.querySelector('.delete-user-btn');
        
        if (editBtn) {
          editBtn.addEventListener('click', () => editUser(user.id));
        }
        if (deleteBtn) {
          deleteBtn.addEventListener('click', () => deleteUser(user.id));
        }
        
        return tr;
      }

      // Escape HTML to prevent XSS
      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // Show notification
      function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 ${
          type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
          <span class="material-icons">${type === 'success' ? 'check_circle' : 'error'}</span>
          <span>${escapeHtml(message)}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
          notification.style.transition = 'opacity 0.3s';
          notification.style.opacity = '0';
          setTimeout(() => notification.remove(), 300);
        }, 3000);
      }

      // Refresh button - reload staff list with fresh data
      const refreshBtn = document.getElementById('refreshBtn');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', async function() {
          console.log('🔄 Manual refresh triggered');
          const search = document.getElementById('searchInput').value.trim();
          const roleId = document.getElementById('roleSelect').value;
          
          // Disable button and show loading state
          refreshBtn.disabled = true;
          const originalHTML = refreshBtn.innerHTML;
          refreshBtn.innerHTML = '<span class="material-icons text-lg animate-spin">refresh</span> Refreshing...';
          
          try {
            await refreshStaffTable(search, roleId);
          } finally {
            // Re-enable button
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalHTML;
          }
        });
      }
      
      // Add Staff button
      const addStaffBtn = document.getElementById('addStaffBtn');
      if (addStaffBtn) {
        addStaffBtn.addEventListener('click', function() {
          openAddModal();
        });
      }
      
      // Search functionality
      document.getElementById('searchBtn').addEventListener('click', async function() {
        const search = document.getElementById('searchInput').value.trim();
        const roleId = document.getElementById('roleSelect').value;
        console.log('🔍 Search triggered:', { search, roleId });
        await refreshStaffTable(search, roleId);
      });

      // Search on Enter key
      document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          document.getElementById('searchBtn').click();
        }
      });

      // Filter by role change - use refresh to ensure clean reload
      document.getElementById('roleSelect').addEventListener('change', async function() {
        const search = document.getElementById('searchInput').value.trim();
        const roleId = this.value;
        console.log('🎯 Role filter changed:', { 
          roleId: roleId || 'All Roles', 
          search: search || 'none' 
        });
        await refreshStaffTable(search, roleId);
      });

      // Edit user
      async function editUser(userId) {
        try {
          // Get user details by fetching all users and finding the one
          const url = `api/get-users.php?_t=${Date.now()}&_r=${Math.random()}`;
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: {
              'Cache-Control': 'no-cache',
              'Pragma': 'no-cache'
            }
          });
          const result = await response.json();
          
          if (result.success) {
            const user = result.users.find(u => u.id == userId);
            if (user) {
              openEditModal(user);
            }
          }
        } catch (error) {
          console.error('Failed to load user:', error);
          showNotification('Failed to load user details', 'error');
        }
      }

      // Delete user
      async function deleteUser(userId) {
        // Show custom confirmation dialog
        const confirmed = await showConfirmDialog({
          title: 'Delete Staff Member?',
          message: 'Are you sure you want to delete this staff member? This action cannot be undone.',
          confirmText: 'Delete',
          cancelText: 'Cancel',
          type: 'danger'
        });
        
        if (!confirmed) {
          return;
        }
        
        try {
          const response = await fetch('api/delete-user.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: userId })
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('Staff member deleted successfully', 'success');
            // Force refresh to show updated data from database
            const search = document.getElementById('searchInput').value.trim();
            const roleId = document.getElementById('roleSelect').value;
            await refreshStaffTable(search, roleId);
          } else {
            showNotification(result.error || 'Failed to delete staff member', 'error');
          }
        } catch (error) {
          console.error('Delete error:', error);
          showNotification('Network error. Please try again.', 'error');
        }
      }

      // Open add modal
      function openAddModal() {
        currentEditUserId = null;
        document.getElementById('modalTitle').textContent = 'Add Staff Member';
        document.getElementById('saveBtnText').textContent = 'Add Staff';
        document.getElementById('passwordRequired').classList.remove('hidden');
        document.getElementById('passwordHint').classList.add('hidden');
        document.getElementById('staffForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('staffUsername').disabled = false;
        document.getElementById('staffPassword').required = true;
        document.getElementById('staffModal').classList.remove('hidden');
        populateRoleSelects();
      }

      // Open edit modal
      function openEditModal(user) {
        currentEditUserId = user.id;
        document.getElementById('modalTitle').textContent = 'Edit Staff Member';
        document.getElementById('saveBtnText').textContent = 'Update Staff';
        document.getElementById('passwordRequired').classList.add('hidden');
        document.getElementById('passwordHint').classList.remove('hidden');
        
        // Populate form
        document.getElementById('userId').value = user.id;
        document.getElementById('staffUsername').value = user.username;
        document.getElementById('staffUsername').disabled = true; // Username cannot be changed
        document.getElementById('staffFullName').value = user.full_name;
        document.getElementById('staffEmail').value = user.email || '';
        document.getElementById('staffMobile').value = user.mobile || '';
        document.getElementById('staffRole').value = user.role_id;
        document.getElementById('staffStatus').value = user.status;
        document.getElementById('staffPassword').value = '';
        document.getElementById('staffPassword').required = false;
        
        populateRoleSelects();
        document.getElementById('staffModal').classList.remove('hidden');
      }

      // Close modal
      document.getElementById('closeModal').addEventListener('click', closeModal);
      document.getElementById('cancelBtn').addEventListener('click', closeModal);
      document.getElementById('modalBackdrop').addEventListener('click', closeModal);
      
      function closeModal() {
        document.getElementById('staffModal').classList.add('hidden');
        document.getElementById('staffForm').reset();
        document.getElementById('staffUsername').disabled = false;
        document.getElementById('staffPassword').required = true;
      }

      // Close modal on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('staffModal').classList.contains('hidden')) {
          closeModal();
        }
      });

      // Handle form submission
      document.getElementById('staffForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveStaffBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-icons animate-spin">hourglass_empty</span> Saving...';
        
        const formData = {
          id: document.getElementById('userId').value || null,
          username: document.getElementById('staffUsername').value.trim(),
          full_name: document.getElementById('staffFullName').value.trim(),
          email: document.getElementById('staffEmail').value.trim(),
          mobile: document.getElementById('staffMobile').value.trim(),
          role_id: parseInt(document.getElementById('staffRole').value),
          status: document.getElementById('staffStatus').value,
          password: document.getElementById('staffPassword').value
        };
        
        // Validate password for new users
        if (!currentEditUserId && !formData.password) {
          showNotification('Password is required for new staff members', 'error');
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
          return;
        }
        
        try {
          const endpoint = currentEditUserId ? 'api/update-user.php' : 'api/add-user.php';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification(result.message || 'Staff member saved successfully', 'success');
            closeModal();
            
            // Force a complete refresh of the table with fresh database data
            console.log('🔄 Refreshing table with fresh data from database...');
            
            // Clear search filters to show all data
            const search = document.getElementById('searchInput').value.trim();
            const roleId = document.getElementById('roleSelect').value;
            
            // Add cache-busting parameter to force fresh data
            await refreshStaffTable(search, roleId);
          } else {
            showNotification(result.error || 'Failed to save staff member', 'error');
          }
        } catch (error) {
          console.error('Save error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
        }
      });

      // Initialize page - ensure it runs with multiple triggers
      async function initializePage() {
        console.log('🔄 Initializing staff page...');
        console.log('Current URL:', window.location.href);
        console.log('Table body exists:', !!document.getElementById('staffTableBody'));
        
        try {
          // Load roles first
          await loadRoles();
          console.log('✅ Roles loaded successfully');
        } catch (error) {
          console.error('❌ Failed to load roles:', error);
        }
        
        // Always use refreshStaffTable for clean initial load
        console.log('📊 Loading initial staff data with fresh refresh...');
        await refreshStaffTable('', ''); // Empty search, all roles
      }
      
      // Initialize once when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePage);
      } else {
        initializePage();
      }
    </script>
  </body>
</html>
