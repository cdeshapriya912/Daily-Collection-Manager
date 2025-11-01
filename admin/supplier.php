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
              <h3 class="text-lg font-semibold text-heading-light">Search Suppliers</h3>
              <div class="flex items-center gap-2">
                <button 
                  id="refreshBtn" 
                  class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2 font-medium"
                  title="Refresh supplier list"
                >
                  <span class="material-icons text-lg">refresh</span>
                  Refresh
                </button>
                <button 
                  id="addSupplierBtn" 
                  class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                >
                  <span class="material-icons text-lg">add</span>
                  Add Supplier
                </button>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <label for="searchInput" class="block text-sm font-medium text-text-light mb-2">Company or Person Name</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search by company name, person name, phone..." 
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

          <!-- Suppliers Table -->
          <div class="bg-card-light rounded-lg border border-border-light overflow-hidden">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Suppliers List</h3>
              <div class="overflow-x-auto md:overflow-x-visible max-w-full w-full touch-pan-x" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[800px] md:min-w-0" id="supplierTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">ID</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Company</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Contact Person</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Phone</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Email</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Action</th>
                    </tr>
                  </thead>
                  <tbody id="supplierTableBody">
                    <tr id="loadingRow">
                      <td colspan="6" class="py-8 text-center text-text-light">
                        <div class="flex flex-col items-center justify-center gap-2">
                          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                          <span>Loading suppliers...</span>
                        </div>
                      </td>
                    </tr>
                    <tr id="emptyRow" class="hidden">
                      <td colspan="6" class="py-8 text-center text-text-light">
                        No suppliers found.
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

    <!-- Add/Edit Supplier Modal -->
    <div id="supplierModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="modal-backdrop absolute inset-0" id="modalBackdrop"></div>
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative z-10">
        <div class="p-6 border-b border-border-light">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-heading-light" id="modalTitle">Add Supplier</h3>
            <button id="closeModal" class="text-text-light hover:text-heading-light transition-colors">
              <span class="material-icons">close</span>
            </button>
          </div>
        </div>
        <form id="supplierForm" class="p-6 space-y-4">
          <input type="hidden" id="supplierId" name="id">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="companyName" class="block text-sm font-medium text-heading-light mb-2">Company Name *</label>
              <input 
                type="text" 
                id="companyName" 
                name="company_name"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter company name"
              >
            </div>
            
            <div>
              <label for="personName" class="block text-sm font-medium text-heading-light mb-2">Contact Person *</label>
              <input 
                type="text" 
                id="personName" 
                name="person_name"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="Enter contact person name"
              >
            </div>
            
            <div>
              <label for="phone" class="block text-sm font-medium text-heading-light mb-2">Phone *</label>
              <input 
                type="tel" 
                id="phone" 
                name="phone"
                required
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="07X XXX XXXX"
              >
            </div>
            
            <div>
              <label for="email" class="block text-sm font-medium text-heading-light mb-2">Email</label>
              <input 
                type="email" 
                id="email" 
                name="email"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                placeholder="name@company.com"
              >
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
              id="saveSupplierBtn"
              class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors font-medium flex items-center gap-2"
            >
              <span class="material-icons text-lg">save</span>
              <span id="saveBtnText">Add Supplier</span>
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

      // Supplier Management Variables
      let currentEditSupplierId = null;

      // Force refresh table with fresh database data
      async function refreshSupplierTable(search = '') {
        console.log('🔄 Force refreshing supplier table with fresh database data...');
        
        const tbody = document.getElementById('supplierTableBody');
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
        
        // Small delay to ensure database transaction is complete
        await new Promise(resolve => setTimeout(resolve, 150));
        
        // Reload with cache-busting to force fresh data from server
        console.log('📡 Fetching fresh data from database...');
        await loadSuppliers(search, true);
        
        console.log('✅ Table refreshed with latest database data');
      }

      // Load suppliers list
      async function loadSuppliers(search = '', bustCache = false) {
        try {
          const params = new URLSearchParams();
          if (search) params.append('search', search);
          
          // ALWAYS add timestamp to prevent any caching
          params.append('_t', Date.now());
          params.append('_r', Math.random());
          
          const url = `api/get-suppliers.php?${params.toString()}`;
          console.log('Fetching suppliers from:', url);
          
          const tbody = document.getElementById('supplierTableBody');
          const loadingRow = document.getElementById('loadingRow');
          const emptyRow = document.getElementById('emptyRow');
          
          if (!tbody) {
            console.error('Table body not found!');
            return;
          }
          
          // Show loading state
          if (loadingRow) loadingRow.classList.remove('hidden');
          if (emptyRow) emptyRow.classList.add('hidden');
          
          // Force no-cache in fetch request
          const response = await fetch(url, {
            method: 'GET',
            cache: 'no-store',
            headers: {
              'Cache-Control': 'no-cache',
              'Pragma': 'no-cache'
            }
          });
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          const result = await response.json();
          console.log('📥 Suppliers API response:', {
            success: result.success,
            count: result.suppliers?.length || 0,
            timestamp: new Date().toISOString()
          });
          
          // Hide loading row
          if (loadingRow) loadingRow.classList.add('hidden');
          
          // Clear existing data rows
          const allRows = Array.from(tbody.querySelectorAll('tr'));
          allRows.forEach(row => {
            if (row.id !== 'loadingRow' && row.id !== 'emptyRow') {
              row.remove();
            }
          });
          
          if (result.success && result.suppliers && result.suppliers.length > 0) {
            // Hide empty row, show data
            if (emptyRow) emptyRow.classList.add('hidden');
            
            // Add supplier rows
            console.log(`Processing ${result.suppliers.length} suppliers from API...`);
            
            result.suppliers.forEach((supplier, index) => {
              const row = createSupplierRow(supplier);
              if (row) {
                tbody.appendChild(row);
              }
            });
            
            console.log(`✅ Successfully loaded ${result.suppliers.length} suppliers`);
          } else {
            // Show empty state
            if (emptyRow) {
              emptyRow.classList.remove('hidden');
            }
            console.log('No suppliers found in database');
          }
        } catch (error) {
          console.error('Failed to load suppliers:', error);
          showNotification('Failed to load suppliers: ' + error.message, 'error');
          
          const loadingRow = document.getElementById('loadingRow');
          const emptyRow = document.getElementById('emptyRow');
          
          if (loadingRow) loadingRow.classList.add('hidden');
          if (emptyRow) {
            emptyRow.classList.remove('hidden');
            emptyRow.innerHTML = '<td colspan="6" class="py-8 text-center text-red-600">Error loading suppliers. Please refresh the page.</td>';
          }
        }
      }

      // Create supplier table row
      function createSupplierRow(supplier) {
        if (!supplier || !supplier.id) {
          console.error('Invalid supplier data:', supplier);
          return null;
        }
        
        const tr = document.createElement('tr');
        tr.className = 'border-b border-border-light hover:bg-gray-50';
        tr.dataset.supplierId = supplier.id;
        
        tr.innerHTML = `
          <td class="py-3 px-4 text-text-light">${supplier.id}</td>
          <td class="py-3 px-4 text-heading-light font-medium">${escapeHtml(supplier.company_name)}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(supplier.contact_person)}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(supplier.phone)}</td>
          <td class="py-3 px-4 text-text-light">${escapeHtml(supplier.email || '-')}</td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <button class="edit-supplier-btn text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit" data-supplier-id="${supplier.id}">
                <span class="material-icons text-lg">edit</span>
              </button>
              <button class="delete-supplier-btn text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete" data-supplier-id="${supplier.id}">
                <span class="material-icons text-lg">delete</span>
              </button>
            </div>
          </td>
        `;
        
        // Attach event listeners
        const editBtn = tr.querySelector('.edit-supplier-btn');
        const deleteBtn = tr.querySelector('.delete-supplier-btn');
        
        if (editBtn) {
          editBtn.addEventListener('click', () => editSupplier(supplier.id));
        }
        if (deleteBtn) {
          deleteBtn.addEventListener('click', () => deleteSupplier(supplier.id));
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

      // Refresh button
      const refreshBtn = document.getElementById('refreshBtn');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', async function() {
          console.log('🔄 Manual refresh triggered');
          const search = document.getElementById('searchInput').value.trim();
          
          refreshBtn.disabled = true;
          const originalHTML = refreshBtn.innerHTML;
          refreshBtn.innerHTML = '<span class="material-icons text-lg animate-spin">refresh</span> Refreshing...';
          
          try {
            await refreshSupplierTable(search);
          } finally {
            refreshBtn.disabled = false;
            refreshBtn.innerHTML = originalHTML;
          }
        });
      }

      // Add Supplier button
      const addSupplierBtn = document.getElementById('addSupplierBtn');
      if (addSupplierBtn) {
        addSupplierBtn.addEventListener('click', function() {
          openAddModal();
        });
      }

      // Search functionality
      document.getElementById('searchBtn').addEventListener('click', async function() {
        const search = document.getElementById('searchInput').value.trim();
        console.log('🔍 Search triggered:', { search });
        await refreshSupplierTable(search);
      });

      // Search on Enter key
      document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          document.getElementById('searchBtn').click();
        }
      });

      // Edit supplier
      async function editSupplier(supplierId) {
        try {
          const url = `api/get-suppliers.php?_t=${Date.now()}&_r=${Math.random()}`;
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
            const supplier = result.suppliers.find(s => s.id == supplierId);
            if (supplier) {
              openEditModal(supplier);
            }
          }
        } catch (error) {
          console.error('Failed to load supplier:', error);
          showNotification('Failed to load supplier details', 'error');
        }
      }

      // Delete supplier
      async function deleteSupplier(supplierId) {
        // Show custom confirmation dialog
        const confirmed = await showConfirmDialog({
          title: 'Delete Supplier?',
          message: 'Are you sure you want to delete this supplier? This action cannot be undone.',
          confirmText: 'Delete',
          cancelText: 'Cancel',
          type: 'danger'
        });
        
        if (!confirmed) {
          return;
        }
        
        try {
          const response = await fetch('api/delete-supplier.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: supplierId })
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('Supplier deleted successfully', 'success');
            const search = document.getElementById('searchInput').value.trim();
            await refreshSupplierTable(search);
          } else {
            showNotification(result.error || 'Failed to delete supplier', 'error');
          }
        } catch (error) {
          console.error('Delete error:', error);
          showNotification('Network error. Please try again.', 'error');
        }
      }

      // Open add modal
      function openAddModal() {
        currentEditSupplierId = null;
        document.getElementById('modalTitle').textContent = 'Add Supplier';
        document.getElementById('saveBtnText').textContent = 'Add Supplier';
        document.getElementById('supplierForm').reset();
        document.getElementById('supplierId').value = '';
        document.getElementById('supplierModal').classList.remove('hidden');
      }

      // Open edit modal
      function openEditModal(supplier) {
        currentEditSupplierId = supplier.id;
        document.getElementById('modalTitle').textContent = 'Edit Supplier';
        document.getElementById('saveBtnText').textContent = 'Update Supplier';
        
        // Populate form
        document.getElementById('supplierId').value = supplier.id;
        document.getElementById('companyName').value = supplier.company_name;
        document.getElementById('personName').value = supplier.contact_person;
        document.getElementById('phone').value = supplier.phone;
        document.getElementById('email').value = supplier.email || '';
        
        document.getElementById('supplierModal').classList.remove('hidden');
      }

      // Close modal
      document.getElementById('closeModal').addEventListener('click', closeModal);
      document.getElementById('cancelBtn').addEventListener('click', closeModal);
      document.getElementById('modalBackdrop').addEventListener('click', closeModal);
      
      function closeModal() {
        document.getElementById('supplierModal').classList.add('hidden');
        document.getElementById('supplierForm').reset();
      }

      // Close modal on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('supplierModal').classList.contains('hidden')) {
          closeModal();
        }
      });

      // Handle form submission
      document.getElementById('supplierForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const saveBtn = document.getElementById('saveSupplierBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-icons animate-spin">hourglass_empty</span> Saving...';
        
        const formData = {
          id: document.getElementById('supplierId').value || null,
          company_name: document.getElementById('companyName').value.trim(),
          contact_person: document.getElementById('personName').value.trim(),
          phone: document.getElementById('phone').value.trim(),
          email: document.getElementById('email').value.trim()
        };
        
        try {
          const endpoint = currentEditSupplierId ? 'api/update-supplier.php' : 'api/add-supplier.php';
          const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification(result.message || 'Supplier saved successfully', 'success');
            closeModal();
            
            // Force a complete refresh of the table
            console.log('🔄 Refreshing table with fresh data from database...');
            const search = document.getElementById('searchInput').value.trim();
            await refreshSupplierTable(search);
          } else {
            showNotification(result.error || 'Failed to save supplier', 'error');
          }
        } catch (error) {
          console.error('Save error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
        }
      });

      // Initialize page
      async function initializePage() {
        console.log('🔄 Initializing supplier page...');
        await refreshSupplierTable('');
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
