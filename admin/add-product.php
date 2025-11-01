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
    <title>Add Product - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Add Product - Daily Collection Manager">
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
    <?php echo getDeveloperBanner(); ?>
    <div class="flex h-screen">
      <?php $activePage = 'add-product'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Add Product</h2>
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
          <div class="bg-card-light p-6 rounded-lg border border-border-light max-w-4xl mx-auto">
            <h3 class="text-lg font-semibold text-heading-light mb-6">Product Registration Form</h3>
            <form id="productForm">
              <!-- Product Image Upload -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-heading-light mb-2">Product Image</label>
                <div class="flex items-start gap-4">
                  <div id="imagePreview" class="w-32 h-32 border-2 border-dashed border-border-light rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden">
                    <span class="material-icons text-4xl text-gray-400">image</span>
                  </div>
                  <div class="flex-1">
                    <input type="file" id="productImage" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('productImage').click()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                      <span class="material-icons">upload</span>
                      Upload Image
                    </button>
                    <p class="text-xs text-text-light mt-2">Supported formats: JPG, PNG, GIF (Max 2MB)</p>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div>
                  <label for="productName" class="block text-sm font-medium text-heading-light mb-2">Product Name *</label>
                  <input type="text" id="productName" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Product ID -->
                <div>
                  <label for="productId" class="block text-sm font-medium text-heading-light mb-2">Product ID *</label>
                  <input type="text" id="productId" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Category -->
                <div>
                  <label for="category" class="block text-sm font-medium text-heading-light mb-2">Category *</label>
                  <div class="relative">
                    <select id="category" required class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white bg-none">
                      <option value="">Select Category</option>
                      <!-- Categories will be loaded dynamically -->
                    </select>
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                      <span class="material-icons">expand_more</span>
                    </span>
                  </div>
                </div>

                <!-- Supplier -->
                <div>
                  <label for="supplier" class="block text-sm font-medium text-heading-light mb-2">Supplier *</label>
                  <div class="relative">
                    <select id="supplier" required class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white bg-none">
                      <option value="">Select Supplier</option>
                      <!-- Suppliers will be loaded dynamically -->
                    </select>
                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                      <span class="material-icons">expand_more</span>
                    </span>
                  </div>
                </div>

                <!-- Buying Price -->
                <div>
                  <label for="buyingPrice" class="block text-sm font-medium text-heading-light mb-2">Buying Price *</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">Rs.</span>
                    <input type="number" id="buyingPrice" step="0.01" min="0" required class="w-full pl-10 pr-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                  </div>
                  <p class="text-xs text-text-light mt-1">Cost price you paid to supplier</p>
                </div>

                <!-- Selling Price -->
                <div>
                  <label for="sellingPrice" class="block text-sm font-medium text-heading-light mb-2">Selling Price *</label>
                  <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">Rs.</span>
                    <input type="number" id="sellingPrice" step="0.01" min="0" required class="w-full pl-10 pr-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                  </div>
                  <p class="text-xs text-text-light mt-1">Must be greater than buying price</p>
                </div>

                <!-- Quantity -->
                <div>
                  <label for="quantity" class="block text-sm font-medium text-heading-light mb-2">Quantity *</label>
                  <input type="number" id="quantity" min="0" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
              </div>

              <!-- Description -->
              <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-heading-light mb-2">Description</label>
                <textarea id="description" rows="4" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"></textarea>
              </div>

              <!-- Form Actions -->
              <div class="mt-6 flex items-center justify-end gap-4">
                  <button type="button" onclick="window.location.href='product.php'" class="px-6 py-3 border border-border-light text-text-light rounded-lg hover:bg-gray-50 transition-colors">
                  Cancel
                </button>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                  <span class="material-icons">save</span>
                  Save Product
                </button>
              </div>
            </form>
          </div>
        </main>
      </div>
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
    <script src="assets/js/notification-dialog.js"></script>
    <script>
      // Load categories from database
      async function loadCategories() {
        try {
          const response = await fetch('api/get-categories.php');
          const data = await response.json();
          
          if (data.success) {
            const categorySelect = document.getElementById('category');
            data.categories.forEach(category => {
              const option = document.createElement('option');
              option.value = category.id;
              option.textContent = category.name;
              categorySelect.appendChild(option);
            });
          } else {
            console.error('Failed to load categories:', data.error);
          }
        } catch (error) {
          console.error('Error loading categories:', error);
        }
      }

      // Load suppliers from database
      async function loadSuppliers() {
        try {
          const response = await fetch('api/get-suppliers.php');
          const data = await response.json();
          
          if (data.success) {
            const supplierSelect = document.getElementById('supplier');
            data.suppliers.forEach(supplier => {
              const option = document.createElement('option');
              option.value = supplier.id;
              option.textContent = supplier.company_name;
              supplierSelect.appendChild(option);
            });
          } else {
            console.error('Failed to load suppliers:', data.error);
          }
        } catch (error) {
          console.error('Error loading suppliers:', error);
        }
      }

      // Load data on page load
      document.addEventListener('DOMContentLoaded', function() {
        loadCategories();
        loadSuppliers();
      });

      // Image preview
      document.getElementById('productImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          // Validate file size
          if (file.size > 2 * 1024 * 1024) {
            showNotificationDialog({
              title: 'File Too Large',
              message: 'File size must be less than 2MB. Please choose a smaller image.',
              type: 'warning'
            });
            e.target.value = '';
            return;
          }
          
          const reader = new FileReader();
          reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
          };
          reader.readAsDataURL(file);
        }
      });

      // Add real-time validation for selling price
      document.getElementById('sellingPrice').addEventListener('input', function() {
        validatePrices();
      });
      
      document.getElementById('buyingPrice').addEventListener('input', function() {
        validatePrices();
      });
      
      function validatePrices() {
        const buyingPrice = parseFloat(document.getElementById('buyingPrice').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('sellingPrice').value) || 0;
        const sellingPriceInput = document.getElementById('sellingPrice');
        
        if (buyingPrice > 0 && sellingPrice > 0) {
          if (sellingPrice <= buyingPrice) {
            sellingPriceInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            sellingPriceInput.classList.remove('border-border-light', 'focus:border-primary', 'focus:ring-primary');
          } else {
            sellingPriceInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            sellingPriceInput.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
            setTimeout(() => {
              sellingPriceInput.classList.remove('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
              sellingPriceInput.classList.add('border-border-light', 'focus:border-primary', 'focus:ring-primary');
            }, 1000);
          }
        }
      }

      // Handle form submission
      document.getElementById('productForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Get values
        const buyingPrice = parseFloat(document.getElementById('buyingPrice').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('sellingPrice').value) || 0;
        
        // Validate: Selling price must be greater than buying price
        if (sellingPrice <= buyingPrice) {
          await showNotificationDialog({
            title: 'Invalid Price',
            message: `Selling Price (Rs. ${sellingPrice.toFixed(2)}) must be greater than Buying Price (Rs. ${buyingPrice.toFixed(2)}). Please adjust your prices.`,
            type: 'warning'
          });
          document.getElementById('sellingPrice').focus();
          return;
        }
        
        // Get form data
        const formData = new FormData();
        formData.append('productName', document.getElementById('productName').value);
        formData.append('productId', document.getElementById('productId').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('supplier', document.getElementById('supplier').value);
        formData.append('buyingPrice', buyingPrice);
        formData.append('sellingPrice', sellingPrice);
        formData.append('quantity', document.getElementById('quantity').value);
        formData.append('description', document.getElementById('description').value);
        
        // Add image if selected
        const imageFile = document.getElementById('productImage').files[0];
        if (imageFile) {
          formData.append('productImage', imageFile);
        }
        
        // Disable submit button
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-icons animate-spin">refresh</span> Saving...';
        
        try {
          const response = await fetch('api/add-product.php', {
            method: 'POST',
            body: formData
          });
          
          const data = await response.json();
          
          if (data.success) {
            await showNotificationDialog({
              title: 'Success!',
              message: 'Product added successfully!',
              type: 'success'
            });
            window.location.href = 'product.php';
          } else {
            await showNotificationDialog({
              title: 'Error',
              message: data.error || 'Failed to add product. Please try again.',
              type: 'error'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        } catch (error) {
          console.error('Error submitting form:', error);
          await showNotificationDialog({
            title: 'Network Error',
            message: 'An error occurred while adding the product. Please check your connection and try again.',
            type: 'error'
          });
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
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
