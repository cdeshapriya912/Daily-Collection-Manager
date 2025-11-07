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
    <title>Assign Installment - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Assign Installment - Daily Collection Manager">
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
      <?php $activePage = 'assign-installment'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
              <span class="material-icons">menu</span>
            </button>
            <button onclick="history.back()" class="flex items-center gap-2 text-text-light hover:text-primary transition-colors">
              <span class="material-icons">arrow_back</span>
              <span>Back</span>
            </button>
          </div>
          <h2 class="text-2xl font-bold text-heading-light">Assign Installment</h2>
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
            <h3 class="text-lg font-semibold text-heading-light mb-6">Assign Product to Customer</h3>
            
            <!-- Step Indicators -->
            <div class="mb-8 flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-border-light">
              <div class="flex items-center gap-2 flex-1">
                <div id="step1Indicator" class="flex items-center gap-2">
                  <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-semibold text-sm">1</span>
                  <span class="text-sm text-text-light">Select Customer</span>
                </div>
                <div class="flex-1 h-px bg-border-light mx-2"></div>
              </div>
              <div class="flex items-center gap-2 flex-1">
                <div id="step2Indicator" class="flex items-center gap-2">
                  <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-semibold text-sm">2</span>
                  <span class="text-sm text-text-light">Add Products</span>
                </div>
                <div class="flex-1 h-px bg-border-light mx-2"></div>
              </div>
              <div class="flex items-center gap-2 flex-1">
                <div id="step3Indicator" class="flex items-center gap-2">
                  <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-semibold text-sm">3</span>
                  <span class="text-sm text-text-light">Select Period</span>
                </div>
                <div class="flex-1 h-px bg-border-light mx-2"></div>
              </div>
              <div class="flex items-center gap-2">
                <div id="step4Indicator" class="flex items-center gap-2">
                  <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-semibold text-sm">4</span>
                  <span class="text-sm text-text-light">Review & Assign</span>
                </div>
              </div>
            </div>
            
            <!-- Customer Selection Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-heading-light mb-2">Select Customer *</label>
              <div class="relative">
                <input 
                  type="text" 
                  id="customerSearch" 
                  placeholder="Search customer by name, code, mobile, or email..."
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                <span class="material-icons absolute right-3 top-3 text-text-light">search</span>
              </div>
              <div id="customerResults" class="hidden mt-2 border border-border-light rounded-lg bg-white max-h-60 overflow-y-auto"></div>
              <div id="selectedCustomer" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-semibold text-heading-light" id="selectedCustomerName">-</p>
                    <p class="text-sm text-text-light" id="selectedCustomerDetails">-</p>
                  </div>
                  <button id="clearCustomer" class="text-red-600 hover:text-red-700">
                    <span class="material-icons">close</span>
                  </button>
                </div>
                <div id="eligibilityStatus" class="mt-2"></div>
              </div>
            </div>

            <!-- Product Selection Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-heading-light mb-2">Select Products *</label>
              <p class="text-xs text-text-light mb-2">Search and click "Add to List" for each product you want to assign</p>
              <div class="relative">
                <input 
                  type="text" 
                  id="productSearch" 
                  placeholder="Search product by name, SKU, or description..."
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                <span class="material-icons absolute right-3 top-3 text-text-light">search</span>
              </div>
              <div id="productResults" class="hidden mt-2 border border-border-light rounded-lg bg-white max-h-60 overflow-y-auto shadow-lg"></div>
              
              <!-- Selected Products List -->
              <div id="selectedProductsContainer" class="hidden mt-4">
                <div class="flex items-center justify-between mb-3">
                  <p class="text-sm font-medium text-heading-light">Selected Products:</p>
                  <button id="clearAllProducts" class="text-sm text-red-600 hover:text-red-700 flex items-center gap-1">
                    <span class="material-icons text-sm">delete</span>
                    Clear All
                  </button>
                </div>
                <div id="selectedProductsList" class="space-y-3"></div>
              </div>
              
              <!-- Immediate Product Summary (shows as soon as products are added) -->
              <div id="productSummarySection" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-semibold text-heading-light mb-3 flex items-center gap-2">
                  <span class="material-icons text-primary">shopping_cart</span>
                  Product Summary
                </h4>
                <div class="space-y-2">
                  <div id="productSummaryBreakdown" class="mb-2 space-y-1 text-sm text-text-light"></div>
                  <div class="flex justify-between items-center pt-2 border-t border-blue-300">
                    <span class="text-lg font-medium text-heading-light">Total Products Amount:</span>
                    <span class="text-2xl font-bold text-primary" id="productTotalAmount">Rs. 0.00</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-text-light">Total Quantity:</span>
                    <span class="font-semibold text-heading-light" id="productTotalQuantity">0</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Installment Period Selection -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-heading-light mb-2">Installment Period *</label>
              <div class="grid grid-cols-2 gap-4">
                <button 
                  id="period30" 
                  class="installment-period-btn p-4 border-2 border-border-light rounded-lg hover:border-primary transition-colors text-center"
                  data-period="30"
                >
                  <span class="material-icons text-3xl text-primary">calendar_today</span>
                  <p class="font-semibold text-heading-light mt-2">30 Days</p>
                  <p class="text-sm text-text-light">Daily Payment</p>
                </button>
                <button 
                  id="period60" 
                  class="installment-period-btn p-4 border-2 border-border-light rounded-lg hover:border-primary transition-colors text-center"
                  data-period="60"
                >
                  <span class="material-icons text-3xl text-primary">event</span>
                  <p class="font-semibold text-heading-light mt-2">60 Days</p>
                  <p class="text-sm text-text-light">Daily Payment</p>
                </button>
              </div>
            </div>

            <!-- Assignment Date -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-heading-light mb-2">Assignment Date *</label>
              <input 
                type="date" 
                id="assignmentDate" 
                value="<?php echo date('Y-m-d'); ?>"
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              >
            </div>

            <!-- Installment Calculation Summary (shows after period selection) -->
            <div id="installmentSummarySection" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
              <h4 class="font-semibold text-heading-light mb-3 flex items-center gap-2">
                <span class="material-icons text-primary">schedule</span>
                Installment Calculation
              </h4>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-text-light">Installment Period:</span>
                  <span class="font-semibold" id="summaryPeriod">-</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-green-300">
                  <span class="text-lg font-medium text-heading-light">Daily Payment Amount:</span>
                  <span class="text-2xl font-bold text-primary" id="summaryDaily">Rs. 0.00</span>
                </div>
              </div>
            </div>

            <!-- Notes Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-heading-light mb-2">Notes (Optional)</label>
              <textarea 
                id="notes" 
                rows="3" 
                placeholder="Any additional notes about this assignment..."
                class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
              ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
              <button 
                id="submitAssignment" 
                class="flex-1 bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
              >
                Assign Installment
              </button>
              <button 
                onclick="history.back()" 
                class="px-6 py-3 border border-border-light rounded-lg font-semibold text-text-light hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
            </div>
          </div>
        </main>
      </div>
      <!-- mobile backdrop -->
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <script src="js/app.js?v=15" defer></script>
    <script>
      let selectedCustomer = null;
      let selectedProducts = []; // Changed to array for multiple products
      let selectedPeriod = null;
      
      // Customer search
      const customerSearch = document.getElementById('customerSearch');
      const customerResults = document.getElementById('customerResults');
      const selectedCustomerDiv = document.getElementById('selectedCustomer');
      
      customerSearch.addEventListener('input', debounce(async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
          customerResults.classList.add('hidden');
          return;
        }
        
        try {
          const response = await fetch(`api/get-customers.php?search=${encodeURIComponent(query)}`);
          const data = await response.json();
          
          if (data.success && data.customers.length > 0) {
            displayCustomerResults(data.customers);
          } else {
            customerResults.innerHTML = '<div class="p-4 text-center text-text-light">No customers found</div>';
            customerResults.classList.remove('hidden');
          }
        } catch (error) {
          console.error('Error searching customers:', error);
        }
      }, 300));
      
      function displayCustomerResults(customers) {
        customerResults.innerHTML = customers.map(customer => `
          <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-border-light last:border-b-0" 
               onclick="selectCustomer(${customer.id}, '${customer.customer_code}', '${customer.full_name}', '${customer.mobile}', '${customer.email}')">
            <p class="font-semibold text-heading-light">${customer.full_name}</p>
            <p class="text-sm text-text-light">${customer.customer_code} | ${customer.mobile}</p>
          </div>
        `).join('');
        customerResults.classList.remove('hidden');
      }
      
      async function selectCustomer(id, code, name, mobile, email) {
        selectedCustomer = { id, code, name, mobile, email };
        customerSearch.value = '';
        customerResults.classList.add('hidden');
        
        document.getElementById('selectedCustomerName').textContent = name;
        document.getElementById('selectedCustomerDetails').textContent = `${code} | ${mobile} | ${email}`;
        selectedCustomerDiv.classList.remove('hidden');
        
        // Check eligibility
        try {
          const response = await fetch(`api/check-customer-eligibility.php?customer_id=${id}`);
          const data = await response.json();
          
          const eligibilityDiv = document.getElementById('eligibilityStatus');
          if (data.success) {
            if (data.eligible) {
              eligibilityDiv.innerHTML = '<span class="text-sm text-green-600 font-medium">✓ Eligible for new installment</span>';
              updateStepIndicators();
              updateSubmitButton();
            } else {
              eligibilityDiv.innerHTML = `
                <div class="text-sm text-red-600 font-medium">
                  ⚠ ${data.message}
                </div>
                ${data.active_orders.length > 0 ? `
                  <div class="mt-2 text-xs text-text-light">
                    Active Order: ${data.active_orders[0].order_number} - Balance: Rs. ${parseFloat(data.active_orders[0].remaining_balance).toFixed(2)}
                  </div>
                ` : ''}
              `;
              selectedCustomer = null; // Prevent submission
              updateStepIndicators();
              updateSubmitButton();
            }
          }
        } catch (error) {
          console.error('Error checking eligibility:', error);
        }
      }
      
      document.getElementById('clearCustomer').addEventListener('click', () => {
        selectedCustomer = null;
        selectedCustomerDiv.classList.add('hidden');
        customerSearch.value = '';
        updateStepIndicators();
        updateSubmitButton();
      });
      
      // Product search
      const productSearch = document.getElementById('productSearch');
      const productResults = document.getElementById('productResults');
      const selectedProductDiv = document.getElementById('selectedProduct');
      
      productSearch.addEventListener('input', debounce(async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
          productResults.classList.add('hidden');
          return;
        }
        
        try {
          const response = await fetch(`api/get-products.php?search=${encodeURIComponent(query)}&status=active`);
          const data = await response.json();
          
          if (data.success && data.products.length > 0) {
            displayProductResults(data.products);
          } else {
            productResults.innerHTML = '<div class="p-4 text-center text-text-light">No products found</div>';
            productResults.classList.remove('hidden');
          }
        } catch (error) {
          console.error('Error searching products:', error);
        }
      }, 300));
      
      function displayProductResults(products) {
        productResults.innerHTML = products.map(product => {
          const isAlreadySelected = selectedProducts.some(p => p.id === product.id);
          // Escape strings for use in HTML attributes
          const escapedName = String(product.name || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
          const escapedSku = String(product.sku || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
          const productId = parseInt(product.id) || 0;
          const productPrice = parseFloat(product.price_selling) || 0;
          
          return `
            <div class="p-3 border-b border-border-light last:border-b-0 ${isAlreadySelected ? 'opacity-50 bg-gray-100' : 'hover:bg-gray-50'}">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="font-semibold text-heading-light">${product.name} ${isAlreadySelected ? '<span class="text-xs text-green-600 font-normal">(Already in list)</span>' : ''}</p>
                  <p class="text-sm text-text-light">${product.sku} | Rs. ${productPrice.toFixed(2)}</p>
                </div>
                ${isAlreadySelected 
                  ? '<span class="text-green-600 text-sm font-medium px-3">Added</span>' 
                  : `<button 
                             data-product-id="${productId}" 
                             data-product-sku="${escapedSku}" 
                             data-product-name="${escapedName}" 
                             data-product-price="${productPrice}"
                             class="add-product-btn ml-3 px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-1">
                      <span class="material-icons text-sm">add</span>
                      Add to List
                    </button>`
                }
              </div>
            </div>
          `;
        }).join('');
        
        // Add event listeners to all "Add to List" buttons using event delegation
        productResults.querySelectorAll('.add-product-btn').forEach(btn => {
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = parseInt(this.getAttribute('data-product-id'));
            const productSku = this.getAttribute('data-product-sku');
            const productName = this.getAttribute('data-product-name');
            const productPrice = parseFloat(this.getAttribute('data-product-price'));
            
            selectProductInternal(productId, productSku, productName, productPrice);
          });
        });
        
        productResults.classList.remove('hidden');
      }
      
      // Define selectProduct function
      function selectProductInternal(id, sku, name, price) {
        try {
          // Validate inputs
          if (!id || !sku || !name) {
            console.error('Invalid product data:', { id, sku, name, price });
            alert('Error: Invalid product data. Please try again.');
            return;
          }
          
          // Check if product is already selected
          if (selectedProducts.some(p => p.id === id)) {
            if (typeof showNotificationDialog === 'function') {
              showNotificationDialog({
                title: 'Already Selected',
                message: 'This product is already in your selection list',
                type: 'info'
              });
            } else {
              alert('This product is already selected');
            }
            return;
          }
          
          // Add product to selected list
          const productPrice = parseFloat(price) || 0;
          if (productPrice <= 0) {
            console.error('Invalid product price:', price);
            alert('Error: Invalid product price. Please try again.');
            return;
          }
          
          selectedProducts.push({ 
            id: parseInt(id), 
            sku: String(sku), 
            name: String(name), 
            price: productPrice,
            quantity: 1
          });
          
          productSearch.value = '';
          productResults.classList.add('hidden');
          
          renderSelectedProducts();
          updateProductSummary();
          updateInstallmentSummary();
          updateStepIndicators();
          updateSubmitButton();
          
          console.log('Product added successfully:', { id, sku, name, price: productPrice });
        } catch (error) {
          console.error('Error in selectProduct:', error);
          alert('Error adding product. Please try again.');
        }
      }
      
      // Expose functions globally for onclick handlers
      window.removeProduct = function(productId) {
        selectedProducts = selectedProducts.filter(p => p.id !== productId);
        renderSelectedProducts();
        updateProductSummary();
        updateInstallmentSummary();
        updateStepIndicators();
        updateSubmitButton();
      };
      
      window.updateProductQuantity = function(productId, quantity) {
        const product = selectedProducts.find(p => p.id === productId);
        if (product) {
          product.quantity = Math.max(1, parseInt(quantity) || 1);
          renderSelectedProducts();
          updateProductSummary();
          updateInstallmentSummary();
        }
      };
      
      function renderSelectedProducts() {
        const container = document.getElementById('selectedProductsContainer');
        const list = document.getElementById('selectedProductsList');
        
        if (selectedProducts.length === 0) {
          container.classList.add('hidden');
          return;
        }
        
        container.classList.remove('hidden');
        
        list.innerHTML = selectedProducts.map((product, index) => `
          <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-xs text-text-light font-medium bg-blue-100 px-2 py-1 rounded">#${index + 1}</span>
                  <p class="font-semibold text-heading-light">${product.name}</p>
                </div>
                <p class="text-sm text-text-light">SKU: ${product.sku}</p>
                <p class="text-lg font-bold text-primary mt-2">Rs. ${product.price.toFixed(2)} each</p>
              </div>
              <button onclick="removeProduct(${product.id})" class="text-red-600 hover:text-red-700 ml-3" title="Remove product">
                <span class="material-icons">close</span>
              </button>
            </div>
            <div class="mt-3 flex items-center gap-3">
              <label class="text-sm font-medium text-heading-light">Quantity:</label>
              <input 
                type="number" 
                min="1" 
                value="${product.quantity}"
                onchange="updateProductQuantity(${product.id}, this.value)"
                class="w-24 px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary"
              >
              <span class="text-sm text-text-light">= Rs. <strong class="text-heading-light">${(product.price * product.quantity).toFixed(2)}</strong></span>
            </div>
          </div>
        `).join('');
      }
      
      document.getElementById('clearAllProducts').addEventListener('click', () => {
        selectedProducts = [];
        renderSelectedProducts();
        updateProductSummary();
        updateInstallmentSummary();
        updateStepIndicators();
        updateSubmitButton();
      });
      
      // Installment period selection
      document.querySelectorAll('.installment-period-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          document.querySelectorAll('.installment-period-btn').forEach(b => {
            b.classList.remove('border-primary', 'bg-primary/5');
            b.classList.add('border-border-light');
          });
          btn.classList.add('border-primary', 'bg-primary/5');
          btn.classList.remove('border-border-light');
          selectedPeriod = parseInt(btn.dataset.period);
          updateInstallmentSummary();
          updateStepIndicators();
          updateSubmitButton();
        });
      });
      
      // Update product summary (shows immediately when products are added)
      function updateProductSummary() {
        const productSummarySection = document.getElementById('productSummarySection');
        
        if (selectedProducts.length === 0) {
          productSummarySection.classList.add('hidden');
          return;
        }
        
        // Calculate totals for all products
        let totalAmount = 0;
        let totalQuantity = 0;
        
        selectedProducts.forEach(product => {
          const subtotal = product.price * product.quantity;
          totalAmount += subtotal;
          totalQuantity += product.quantity;
        });
        
        // Update product summary display
        document.getElementById('productTotalAmount').textContent = `Rs. ${totalAmount.toFixed(2)}`;
        document.getElementById('productTotalQuantity').textContent = totalQuantity;
        
        // Show product breakdown
        const productBreakdown = document.getElementById('productSummaryBreakdown');
        if (productBreakdown) {
          productBreakdown.innerHTML = selectedProducts.map(p => 
            `<div>${p.name} (${p.quantity}x): Rs. ${(p.price * p.quantity).toFixed(2)}</div>`
          ).join('');
        }
        
        productSummarySection.classList.remove('hidden');
      }
      
      // Update installment calculation summary (shows after period selection)
      function updateInstallmentSummary() {
        const installmentSummarySection = document.getElementById('installmentSummarySection');
        
        if (selectedProducts.length === 0 || !selectedPeriod) {
          installmentSummarySection.classList.add('hidden');
          return;
        }
        
        // Calculate total amount
        let totalAmount = 0;
        selectedProducts.forEach(product => {
          totalAmount += product.price * product.quantity;
        });
        
        const daily = totalAmount / selectedPeriod;
        
        // Update installment summary display
        document.getElementById('summaryPeriod').textContent = `${selectedPeriod} Days`;
        document.getElementById('summaryDaily').textContent = `Rs. ${daily.toFixed(2)}`;
        
        installmentSummarySection.classList.remove('hidden');
      }
      
      // Update step indicators based on completion status
      function updateStepIndicators() {
        // Step 1: Customer selected and eligible
        const step1Indicator = document.getElementById('step1Indicator');
        const step1Circle = step1Indicator.querySelector('span:first-child');
        const step1Text = step1Indicator.querySelector('span:last-child');
        
        if (selectedCustomer && !document.getElementById('selectedCustomer').classList.contains('hidden')) {
          step1Circle.classList.remove('bg-gray-300');
          step1Circle.classList.add('bg-primary');
          step1Circle.innerHTML = '<span class="material-icons" style="font-size: 16px;">check</span>';
          step1Text.classList.remove('text-text-light');
          step1Text.classList.add('text-primary', 'font-semibold');
        } else {
          step1Circle.classList.remove('bg-primary');
          step1Circle.classList.add('bg-gray-300');
          step1Circle.textContent = '1';
          step1Text.classList.remove('text-primary', 'font-semibold');
          step1Text.classList.add('text-text-light');
        }
        
        // Step 2: Products added
        const step2Indicator = document.getElementById('step2Indicator');
        const step2Circle = step2Indicator.querySelector('span:first-child');
        const step2Text = step2Indicator.querySelector('span:last-child');
        
        if (selectedProducts.length > 0) {
          step2Circle.classList.remove('bg-gray-300');
          step2Circle.classList.add('bg-primary');
          step2Circle.innerHTML = '<span class="material-icons" style="font-size: 16px;">check</span>';
          step2Text.classList.remove('text-text-light');
          step2Text.classList.add('text-primary', 'font-semibold');
        } else {
          step2Circle.classList.remove('bg-primary');
          step2Circle.classList.add('bg-gray-300');
          step2Circle.textContent = '2';
          step2Text.classList.remove('text-primary', 'font-semibold');
          step2Text.classList.add('text-text-light');
        }
        
        // Step 3: Period selected
        const step3Indicator = document.getElementById('step3Indicator');
        const step3Circle = step3Indicator.querySelector('span:first-child');
        const step3Text = step3Indicator.querySelector('span:last-child');
        
        if (selectedPeriod) {
          step3Circle.classList.remove('bg-gray-300');
          step3Circle.classList.add('bg-primary');
          step3Circle.innerHTML = '<span class="material-icons" style="font-size: 16px;">check</span>';
          step3Text.classList.remove('text-text-light');
          step3Text.classList.add('text-primary', 'font-semibold');
        } else {
          step3Circle.classList.remove('bg-primary');
          step3Circle.classList.add('bg-gray-300');
          step3Circle.textContent = '3';
          step3Text.classList.remove('text-primary', 'font-semibold');
          step3Text.classList.add('text-text-light');
        }
        
        // Step 4: Ready to submit (all steps complete)
        const step4Indicator = document.getElementById('step4Indicator');
        const step4Circle = step4Indicator.querySelector('span:first-child');
        const step4Text = step4Indicator.querySelector('span:last-child');
        
        if (selectedCustomer && selectedProducts.length > 0 && selectedPeriod) {
          step4Circle.classList.remove('bg-gray-300');
          step4Circle.classList.add('bg-primary');
          step4Circle.innerHTML = '<span class="material-icons" style="font-size: 16px;">check</span>';
          step4Text.classList.remove('text-text-light');
          step4Text.classList.add('text-primary', 'font-semibold');
        } else {
          step4Circle.classList.remove('bg-primary');
          step4Circle.classList.add('bg-gray-300');
          step4Circle.textContent = '4';
          step4Text.classList.remove('text-primary', 'font-semibold');
          step4Text.classList.add('text-text-light');
        }
      }
      
      function updateSubmitButton() {
        const submitBtn = document.getElementById('submitAssignment');
        if (selectedCustomer && selectedProducts.length > 0 && selectedPeriod && selectedCustomer) {
          // Double check customer is not null (eligibility passed)
          submitBtn.disabled = false;
        } else {
          submitBtn.disabled = true;
        }
      }
      
      // Submit assignment
      document.getElementById('submitAssignment').addEventListener('click', async () => {
        if (!selectedCustomer || selectedProducts.length === 0 || !selectedPeriod) {
          alert('Please select at least one product and fill all required fields');
          return;
        }
        
        const assignmentDate = document.getElementById('assignmentDate').value;
        const notes = document.getElementById('notes').value;
        
        const submitBtn = document.getElementById('submitAssignment');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Assigning...';
        
        try {
          // Prepare products array
          const products = selectedProducts.map(p => ({
            product_id: p.id,
            quantity: p.quantity
          }));
          
          const response = await fetch('api/assign-installment.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              customer_id: selectedCustomer.id,
              products: products,
              installment_period: selectedPeriod,
              assignment_date: assignmentDate,
              notes: notes || null
            })
          });
          
          const data = await response.json();
          
          if (data.success) {
            if (typeof showNotificationDialog === 'function') {
              showNotificationDialog({
                title: 'Success',
                message: 'Installment assigned successfully!',
                type: 'success'
              });
            } else {
              alert('Installment assigned successfully!');
            }
            
            // Redirect to customer detail page
            setTimeout(() => {
              window.location.href = `customer-detail.php?id=${selectedCustomer.id}`;
            }, 1500);
          } else {
            alert('Error: ' + (data.error || 'Failed to assign installment'));
            submitBtn.disabled = false;
            submitBtn.textContent = 'Assign Installment';
          }
        } catch (error) {
          console.error('Error assigning installment:', error);
          alert('Error: Failed to assign installment. Please try again.');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Assign Installment';
        }
      });
      
      // Utility function
      function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
          const later = () => {
            clearTimeout(timeout);
            func(...args);
          };
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
        };
      }
      
      // Sidebar toggle
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');
      
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
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
      }
      
      sidebarBackdrop.addEventListener('click', () => {
        mobileSidebar.classList.add('-translate-x-full');
        sidebarBackdrop.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
      });
      
      // Initialize step indicators on page load
      updateStepIndicators();
    </script>
  </body>
</html>

