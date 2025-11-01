<?php
/**
 * Category Management Page
 * 
 * Features:
 * - Real-time category listing from database
 * - Live product count from products table (calculated on-the-fly)
 * - No caching - always fresh data
 * - Search and filter capabilities
 * - Add/Edit/Delete categories
 * 
 * Data Source: MySQL database (SAHANALK.categories + SAHANALK.products)
 * See: admin/REALTIME_DATA_DOCUMENTATION.md for technical details
 */

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
    <style>
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }
      #liveIndicator {
        animation: pulse 2s ease-in-out infinite;
      }
    </style>
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
                <p class="font-semibold text-heading-light" id="userName"><?php echo htmlspecialchars($full_name); ?></p>
                <p class="text-sm text-text-light">Admin</p>
              </div>
            </div>
          </div>
        </header>
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
          <!-- Search and Add Section -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-heading-light">Manage Categories</h3>
              <div class="flex items-center gap-2">
                <button 
                  id="refreshBtn" 
                  class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2 font-medium"
                  title="Refresh category list"
                >
                  <span class="material-icons text-lg">refresh</span>
                  Refresh
                </button>
                <button 
                  id="addCategoryBtn" 
                  class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                >
                  <span class="material-icons text-lg">add</span>
                  Add Category
                </button>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-text-light mb-2">Search Categories</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search by category name or description..." 
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

          <!-- Category Table -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <h3 class="text-lg font-semibold text-heading-light">All Categories</h3>
                  <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-semibold" id="totalCount">0 Categories</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-text-light">
                  <span class="material-icons text-primary text-sm" id="liveIndicator" title="Live data indicator">fiber_manual_record</span>
                  <span id="lastUpdateTime">Loading...</span>
                </div>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full" id="categoryTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Category Name</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Description</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">
                        <div class="flex items-center gap-1">
                          <span>Products</span>
                          <span class="material-icons text-sm text-text-light" title="Live count from Products table">info</span>
                        </div>
                      </th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="categoryTableBody">
                    <tr id="loadingRow">
                      <td colspan="4" class="py-8 text-center text-text-light">
                        <div class="flex flex-col items-center justify-center gap-2">
                          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                          <span>Loading real-time data from database...</span>
                        </div>
                      </td>
                    </tr>
                    <tr id="emptyRow" class="hidden">
                      <td colspan="4" class="py-8 text-center text-text-light">
                        No categories found.
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

    <!-- Add/Edit Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="modal-backdrop absolute inset-0" id="modalBackdrop"></div>
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative z-10">
        <div class="p-6 border-b border-border-light">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-heading-light" id="modalTitle">Add Category</h3>
            <button id="closeModal" class="text-text-light hover:text-heading-light transition-colors">
              <span class="material-icons">close</span>
            </button>
          </div>
        </div>
        <form id="categoryForm" class="p-6 space-y-4">
          <input type="hidden" id="categoryId" name="id">
          
          <div>
            <label for="categoryName" class="block text-sm font-medium text-heading-light mb-2">Category Name *</label>
            <input 
              type="text" 
              id="categoryName" 
              name="name"
              required
              class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
              placeholder="Enter category name"
            >
          </div>
          
          <div>
            <label for="categoryDescription" class="block text-sm font-medium text-heading-light mb-2">Description</label>
            <textarea 
              id="categoryDescription" 
              name="description"
              rows="3"
              class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
              placeholder="Enter category description"
            ></textarea>
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
              id="saveCategoryBtn"
              class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center gap-2"
            >
              <span class="material-icons text-lg">save</span>
              <span id="saveBtnText">Add Category</span>
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

      // ========================================================================
      // CATEGORY MANAGEMENT - REAL-TIME DATABASE DATA LOADING
      // ========================================================================
      // This page loads categories with REAL-TIME product counts from the database.
      // Product counts are calculated live using SQL subquery from the products table.
      // NO CACHING is applied - every request fetches fresh data from MySQL.
      // See REALTIME_DATA_DOCUMENTATION.md for detailed information.
      // ========================================================================

      // Category Management Variables
      let currentEditCategoryId = null;

      // Force refresh table with fresh database data
      async function refreshCategoryTable(search = '') {
        console.log('🔄 Force refreshing category table...');
        
        const tbody = document.getElementById('categoryTableBody');
        const loadingRow = document.getElementById('loadingRow');
        
        if (loadingRow) loadingRow.classList.remove('hidden');
        if (tbody) {
          const allRows = Array.from(tbody.querySelectorAll('tr'));
          allRows.forEach(row => {
            if (row.id !== 'loadingRow' && row.id !== 'emptyRow') row.remove();
          });
        }
        
        await new Promise(resolve => setTimeout(resolve, 150));
        await loadCategories(search, true);
        console.log('✅ Table refreshed');
      }

      // Update last update time indicator
      function updateLastUpdateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
          hour: '2-digit', 
          minute: '2-digit', 
          second: '2-digit',
          hour12: true 
        });
        const lastUpdateEl = document.getElementById('lastUpdateTime');
        const liveIndicator = document.getElementById('liveIndicator');
        
        if (lastUpdateEl) {
          lastUpdateEl.textContent = `Last updated: ${timeString}`;
        }
        
        // Pulse the live indicator
        if (liveIndicator) {
          liveIndicator.style.animation = 'none';
          setTimeout(() => {
            liveIndicator.style.animation = 'pulse 1s ease-in-out';
          }, 10);
        }
      }

      // Load categories from real database
      async function loadCategories(search = '', bustCache = false) {
        try {
          console.log('📊 Fetching real-time data from database...');
          
          const params = new URLSearchParams();
          if (search) params.append('search', search);
          // Force fresh data from database
          params.append('_t', Date.now());
          params.append('_r', Math.random());
          
          const url = `api/get-categories.php?${params.toString()}`;
          
          const tbody = document.getElementById('categoryTableBody');
          const loadingRow = document.getElementById('loadingRow');
          const emptyRow = document.getElementById('emptyRow');
          
          if (!tbody) return;
          
          if (loadingRow) loadingRow.classList.remove('hidden');
          if (emptyRow) emptyRow.classList.add('hidden');
          
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: { 
              'Cache-Control': 'no-cache, no-store, must-revalidate', 
              'Pragma': 'no-cache',
              'Expires': '0'
            }
          });
          
          if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
          
          const result = await response.json();
          
          console.log(`✅ Loaded ${result.categories?.length || 0} categories with real-time product counts`);
          
          if (loadingRow) loadingRow.classList.add('hidden');
          
          // Remove existing data rows
          const allRows = Array.from(tbody.querySelectorAll('tr'));
          allRows.forEach(row => {
            if (row.id !== 'loadingRow' && row.id !== 'emptyRow') row.remove();
          });
          
          if (result.success && result.categories && result.categories.length > 0) {
            if (emptyRow) emptyRow.classList.add('hidden');
            result.categories.forEach((category) => {
              const row = createCategoryRow(category);
              if (row) tbody.appendChild(row);
            });
          } else {
            if (emptyRow) emptyRow.classList.remove('hidden');
          }
          
          // Update total count badge
          const totalCountEl = document.getElementById('totalCount');
          if (totalCountEl && result.categories) {
            const count = result.categories.length;
            totalCountEl.textContent = `${count} ${count === 1 ? 'Category' : 'Categories'}`;
          }
          
          // Update last update time
          updateLastUpdateTime();
          
        } catch (error) {
          console.error('❌ Failed to load categories:', error);
          showNotification('Failed to load categories: ' + error.message, 'error');
        }
      }

      // Create category row with real-time product count
      function createCategoryRow(category) {
        if (!category || !category.id) return null;
        
        const tr = document.createElement('tr');
        tr.className = 'border-b border-border-light hover:bg-gray-50 transition-colors';
        tr.dataset.categoryId = category.id;
        
        // Product count badge styling
        const productCount = category.product_count || 0;
        const badgeColor = productCount > 0 ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-500';
        
        tr.innerHTML = `
          <td class="py-3 px-4 text-heading-light font-medium">${escapeHtml(category.name)}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(category.description || '-')}</td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 rounded-full ${badgeColor} font-semibold text-sm" title="Real-time count from products table">
                ${productCount} ${productCount === 1 ? 'Product' : 'Products'}
              </span>
            </div>
          </td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <button class="edit-category-btn text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit Category" data-category-id="${category.id}">
                <span class="material-icons text-lg">edit</span>
              </button>
              <button class="delete-category-btn text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete Category" data-category-id="${category.id}">
                <span class="material-icons text-lg">delete</span>
              </button>
            </div>
          </td>
        `;
        
        const editBtn = tr.querySelector('.edit-category-btn');
        const deleteBtn = tr.querySelector('.delete-category-btn');
        
        if (editBtn) editBtn.addEventListener('click', () => editCategory(category.id));
        if (deleteBtn) deleteBtn.addEventListener('click', () => deleteCategory(category.id));
        
        return tr;
      }

      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

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

      const refreshBtn = document.getElementById('refreshBtn');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', async function() {
          const search = document.getElementById('searchInput').value.trim();
          refreshBtn.disabled = true;
          const originalHTML = refreshBtn.innerHTML;
          refreshBtn.innerHTML = '<span class="material-icons text-lg animate-spin">refresh</span> Refreshing...';
          try {
            await refreshCategoryTable(search);
          } finally {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalHTML;
          }
        });
      }

      const addCategoryBtn = document.getElementById('addCategoryBtn');
      if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', () => openAddModal());
      }

      document.getElementById('searchBtn').addEventListener('click', async function() {
        const search = document.getElementById('searchInput').value.trim();
        await refreshCategoryTable(search);
      });

      document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('searchBtn').click();
      });

      async function editCategory(categoryId) {
        try {
          const url = `api/get-categories.php?_t=${Date.now()}&_r=${Math.random()}`;
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
          });
          const result = await response.json();
          
          if (result.success) {
            const category = result.categories.find(c => c.id == categoryId);
            if (category) openEditModal(category);
          }
        } catch (error) {
          console.error('Failed to load category:', error);
          showNotification('Failed to load category details', 'error');
        }
      }

      async function deleteCategory(categoryId) {
        const confirmed = await showConfirmDialog({
          title: 'Delete Category?',
          message: 'Are you sure you want to delete this category? This action cannot be undone.',
          confirmText: 'Delete',
          cancelText: 'Cancel',
          type: 'danger'
        });
        
        if (!confirmed) return;
        
        try {
          const response = await fetch('api/delete-category.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: categoryId })
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('Category deleted successfully', 'success');
            const search = document.getElementById('searchInput').value.trim();
            await refreshCategoryTable(search);
          } else {
            showNotification(result.error || 'Failed to delete category', 'error');
          }
        } catch (error) {
          console.error('Delete error:', error);
          showNotification('Network error. Please try again.', 'error');
        }
      }

      function openAddModal() {
        currentEditCategoryId = null;
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('saveBtnText').textContent = 'Add Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryModal').classList.remove('hidden');
      }

      function openEditModal(category) {
        currentEditCategoryId = category.id;
        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('saveBtnText').textContent = 'Update Category';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryDescription').value = category.description || '';
        document.getElementById('categoryModal').classList.remove('hidden');
      }

      document.getElementById('closeModal').addEventListener('click', closeModal);
      document.getElementById('cancelBtn').addEventListener('click', closeModal);
      document.getElementById('modalBackdrop').addEventListener('click', closeModal);
      
      function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryForm').reset();
      }

      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('categoryModal').classList.contains('hidden')) {
          closeModal();
        }
      });

      document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveCategoryBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-icons animate-spin">hourglass_empty</span> Saving...';
        
        const formData = {
          id: document.getElementById('categoryId').value || null,
          name: document.getElementById('categoryName').value.trim(),
          description: document.getElementById('categoryDescription').value.trim()
        };
        
        try {
          const endpoint = currentEditCategoryId ? 'api/update-category.php' : 'api/add-category.php';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification(result.message || 'Category saved successfully', 'success');
            closeModal();
            const search = document.getElementById('searchInput').value.trim();
            await refreshCategoryTable(search);
          } else {
            showNotification(result.error || 'Failed to save category', 'error');
          }
        } catch (error) {
          console.error('Save error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
        }
      });

      async function initializePage() {
        console.log('🔄 Initializing category page...');
        await refreshCategoryTable('');
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePage);
      } else {
        initializePage();
      }
    </script>
  </body>
</html>
