<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Collections - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Collection Management - Daily Collection Manager">
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
      <?php $activePage = 'collection'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Collections</h2>
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
          <!-- Tab Navigation -->
          <div class="bg-card-light rounded-lg border border-border-light mb-6">
            <div class="flex border-b border-border-light">
              <button id="sellTab" class="flex-1 px-6 py-4 text-center font-semibold text-primary bg-primary/10 border-b-2 border-primary">
                <span class="material-icons mr-2">shopping_cart</span>
                Sell Products
              </button>
              <button id="payTab" class="flex-1 px-6 py-4 text-center font-semibold text-text-light hover:text-primary hover:bg-primary/5">
                <span class="material-icons mr-2">payment</span>
                Collect Payment
              </button>
            </div>
          </div>

          <!-- Sell Tab Content -->
          <div id="sellContent" class="tab-content">
            <!-- Customer Search Section -->
            <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Find Customer</h3>
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                  <label for="customerSearch" class="block text-sm font-medium text-text-light mb-2">Search Customer (ID/Mobile/Name)</label>
                  <div class="relative">
                    <input 
                      type="text" 
                      id="customerSearch" 
                      placeholder="Enter customer ID, mobile or name..." 
                      class="w-full px-4 py-3 pl-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                    >
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">
                      <span class="material-icons text-lg">search</span>
                    </span>
                  </div>
                  <div id="customerResults" class="mt-2 hidden">
                    <!-- Customer search results will appear here -->
                  </div>
                </div>
                <div class="flex items-end">
                  <button 
                    id="searchCustomerBtn" 
                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                  >
                    <span class="material-icons text-lg">search</span>
                    Search
                  </button>
                </div>
              </div>
            </div>

            <!-- Selected Customer Info -->
            <div id="selectedCustomerInfo" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Selected Customer</h3>
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold text-heading-light" id="selectedCustomerName">Customer Name</p>
                  <p class="text-text-light">ID: <span id="selectedCustomerId"></span></p>
                  <p class="text-text-light">Mobile: <span id="selectedCustomerMobile"></span></p>
                </div>
                <button id="changeCustomerBtn" class="text-primary hover:text-primary/80 text-sm">Change Customer</button>
              </div>
            </div>

            <!-- Product Selection -->
            <div id="productSelection" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-heading-light">Add Products</h3>
                <button id="addProductBtn" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                  <span class="material-icons text-lg">add</span>
                  Add Product
                </button>
              </div>
              
              <!-- Product Search -->
              <div id="productSearchSection" class="mb-4 hidden">
                <div class="flex flex-col sm:flex-row gap-4">
                  <div class="flex-1">
                    <input 
                      type="text" 
                      id="productSearch" 
                      placeholder="Search products..." 
                      class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                    >
                  </div>
                  <div class="flex items-end">
                    <button id="searchProductBtn" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors">
                      Search
                    </button>
                  </div>
                </div>
                <div id="productResults" class="mt-4">
                  <!-- Product search results will appear here -->
                </div>
              </div>

              <!-- Selected Products -->
              <div id="selectedProducts">
                <!-- Selected products will be added here dynamically -->
              </div>
            </div>

            <!-- Order Summary -->
            <div id="orderSummary" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Order Summary</h3>
              <div class="space-y-2">
                <div class="flex justify-between">
                  <span class="text-text-light">Subtotal:</span>
                  <span class="font-semibold" id="subtotal">Rs. 0.00</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-text-light">Total Amount:</span>
                  <span class="text-xl font-bold text-primary" id="totalAmount">Rs. 0.00</span>
                </div>
              </div>
              
              <!-- Installment Period Selection -->
              <div class="mt-6">
                <label class="block text-sm font-medium text-text-light mb-2">Installment Period</label>
                <div class="flex gap-4">
                  <label class="flex items-center">
                    <input type="radio" name="installmentPeriod" value="30" class="mr-2" checked>
                    <span class="text-text-light">30 Days</span>
                  </label>
                  <label class="flex items-center">
                    <input type="radio" name="installmentPeriod" value="60" class="mr-2">
                    <span class="text-text-light">60 Days</span>
                  </label>
                </div>
                <div class="mt-2">
                  <span class="text-sm text-text-light">Daily Payment: </span>
                  <span class="font-semibold text-primary" id="dailyPayment">Rs. 0.00</span>
                </div>
              </div>
              
              <button id="submitOrderBtn" class="w-full mt-6 bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                Submit Order
              </button>
            </div>
          </div>

          <!-- Pay Tab Content -->
          <div id="payContent" class="tab-content hidden">
            <!-- Customer Search for Payment -->
            <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Find Customer for Payment</h3>
              <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                  <label for="paymentCustomerSearch" class="block text-sm font-medium text-text-light mb-2">Search Customer (ID/Mobile/Name)</label>
                  <div class="relative">
                    <input 
                      type="text" 
                      id="paymentCustomerSearch" 
                      placeholder="Enter customer ID, mobile or name..." 
                      class="w-full px-4 py-3 pl-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                    >
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">
                      <span class="material-icons text-lg">search</span>
                    </span>
                  </div>
                  <div id="paymentCustomerResults" class="mt-2 hidden">
                    <!-- Customer search results will appear here -->
                  </div>
                </div>
                <div class="flex items-end">
                  <button 
                    id="searchPaymentCustomerBtn" 
                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                  >
                    <span class="material-icons text-lg">search</span>
                    Search
                  </button>
                </div>
              </div>
            </div>

            <!-- Payment Details -->
            <div id="paymentDetails" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Payment Details</h3>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="order-2 lg:order-1">
                  <h4 class="font-semibold text-heading-light mb-2">Customer Information</h4>
                  <p class="text-text-light">Name: <span id="paymentCustomerName"></span></p>
                  <p class="text-text-light">ID: <span id="paymentCustomerId"></span></p>
                  <p class="text-text-light">Mobile: <span id="paymentCustomerMobile"></span></p>
                </div>
                <div class="order-1 lg:order-2">
                  <h4 class="font-semibold text-heading-light mb-2">Payment Information</h4>
                  <div class="mb-4">
                    <p class="text-text-light mb-1">Today's Payment Due:</p>
                    <p class="text-2xl sm:text-3xl font-bold text-primary" id="todayPayment">Rs. 0.00</p>
                  </div>
                  <p class="text-text-light">Remaining Balance: <span class="font-semibold text-red-600" id="remainingBalance">Rs. 0.00</span></p>
                  <p class="text-text-light">Days Remaining: <span class="font-semibold" id="daysRemaining">0</span></p>
                </div>
              </div>
              
              <!-- Payment Form -->
              <div class="mt-6">
                <label for="paymentAmount" class="block text-sm font-medium text-text-light mb-2">Payment Amount</label>
                <input 
                  type="number" 
                  id="paymentAmount" 
                  placeholder="Enter amount to collect" 
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none mb-4"
                >
                <button id="collectPaymentBtn" class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                  Collect Payment
                </button>
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
    <script>
      // Mock data for customers and products
      const customers = [
        { id: 'C001', name: 'Robert Anderson', email: 'robert.a@email.com', mobile: '+1 555-0101', balance: 250.00 },
        { id: 'C002', name: 'Emily Martinez', email: 'emily.m@email.com', mobile: '+1 555-0102', balance: 0.00 },
        { id: 'C003', name: 'David Thompson', email: 'david.t@email.com', mobile: '+1 555-0103', balance: 125.50 },
        { id: 'C004', name: 'Sarah Wilson', email: 'sarah.wilson@email.com', mobile: '+1 555-0104', balance: 0.00 },
        { id: 'C005', name: 'Michael Brown', email: 'michael.b@email.com', mobile: '+1 555-0105', balance: 575.25 },
        { id: 'C006', name: 'Jessica Davis', email: 'jessica.d@email.com', mobile: '+1 555-0106', balance: 89.99 }
      ];

      const products = [
        { id: 'P001', name: 'Wireless Headphones', price: 1250.00, image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100&h=100&fit=crop' },
        { id: 'P002', name: 'Smart Watch', price: 2500.00, image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop' },
        { id: 'P003', name: 'Bluetooth Speaker', price: 800.00, image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop' },
        { id: 'P004', name: 'Gaming Mouse', price: 450.00, image: 'https://images.unsplash.com/photo-1527864550417-7f27c4a0b0b0?w=100&h=100&fit=crop' },
        { id: 'P005', name: 'Mechanical Keyboard', price: 1200.00, image: 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=100&h=100&fit=crop' },
        { id: 'P006', name: 'USB-C Hub', price: 350.00, image: 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=100&h=100&fit=crop' }
      ];

      let selectedCustomer = null;
      let selectedProducts = [];
      let selectedPaymentCustomer = null;

      // Tab switching functionality
      document.getElementById('sellTab').addEventListener('click', function() {
        switchTab('sell');
      });

      document.getElementById('payTab').addEventListener('click', function() {
        switchTab('pay');
      });

      function switchTab(tab) {
        // Hide all tab contents
        document.getElementById('sellContent').classList.add('hidden');
        document.getElementById('payContent').classList.add('hidden');
        
        // Remove active styling from all tabs
        document.getElementById('sellTab').classList.remove('text-primary', 'bg-primary/10', 'border-primary');
        document.getElementById('sellTab').classList.add('text-text-light', 'hover:text-primary', 'hover:bg-primary/5');
        document.getElementById('payTab').classList.remove('text-primary', 'bg-primary/10', 'border-primary');
        document.getElementById('payTab').classList.add('text-text-light', 'hover:text-primary', 'hover:bg-primary/5');

        // Show selected tab content and add active styling
        if (tab === 'sell') {
          document.getElementById('sellContent').classList.remove('hidden');
          document.getElementById('sellTab').classList.add('text-primary', 'bg-primary/10', 'border-b-2', 'border-primary');
          document.getElementById('sellTab').classList.remove('text-text-light', 'hover:text-primary', 'hover:bg-primary/5');
        } else {
          document.getElementById('payContent').classList.remove('hidden');
          document.getElementById('payTab').classList.add('text-primary', 'bg-primary/10', 'border-b-2', 'border-primary');
          document.getElementById('payTab').classList.remove('text-text-light', 'hover:text-primary', 'hover:bg-primary/5');
        }
      }

      // Customer search functionality for Sell tab
      document.getElementById('searchCustomerBtn').addEventListener('click', function() {
        const searchTerm = document.getElementById('customerSearch').value.toLowerCase();
        const results = customers.filter(customer => 
          customer.id.toLowerCase().includes(searchTerm) ||
          customer.name.toLowerCase().includes(searchTerm) ||
          customer.mobile.includes(searchTerm)
        );
        displayCustomerResults(results, 'customerResults');
      });

      // Customer search functionality for Pay tab
      document.getElementById('searchPaymentCustomerBtn').addEventListener('click', function() {
        const searchTerm = document.getElementById('paymentCustomerSearch').value.toLowerCase();
        const results = customers.filter(customer => 
          customer.id.toLowerCase().includes(searchTerm) ||
          customer.name.toLowerCase().includes(searchTerm) ||
          customer.mobile.includes(searchTerm)
        );
        displayCustomerResults(results, 'paymentCustomerResults', true);
      });

      function displayCustomerResults(results, containerId, isPayment = false) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        
        if (results.length === 0) {
          container.innerHTML = '<p class="text-text-light">No customers found</p>';
        } else {
          results.forEach(customer => {
            const div = document.createElement('div');
            div.className = 'p-3 border border-border-light rounded-lg hover:bg-gray-50 cursor-pointer mb-2';
            div.innerHTML = `
              <div class="flex justify-between items-center">
                <div>
                  <p class="font-semibold text-heading-light">${customer.name}</p>
                  <p class="text-sm text-text-light">ID: ${customer.id} | Mobile: ${customer.mobile}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm text-text-light">Balance: <span class="font-semibold ${customer.balance > 0 ? 'text-red-600' : 'text-green-600'}">Rs. ${customer.balance.toFixed(2)}</span></p>
                </div>
              </div>
            `;
            div.addEventListener('click', function() {
              if (isPayment) {
                selectPaymentCustomer(customer);
              } else {
                selectCustomer(customer);
              }
            });
            container.appendChild(div);
          });
        }
        container.classList.remove('hidden');
      }

      function selectCustomer(customer) {
        selectedCustomer = customer;
        document.getElementById('selectedCustomerName').textContent = customer.name;
        document.getElementById('selectedCustomerId').textContent = customer.id;
        document.getElementById('selectedCustomerMobile').textContent = customer.mobile;
        document.getElementById('selectedCustomerInfo').classList.remove('hidden');
        document.getElementById('productSelection').classList.remove('hidden');
        document.getElementById('customerResults').classList.add('hidden');
      }

      function selectPaymentCustomer(customer) {
        selectedPaymentCustomer = customer;
        document.getElementById('paymentCustomerName').textContent = customer.name;
        document.getElementById('paymentCustomerId').textContent = customer.id;
        document.getElementById('paymentCustomerMobile').textContent = customer.mobile;
        document.getElementById('paymentDetails').classList.remove('hidden');
        document.getElementById('paymentCustomerResults').classList.add('hidden');
        
        // Calculate payment details
        const dailyPayment = customer.balance / 30; // Assuming 30-day period
        document.getElementById('todayPayment').textContent = `Rs. ${dailyPayment.toFixed(2)}`;
        document.getElementById('remainingBalance').textContent = `Rs. ${customer.balance.toFixed(2)}`;
        document.getElementById('daysRemaining').textContent = Math.ceil(customer.balance / dailyPayment);
        document.getElementById('paymentAmount').value = dailyPayment.toFixed(2);
      }

      // Product search and selection
      document.getElementById('addProductBtn').addEventListener('click', function() {
        document.getElementById('productSearchSection').classList.remove('hidden');
      });

      document.getElementById('searchProductBtn').addEventListener('click', function() {
        const searchTerm = document.getElementById('productSearch').value.toLowerCase();
        const results = products.filter(product => 
          product.name.toLowerCase().includes(searchTerm) ||
          product.id.toLowerCase().includes(searchTerm)
        );
        displayProductResults(results);
      });

      function displayProductResults(results) {
        const container = document.getElementById('productResults');
        container.innerHTML = '';
        
        if (results.length === 0) {
          container.innerHTML = '<p class="text-text-light">No products found</p>';
        } else {
          results.forEach(product => {
            const div = document.createElement('div');
            div.className = 'p-4 border border-border-light rounded-lg hover:bg-gray-50 cursor-pointer mb-2';
            div.innerHTML = `
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <p class="font-semibold text-heading-light">${product.name}</p>
                    <p class="text-sm text-text-light">ID: ${product.id}</p>
                    <p class="text-sm text-text-light">Price: Rs. ${product.price.toFixed(2)}</p>
                  </div>
                </div>
                <button class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors" onclick="addProductToOrder('${product.id}')">
                  Add to Order
                </button>
              </div>
            `;
            container.appendChild(div);
          });
        }
      }

      function addProductToOrder(productId) {
        const product = products.find(p => p.id === productId);
        if (product && !selectedProducts.find(p => p.id === productId)) {
          selectedProducts.push({...product, quantity: 1});
          updateSelectedProducts();
          updateOrderSummary();
        }
      }

      function updateSelectedProducts() {
        const container = document.getElementById('selectedProducts');
        container.innerHTML = '';
        
        selectedProducts.forEach((product, index) => {
          const div = document.createElement('div');
          div.className = 'p-4 border border-border-light rounded-lg mb-3';
          div.innerHTML = `
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100">
                  <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">
                </div>
                <div>
                  <p class="font-semibold text-heading-light">${product.name}</p>
                  <p class="text-sm text-text-light">ID: ${product.id}</p>
                  <p class="text-sm text-text-light">Price: Rs. ${product.price.toFixed(2)}</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                  <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">-</button>
                  <span class="w-8 text-center">${product.quantity}</span>
                  <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">+</button>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-heading-light">Rs. ${(product.price * product.quantity).toFixed(2)}</p>
                </div>
                <button onclick="removeProduct(${index})" class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50">
                  <span class="material-icons text-lg">delete</span>
                </button>
              </div>
            </div>
          `;
          container.appendChild(div);
        });
        
        if (selectedProducts.length > 0) {
          document.getElementById('orderSummary').classList.remove('hidden');
        }
      }

      function updateQuantity(index, change) {
        selectedProducts[index].quantity += change;
        if (selectedProducts[index].quantity <= 0) {
          selectedProducts.splice(index, 1);
        }
        updateSelectedProducts();
        updateOrderSummary();
      }

      function removeProduct(index) {
        selectedProducts.splice(index, 1);
        updateSelectedProducts();
        updateOrderSummary();
      }

      function updateOrderSummary() {
        const subtotal = selectedProducts.reduce((sum, product) => sum + (product.price * product.quantity), 0);
        document.getElementById('subtotal').textContent = `Rs. ${subtotal.toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `Rs. ${subtotal.toFixed(2)}`;
        
        // Update daily payment based on installment period
        updateDailyPayment();
      }

      function updateDailyPayment() {
        const subtotal = selectedProducts.reduce((sum, product) => sum + (product.price * product.quantity), 0);
        const installmentPeriod = document.querySelector('input[name="installmentPeriod"]:checked').value;
        const dailyPayment = subtotal / parseInt(installmentPeriod);
        document.getElementById('dailyPayment').textContent = `Rs. ${dailyPayment.toFixed(2)}`;
      }

      // Installment period change handler
      document.querySelectorAll('input[name="installmentPeriod"]').forEach(radio => {
        radio.addEventListener('change', updateDailyPayment);
      });

      // Submit order
      document.getElementById('submitOrderBtn').addEventListener('click', function() {
        if (selectedCustomer && selectedProducts.length > 0) {
          const subtotal = selectedProducts.reduce((sum, product) => sum + (product.price * product.quantity), 0);
          const installmentPeriod = document.querySelector('input[name="installmentPeriod"]:checked').value;
          const dailyPayment = subtotal / parseInt(installmentPeriod);
          
          alert(`Order submitted successfully!\nCustomer: ${selectedCustomer.name}\nTotal Amount: Rs. ${subtotal.toFixed(2)}\nInstallment Period: ${installmentPeriod} days\nDaily Payment: Rs. ${dailyPayment.toFixed(2)}`);
          
          // Reset form
          selectedCustomer = null;
          selectedProducts = [];
          document.getElementById('selectedCustomerInfo').classList.add('hidden');
          document.getElementById('productSelection').classList.add('hidden');
          document.getElementById('orderSummary').classList.add('hidden');
          document.getElementById('productSearchSection').classList.add('hidden');
          document.getElementById('customerSearch').value = '';
          document.getElementById('productSearch').value = '';
        }
      });

      // Collect payment
      document.getElementById('collectPaymentBtn').addEventListener('click', function() {
        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
        if (selectedPaymentCustomer && paymentAmount > 0) {
          const newBalance = selectedPaymentCustomer.balance - paymentAmount;
          alert(`Payment collected successfully!\nAmount: Rs. ${paymentAmount.toFixed(2)}\nRemaining Balance: Rs. ${newBalance.toFixed(2)}`);
          
          // Update customer balance
          selectedPaymentCustomer.balance = newBalance;
          if (newBalance <= 0) {
            alert('Customer has fully paid their balance!');
          }
        }
      });

      // Change customer button
      document.getElementById('changeCustomerBtn').addEventListener('click', function() {
        selectedCustomer = null;
        selectedProducts = [];
        document.getElementById('selectedCustomerInfo').classList.add('hidden');
        document.getElementById('productSelection').classList.add('hidden');
        document.getElementById('orderSummary').classList.add('hidden');
        document.getElementById('productSearchSection').classList.add('hidden');
        document.getElementById('customerSearch').value = '';
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
