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
    <title>Customer Detail - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Customer Detail - Daily Collection Manager">
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
      <?php $activePage = 'customer-detail'; include __DIR__ . '/partials/menu.php'; ?>
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
          <div class="flex items-center gap-3">
            <h2 class="text-2xl font-bold text-heading-light">Customer Detail</h2>
            <button id="paymentHistoryBtn" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" title="View Payment History">
              <span class="material-icons text-sm">info</span>
              <span>Payment History</span>
            </button>
          </div>
          <div class="flex items-center gap-4">
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
          <!-- Loading State -->
          <div id="loadingState" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
            <p class="mt-4 text-text-light">Loading customer details...</p>
          </div>

          <!-- Customer Info -->
          <div id="customerContent" class="hidden">
            <!-- Header Section with Photo -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
              <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Customer Photo -->
                <div class="flex-shrink-0">
                  <div class="w-32 h-32 rounded-lg overflow-hidden bg-gray-100 border-2 border-border-light">
                    <img id="customerPhoto" src="" alt="Customer Photo" class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27100%27 height=%27100%27%3E%3Crect fill=%27%23e5e7eb%27 width=%27100%27 height=%27100%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27%239ca3af%27 font-family=%27system-ui%27 font-size=%2714%27%3ENo Photo%3C/text%3E%3C/svg%3E'">
                  </div>
                </div>
                <!-- Customer Basic Info -->
                <div class="flex-1">
                  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
              <div>
                      <h3 class="text-2xl font-bold text-heading-light" id="customerName">-</h3>
                      <p class="text-text-light mt-1">Customer ID: <span id="customerCode" class="font-semibold">-</span></p>
                      <p class="text-text-light">Email: <span id="customerEmail" class="font-medium">-</span></p>
                      <p class="text-text-light">Mobile: <span id="customerMobile" class="font-medium">-</span></p>
                      <p class="text-text-light mt-2">NIC: <span id="customerNIC" class="font-medium">-</span></p>
              </div>
                    <div class="text-left md:text-right">
                <p class="text-sm text-text-light">Remaining Balance</p>
                      <p class="text-2xl font-bold text-red-600" id="remainingBalance">Rs. 0.00</p>
                      <span id="customerStatus" class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">Active</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Personal Information Section -->
            <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Personal Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium text-text-light">First Name</label>
                  <p class="text-heading-light font-medium" id="firstName">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Last Name</label>
                  <p class="text-heading-light font-medium" id="lastName">-</p>
                </div>
                <div class="md:col-span-2">
                  <label class="text-sm font-medium text-text-light">Full Name with Surname</label>
                  <p class="text-heading-light font-medium" id="fullNameWithSurname">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Email Address</label>
                  <p class="text-heading-light font-medium" id="email">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Phone Number</label>
                  <p class="text-heading-light font-medium" id="mobile">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">NIC ID Number</label>
                  <p class="text-heading-light font-medium" id="nic">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Occupation</label>
                  <p class="text-heading-light font-medium" id="occupation">-</p>
                </div>
              </div>
            </div>

            <!-- Address Information Section -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Address Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label class="text-sm font-medium text-text-light">Permanent Address</label>
                  <p class="text-heading-light font-medium" id="address">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Grama Niladari Division (GND)</label>
                  <p class="text-heading-light font-medium" id="gnd">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Local Government Institutions (LGI)</label>
                  <p class="text-heading-light font-medium" id="lgi">-</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-text-light">Police Station</label>
                  <p class="text-heading-light font-medium" id="policeStation">-</p>
                </div>
              <div>
                  <label class="text-sm font-medium text-text-light">Period of Residence</label>
                  <p class="text-heading-light font-medium" id="residencePeriod">-</p>
                </div>
              </div>
            </div>

            

            <!-- Documents Section (Hidden from print, but visible in view) -->
            <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6 no-print">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Documents</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIC Front -->
                <div>
                  <label class="text-sm font-medium text-text-light mb-2 block">NIC Front</label>
                  <div id="nicFrontContainer" class="border-2 border-border-light rounded-lg overflow-hidden bg-gray-50 aspect-[3/2] cursor-pointer hover:border-primary transition-colors relative group">
                    <img id="nicFrontImg" src="" alt="NIC Front" class="w-full h-full object-contain" style="display: none;">
                    <div id="nicFrontPlaceholder" class="w-full h-full flex items-center justify-center text-text-light text-sm">
                      <div class="text-center">
                        <span class="material-icons text-6xl text-gray-300 block mb-2">image</span>
                        <p class="text-text-light text-sm">No image</p>
                      </div>
                    </div>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <span class="material-icons text-white text-2xl bg-black/50 rounded-full p-2">zoom_in</span>
                    </div>
                  </div>
                </div>
                <!-- NIC Back -->
                <div>
                  <label class="text-sm font-medium text-text-light mb-2 block">NIC Back</label>
                  <div id="nicBackContainer" class="border-2 border-border-light rounded-lg overflow-hidden bg-gray-50 aspect-[3/2] cursor-pointer hover:border-primary transition-colors relative group">
                    <img id="nicBackImg" src="" alt="NIC Back" class="w-full h-full object-contain" style="display: none;">
                    <div id="nicBackPlaceholder" class="w-full h-full flex items-center justify-center text-text-light text-sm">
                      <div class="text-center">
                        <span class="material-icons text-6xl text-gray-300 block mb-2">image</span>
                        <p class="text-text-light text-sm">No image</p>
                      </div>
                    </div>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                      <span class="material-icons text-white text-2xl bg-black/50 rounded-full p-2">zoom_in</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment History Modal -->
            <div id="paymentHistoryModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
              <div class="relative w-full max-w-5xl max-h-[90vh] bg-white rounded-lg overflow-hidden">
                <button id="closePaymentHistoryModal" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                  <span class="material-icons text-heading-light">close</span>
                </button>
                <div class="p-6 overflow-y-auto" style="max-height: 90vh;">
                  <h3 class="text-2xl font-bold text-heading-light mb-6">Payment History</h3>
                  
                  <!-- Loading State -->
                  <div id="paymentHistoryLoading" class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
                    <p class="mt-4 text-text-light">Loading payment history...</p>
                  </div>
                  
                  <!-- Payment History Content -->
                  <div id="paymentHistoryContent" class="hidden">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                      <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-sm text-text-light">Total Paid</p>
                        <p class="text-2xl font-bold text-blue-600" id="historyTotalPaid">Rs. 0.00</p>
                      </div>
                      <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="text-sm text-text-light">Completed Payments</p>
                        <p class="text-2xl font-bold text-green-600" id="historyCompletedCount">0</p>
                      </div>
                      <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <p class="text-sm text-text-light">Pending Payments</p>
                        <p class="text-2xl font-bold text-yellow-600" id="historyPendingCount">0</p>
                      </div>
                      <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <p class="text-sm text-text-light">Remaining Balance</p>
                        <p class="text-2xl font-bold text-red-600" id="historyRemainingBalance">Rs. 0.00</p>
                      </div>
                    </div>
                    
                    <!-- Installments List -->
                    <div id="paymentHistoryInstallments">
                      <!-- Installments will be loaded here -->
                    </div>
                  </div>
                  
                  <!-- Empty State -->
                  <div id="paymentHistoryEmpty" class="hidden text-center py-12">
                    <span class="material-icons text-6xl text-gray-300 block mb-4">payment</span>
                    <p class="text-text-light text-lg">No payment history available</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Image View Modal -->
            <div id="imageModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
              <div class="relative w-full max-w-6xl max-h-[90vh] bg-white rounded-lg overflow-hidden">
                <button id="closeImageModal" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                  <span class="material-icons text-heading-light">close</span>
                </button>
                <div class="absolute top-4 left-4 z-10 flex gap-2">
                  <button id="zoomIn" class="bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                    <span class="material-icons text-heading-light">zoom_in</span>
                  </button>
                  <button id="zoomOut" class="bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                    <span class="material-icons text-heading-light">zoom_out</span>
                  </button>
                  <button id="resetZoom" class="bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                    <span class="material-icons text-heading-light">fit_screen</span>
                  </button>
                </div>
                <div class="w-full h-full overflow-auto p-8 flex items-center justify-center" style="max-height: 90vh;">
                  <img id="modalImage" src="" alt="Document" class="max-w-full max-h-full object-contain transition-transform duration-200" style="transform-origin: center;">
                </div>
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/90 rounded-full px-4 py-2 shadow-lg">
                  <p id="modalImageTitle" class="text-sm font-medium text-heading-light">NIC Front</p>
              </div>
            </div>

            <!-- Edit Schedule Entry Modal -->
            <div id="editScheduleModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center p-4" style="display: none;">
              <div class="relative w-full max-w-md bg-white rounded-lg overflow-hidden">
                <button id="closeEditScheduleModal" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
                  <span class="material-icons text-heading-light">close</span>
                </button>
                <div class="p-6">
                  <h3 class="text-xl font-bold text-heading-light mb-4">Edit Payment Schedule</h3>
                  <form id="editScheduleForm">
                    <input type="hidden" id="editScheduleId" value="">
                    <div class="mb-4">
                      <label class="block text-sm font-medium text-heading-light mb-2">Schedule Date</label>
                      <input type="date" id="editScheduleDate" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary" readonly>
                    </div>
                    <div class="mb-4">
                      <label class="block text-sm font-medium text-heading-light mb-2">Amount Due *</label>
                      <input type="number" id="editDueAmount" step="0.01" min="0" required
                             class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                      <label class="block text-sm font-medium text-heading-light mb-2">Amount Paid *</label>
                      <input type="number" id="editPaidAmount" step="0.01" min="0" required
                             class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                      <label class="block text-sm font-medium text-heading-light mb-2">Status *</label>
                      <select id="editStatus" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partially Paid</option>
                        <option value="missed">Missed</option>
                      </select>
                    </div>
                    <div class="flex gap-3">
                      <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/90">
                        Update
                      </button>
                      <button type="button" id="cancelEditSchedule" class="px-6 py-3 border border-border-light rounded-lg font-semibold text-text-light hover:bg-gray-50">
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Products Purchased -->
          <div class="bg-card-light rounded-lg border border-border-light mb-6">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4">Products Purchased</h3>
              <div class="overflow-x-auto">
                <table class="w-full" id="productTable">
                  <thead>
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Product</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Price</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Quantity</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Total</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Purchase Date</th>
                    </tr>
                  </thead>
                  <tbody id="productTableBody">
                    <tr>
                      <td colspan="5" class="py-8 text-center text-text-light">
                        <span class="material-icons text-4xl text-gray-300 block mb-2">shopping_cart</span>
                        <p>No products purchased yet</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="mt-4 pt-4 border-t border-border-light">
                <div class="flex justify-between items-center">
                  <span class="text-lg font-semibold text-heading-light">Total Purchased:</span>
                  <span class="text-xl font-bold text-primary" id="totalPurchased">Rs. 0.00</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-lg font-semibold text-heading-light">Amount Paid:</span>
                  <span class="text-xl font-bold text-green-600" id="totalPaid">Rs. 0.00</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-lg font-semibold text-heading-light">Remaining Balance:</span>
                  <span class="text-xl font-bold" id="remainingBalanceSummary">Rs. 0.00</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Schedule -->
          <div class="bg-card-light rounded-lg border border-border-light">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-heading-light mb-4" id="paymentScheduleTitle">Payment Schedule</h3>
              <div class="payment-schedule">
                <table class="w-full">
                  <thead class="sticky top-0 bg-white">
                    <tr class="border-b border-border-light">
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Date</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Amount Due</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Amount Paid</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Status</th>
                      <th class="text-left py-3 px-4 font-semibold text-heading-light">Actions</th>
                    </tr>
                  </thead>
                  <tbody id="paymentScheduleBody">
                    <tr>
                      <td colspan="5" class="py-8 text-center text-text-light">
                        <span class="material-icons text-4xl text-gray-300 block mb-2">schedule</span>
                        <p>Loading payment schedule...</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          </div> <!-- End customerContent -->
        </main>
      </div>
      <!-- mobile backdrop -->
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
    <script>
      // Get customer ID from URL
      function getCustomerIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
      }

      // Load customer data from API
      async function loadCustomerData() {
        const customerId = getCustomerIdFromUrl();
        
        if (!customerId) {
          document.getElementById('loadingState').innerHTML = '<p class="text-red-600">Customer ID is required. Please select a customer from the list.</p>';
          return;
        }

        try {
          // Use relative path - should work from admin/customer-detail.php
          const apiUrl = `api/get-customer-detail.php?id=${customerId}`;
          console.log('Fetching customer data from:', apiUrl);
          console.log('Customer ID:', customerId);
          
          const response = await fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin', // Include cookies/session
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            }
          });
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          const contentType = response.headers.get('content-type');
          if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Invalid response format from server');
          }
          
          const data = await response.json();
          console.log('Response data:', data);

          if (data.success && data.customer) {
            console.log('Customer data loaded:', data.customer);
            console.log('Active installments from API:', data.active_installments);
            // Attach active_installments to customer object for easier access
            data.customer.active_installments = data.active_installments || [];
            displayCustomerData(data.customer, data);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('customerContent').classList.remove('hidden');
          } else {
            const errorMsg = data.error || 'Failed to load customer data';
            console.error('API error:', errorMsg);
            document.getElementById('loadingState').innerHTML = `<p class="text-red-600">${errorMsg}</p>`;
          }
        } catch (error) {
          console.error('Error loading customer:', error);
          document.getElementById('loadingState').innerHTML = `<p class="text-red-600">Error: ${error.message}. Please check the browser console for more details.</p>`;
        }
      }

      // Helper function to safely set text content
      function setTextContent(elementId, value) {
        const element = document.getElementById(elementId);
        if (element) {
          element.textContent = value;
        } else {
          console.warn(`Element with ID '${elementId}' not found`);
        }
      }

      // Display customer data
      function displayCustomerData(customer, data = {}) {
        try {
          // Basic Information
          setTextContent('customerName', customer.full_name || `${customer.first_name || ''} ${customer.last_name || ''}`.trim() || 'N/A');
          setTextContent('customerCode', customer.customer_code || 'N/A');
          setTextContent('customerEmail', customer.email || 'N/A');
          setTextContent('email', customer.email || 'N/A');
          setTextContent('customerMobile', customer.mobile || 'N/A');
          setTextContent('mobile', customer.mobile || 'N/A');
          setTextContent('customerNIC', customer.nic || 'N/A');
          
          const remainingBalance = parseFloat(customer.remaining_balance || 0);
          const remainingBalanceEl = document.getElementById('remainingBalance');
          if (remainingBalanceEl) {
            remainingBalanceEl.textContent = `Rs. ${remainingBalance.toFixed(2)}`;
            if (remainingBalance < 0) {
              remainingBalanceEl.classList.remove('text-red-600');
              remainingBalanceEl.classList.add('text-green-600');
            }
          }

          // Status Badge
          const statusBadge = document.getElementById('customerStatus');
          if (statusBadge) {
            statusBadge.textContent = (customer.status || 'active').charAt(0).toUpperCase() + (customer.status || 'active').slice(1);
            statusBadge.className = 'inline-block mt-2 px-3 py-1 rounded-full text-sm font-medium';
            if (customer.status === 'active') {
              statusBadge.classList.add('bg-green-100', 'text-green-700');
            } else if (customer.status === 'inactive') {
              statusBadge.classList.add('bg-gray-100', 'text-gray-700');
            } else if (customer.status === 'blocked') {
              statusBadge.classList.add('bg-red-100', 'text-red-700');
            }
          }

          // Personal Information
          setTextContent('firstName', (customer.first_name && customer.first_name.trim()) ? customer.first_name : 'N/A');
          setTextContent('lastName', (customer.last_name && customer.last_name.trim()) ? customer.last_name : 'N/A');
          setTextContent('fullNameWithSurname', (customer.full_name_with_surname && customer.full_name_with_surname.trim()) ? customer.full_name_with_surname : 'N/A');
          setTextContent('nic', (customer.nic && customer.nic.trim()) ? customer.nic : 'N/A');
          setTextContent('occupation', (customer.occupation && customer.occupation.trim()) ? customer.occupation : 'N/A');

          // Address Information
          setTextContent('address', (customer.address && customer.address.trim()) ? customer.address : 'N/A');
          setTextContent('gnd', (customer.gnd && customer.gnd.trim()) ? customer.gnd : 'N/A');
          setTextContent('lgi', (customer.lgi && customer.lgi.trim()) ? customer.lgi : 'N/A');
          setTextContent('policeStation', (customer.police_station && customer.police_station.trim()) ? customer.police_station : 'N/A');
          setTextContent('residencePeriod', (customer.residence_period && customer.residence_period.trim()) ? customer.residence_period : 'N/A');

          // Documents - Set image sources and visibility
          const customerPhoto = document.getElementById('customerPhoto');
          if (customerPhoto && customer.customer_photo_path) {
            customerPhoto.src = '../' + customer.customer_photo_path;
          }
          
          // NIC Front Image
          const nicFrontContainer = document.getElementById('nicFrontContainer');
          if (nicFrontContainer) {
            if (customer.nic_front_path && customer.nic_front_path.trim()) {
              const nicFrontImg = document.getElementById('nicFrontImg');
              const nicFrontPlaceholder = document.getElementById('nicFrontPlaceholder');
              if (nicFrontImg && nicFrontPlaceholder) {
                nicFrontImg.onload = function() {
                  this.style.display = 'block';
                  nicFrontPlaceholder.style.display = 'none';
                  nicFrontContainer.classList.add('cursor-pointer');
                };
                nicFrontImg.onerror = function() {
                  this.style.display = 'none';
                  nicFrontPlaceholder.style.display = 'flex';
                  nicFrontContainer.classList.remove('cursor-pointer');
                };
                nicFrontImg.src = '../' + customer.nic_front_path;
              }
            } else {
              nicFrontContainer.classList.remove('cursor-pointer');
            }
          }
          
          // NIC Back Image
          const nicBackContainer = document.getElementById('nicBackContainer');
          if (nicBackContainer) {
            if (customer.nic_back_path && customer.nic_back_path.trim()) {
              const nicBackImg = document.getElementById('nicBackImg');
              const nicBackPlaceholder = document.getElementById('nicBackPlaceholder');
              if (nicBackImg && nicBackPlaceholder) {
                nicBackImg.onload = function() {
                  this.style.display = 'block';
                  nicBackPlaceholder.style.display = 'none';
                  nicBackContainer.classList.add('cursor-pointer');
                };
                nicBackImg.onerror = function() {
                  this.style.display = 'none';
                  nicBackPlaceholder.style.display = 'flex';
                  nicBackContainer.classList.remove('cursor-pointer');
                };
                nicBackImg.src = '../' + customer.nic_back_path;
              }
            } else {
              nicBackContainer.classList.remove('cursor-pointer');
            }
          }

          // Financial Summary
          const totalPurchased = parseFloat(customer.total_purchased || 0);
          const totalPaid = parseFloat(customer.total_paid || 0);
          setTextContent('totalPurchased', `Rs. ${totalPurchased.toFixed(2)}`);
          setTextContent('totalPaid', `Rs. ${totalPaid.toFixed(2)}`);
          
          const balanceElement = document.getElementById('remainingBalanceSummary');
          if (balanceElement) {
            balanceElement.textContent = `Rs. ${remainingBalance.toFixed(2)}`;
            if (remainingBalance > 0) {
              balanceElement.classList.remove('text-green-600');
              balanceElement.classList.add('text-red-600');
            } else {
              balanceElement.classList.remove('text-red-600');
              balanceElement.classList.add('text-green-600');
            }
          }

          // Display purchased products
          if (data.all_products && data.all_products.length > 0) {
            displayPurchasedProducts(data.all_products);
          } else {
            displayPurchasedProducts([]);
          }
          
          // Load installments if available
          console.log('Checking installments:', customer.active_installments);
          if (customer.active_installments && Array.isArray(customer.active_installments) && customer.active_installments.length > 0) {
            console.log('Found installments, loading schedules...', customer.active_installments.length);
            // Try to display installments (optional - won't error if elements don't exist)
            try {
              displayInstallments(customer.active_installments);
            } catch (err) {
              console.warn('Could not display installments list:', err);
            }
            // Load all payment schedules from all active installments
            loadAllPaymentSchedules(customer.active_installments);
          } else {
            console.log('No installments found or empty array');
            // No installments - show empty schedule
            displayAllPaymentSchedules([]);
          }
        } catch (error) {
          console.error('Error in displayCustomerData:', error);
          alert('Error displaying customer data: ' + error.message);
        }
      }

      // Display purchased products in the table
      function displayPurchasedProducts(products) {
        const productTableBody = document.getElementById('productTableBody');
        
        if (!productTableBody) {
          console.warn('productTableBody element not found');
          return;
        }
        
        if (!products || products.length === 0) {
          productTableBody.innerHTML = `
            <tr>
              <td colspan="5" class="py-8 text-center text-text-light">
                <span class="material-icons text-4xl text-gray-300 block mb-2">shopping_cart</span>
                <p>No products purchased yet</p>
              </td>
            </tr>
          `;
          return;
        }
        
        // Build table rows
        productTableBody.innerHTML = products.map(product => {
          const purchaseDate = product.order_date 
            ? new Date(product.order_date).toLocaleDateString('en-GB', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
              })
            : '-';
          
          const productName = product.product_name || 'N/A';
          const unitPrice = parseFloat(product.unit_price || 0).toFixed(2);
          const quantity = parseInt(product.quantity || 0);
          const subtotal = parseFloat(product.subtotal || 0).toFixed(2);
          
          return `
            <tr class="border-b border-border-light hover:bg-gray-50">
              <td class="py-3 px-4 text-heading-light font-medium">${productName}</td>
              <td class="py-3 px-4 text-text-light">Rs. ${unitPrice}</td>
              <td class="py-3 px-4 text-text-light">${quantity}</td>
              <td class="py-3 px-4 text-heading-light font-semibold">Rs. ${subtotal}</td>
              <td class="py-3 px-4 text-text-light">${purchaseDate}</td>
            </tr>
          `;
        }).join('');
      }

      // Display active installments
      function displayInstallments(installments) {
        const installmentsSection = document.getElementById('installmentsSection');
        const installmentsList = document.getElementById('installmentsList');
        
        // If elements don't exist, skip this function (optional UI element)
        if (!installmentsSection || !installmentsList) {
          console.log('Installments section elements not found, skipping display');
          return;
        }
        
        if (installments.length === 0) {
          installmentsSection.classList.add('hidden');
          return;
        }
        
        installmentsSection.classList.remove('hidden');
        
        installmentsList.innerHTML = installments.map(installment => {
          const completion = installment.completion_percentage || 0;
          const statusBadge = installment.status === 'active' 
            ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Active</span>'
            : '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Pending</span>';
          
          const nextDue = installment.next_due 
            ? `<p class="text-sm text-text-light mt-1">Next Due: ${new Date(installment.next_due.schedule_date).toLocaleDateString()} - Rs. ${parseFloat(installment.next_due.due_amount).toFixed(2)}</p>`
            : '<p class="text-sm text-green-600 mt-1">All payments completed</p>';
          
          return `
            <div class="p-4 border border-border-light rounded-lg mb-3 cursor-pointer hover:bg-gray-50 transition-colors" 
                 onclick="loadInstallmentSchedule(${installment.id})">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h4 class="font-semibold text-heading-light">${installment.order_number}</h4>
                    ${statusBadge}
                  </div>
                  <p class="text-sm text-text-light">Products: ${installment.products.map(p => p.product_name).join(', ')}</p>
                  <p class="text-sm text-text-light">Total: Rs. ${parseFloat(installment.total_amount).toFixed(2)} | Balance: Rs. ${parseFloat(installment.remaining_balance).toFixed(2)}</p>
                  ${nextDue}
                  <div class="mt-2">
                    <div class="flex items-center gap-2">
                      <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-primary h-2 rounded-full transition-all" style="width: ${completion}%"></div>
                      </div>
                      <span class="text-sm font-medium text-text-light">${completion}%</span>
                    </div>
                  </div>
                </div>
                <span class="material-icons text-text-light">chevron_right</span>
              </div>
            </div>
          `;
        }).join('');
      }

      let currentOrderId = null;

      // Load all payment schedules from all active installments
      async function loadAllPaymentSchedules(installments) {
        console.log('loadAllPaymentSchedules called with installments:', installments);
        
        if (!installments || installments.length === 0) {
          console.log('No installments found, displaying empty schedule');
          displayAllPaymentSchedules([]);
          return;
        }

        try {
          // Fetch schedules from all installments
          const schedulePromises = installments.map(async (installment) => {
            try {
              console.log(`Fetching schedule for order ${installment.id}...`);
              const response = await fetch(`api/get-installment-schedule.php?order_id=${installment.id}`);
              
              if (!response.ok) {
                console.error(`HTTP error for order ${installment.id}: ${response.status}`);
                return [];
              }
              
              const data = await response.json();
              console.log(`Schedule data for order ${installment.id}:`, data);
              
              if (data.success && data.schedules && data.schedules.length > 0) {
                // Add order info to each schedule for context
                return data.schedules.map(schedule => ({
                  ...schedule,
                  order_number: data.order.order_number,
                  installment_period: data.order.installment_period || 60
                }));
              } else {
                console.warn(`No schedules found for order ${installment.id}`, data);
                return [];
              }
            } catch (err) {
              console.error(`Error loading schedule for order ${installment.id}:`, err);
              return [];
            }
          });

          const allSchedulesArrays = await Promise.all(schedulePromises);
          const allSchedules = allSchedulesArrays.flat();
          
          console.log('All schedules combined:', allSchedules);
          
          if (allSchedules.length === 0) {
            console.warn('No schedules found from any installments');
            displayAllPaymentSchedules([]);
            return;
          }
          
          // Sort by date
          allSchedules.sort((a, b) => new Date(a.schedule_date) - new Date(b.schedule_date));
          
          // Update title based on installment periods
          const periods = installments.map(i => i.installment_period || 60);
          const maxPeriod = Math.max(...periods);
          const titleElement = document.getElementById('paymentScheduleTitle');
          if (titleElement) {
            titleElement.textContent = `${maxPeriod}-Day Payment Schedule`;
          }
          
          displayAllPaymentSchedules(allSchedules);
        } catch (error) {
          console.error('Error loading all payment schedules:', error);
          displayAllPaymentSchedules([]);
        }
      }

      // Display all payment schedules in the table
      function displayAllPaymentSchedules(schedules) {
        const paymentScheduleBody = document.getElementById('paymentScheduleBody');
        
        if (!paymentScheduleBody) {
          console.warn('paymentScheduleBody element not found');
          return;
        }
        
        if (!schedules || schedules.length === 0) {
          paymentScheduleBody.innerHTML = `
            <tr>
              <td colspan="5" class="py-8 text-center text-text-light">
                <span class="material-icons text-4xl text-gray-300 block mb-2">schedule</span>
                <p>No payment schedule available</p>
              </td>
            </tr>
          `;
          return;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Build table rows
        paymentScheduleBody.innerHTML = schedules.map(schedule => {
          const scheduleDate = new Date(schedule.schedule_date);
          scheduleDate.setHours(0, 0, 0, 0);
          const isOverdue = schedule.is_overdue || (schedule.status === 'pending' && scheduleDate < today);
          
          let statusClass = 'text-yellow-600';
          let statusText = 'Not Paid';
          if (schedule.status === 'paid') {
            statusClass = 'text-green-600';
            statusText = 'Paid';
          } else if (schedule.status === 'missed' || isOverdue) {
            statusClass = 'text-red-600';
            statusText = 'Missed';
          } else if (schedule.status === 'partial') {
            statusClass = 'text-orange-600';
            statusText = 'Partially Paid';
          }
          
          const rowClass = isOverdue ? 'bg-red-50' : '';
          const dueAmount = parseFloat(schedule.due_amount || 0).toFixed(2);
          const paidAmount = parseFloat(schedule.paid_amount || 0).toFixed(2);
          
          // Format date for input field (YYYY-MM-DD)
          const dateInputValue = schedule.schedule_date ? schedule.schedule_date.split('T')[0] : '';
          // Escape values for onclick handler
          const escapedDate = dateInputValue.replace(/'/g, "\\'");
          const escapedStatus = (schedule.status || 'pending').replace(/'/g, "\\'");
          
          return `
            <tr class="${rowClass} border-b border-border-light hover:bg-gray-50">
              <td class="py-3 px-4 text-text-light">${scheduleDate.toLocaleDateString('en-GB', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
              })}</td>
              <td class="py-3 px-4 text-heading-light font-semibold">Rs. ${dueAmount}</td>
              <td class="py-3 px-4 text-green-600 font-medium">Rs. ${paidAmount}</td>
              <td class="py-3 px-4 ${statusClass} font-medium">${statusText}</td>
              <td class="py-3 px-4">
                <button data-schedule-id="${schedule.id}" 
                        data-schedule-date="${escapedDate}" 
                        data-due-amount="${schedule.due_amount || 0}" 
                        data-paid-amount="${schedule.paid_amount || 0}" 
                        data-status="${escapedStatus}"
                        class="edit-schedule-btn flex items-center gap-1 px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                  <span class="material-icons text-sm">edit</span>
                  <span>Edit</span>
                </button>
              </td>
            </tr>
          `;
        }).join('');
      }

      // Load installment schedule (for detail view - optional)
      async function loadInstallmentSchedule(orderId) {
        currentOrderId = orderId;
        const scheduleSection = document.getElementById('installmentScheduleSection');
        const scheduleBody = document.getElementById('installmentScheduleBody');
        const orderSummary = document.getElementById('orderSummary');
        
        // If elements don't exist, skip this function (optional UI element)
        if (!scheduleSection || !scheduleBody) {
          console.log('Installment schedule detail section not found, skipping');
          return;
        }
        
        try {
          const response = await fetch(`api/get-installment-schedule.php?order_id=${orderId}`);
          const data = await response.json();
          
          if (data.success) {
            if (scheduleSection) scheduleSection.classList.remove('hidden');
            if (orderSummary) orderSummary.classList.remove('hidden');
            
            // Update order summary with null checks
            const orderNumberEl = document.getElementById('orderNumber');
            const orderTotalEl = document.getElementById('orderTotal');
            const orderBalanceEl = document.getElementById('orderBalance');
            const orderProgressEl = document.getElementById('orderProgress');
            
            if (orderNumberEl) orderNumberEl.textContent = data.order.order_number;
            if (orderTotalEl) orderTotalEl.textContent = `Rs. ${parseFloat(data.order.total_amount).toFixed(2)}`;
            if (orderBalanceEl) orderBalanceEl.textContent = `Rs. ${parseFloat(data.order.remaining_balance).toFixed(2)}`;
            if (orderProgressEl) orderProgressEl.textContent = `${data.summary.completion_percentage}%`;
            
            // Display schedule
            if (data.schedules.length === 0) {
              scheduleBody.innerHTML = `
                <tr>
                  <td colspan="5" class="py-8 text-center text-text-light">
                    <span class="material-icons text-4xl text-gray-300 block mb-2">schedule</span>
                    <p>No schedule available</p>
                  </td>
                </tr>
              `;
            } else {
              scheduleBody.innerHTML = data.schedules.map(schedule => {
                const scheduleDate = new Date(schedule.schedule_date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const isOverdue = schedule.is_overdue || (schedule.status === 'pending' && scheduleDate < today);
                
                let statusClass = 'text-yellow-600';
                let statusText = 'Pending';
                if (schedule.status === 'paid') {
                  statusClass = 'text-green-600';
                  statusText = 'Paid';
                } else if (schedule.status === 'missed' || isOverdue) {
                  statusClass = 'text-red-600';
                  statusText = 'Missed';
                } else if (schedule.status === 'partial') {
                  statusClass = 'text-orange-600';
                  statusText = 'Partial';
                }
                
                const rowClass = isOverdue ? 'bg-red-50' : '';
                
                return `
                  <tr class="${rowClass} border-b border-border-light hover:bg-gray-50">
                    <td class="py-3 px-4 text-text-light">${scheduleDate.toLocaleDateString()}</td>
                    <td class="py-3 px-4 text-heading-light font-semibold">Rs. ${parseFloat(schedule.due_amount).toFixed(2)}</td>
                    <td class="py-3 px-4 text-green-600 font-medium">Rs. ${parseFloat(schedule.paid_amount || 0).toFixed(2)}</td>
                    <td class="py-3 px-4 ${statusClass} font-medium">${statusText}</td>
                    <td class="py-3 px-4 text-text-light">${schedule.payment_date ? new Date(schedule.payment_date).toLocaleDateString() : '-'}</td>
                  </tr>
                `;
              }).join('');
            }
          }
        } catch (error) {
          console.error('Error loading schedule:', error);
        }
      }

      // Payment Modal
      const paymentModal = document.createElement('div');
      paymentModal.id = 'paymentModal';
      paymentModal.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4';
      paymentModal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-heading-light">Make Payment</h3>
            <button id="closePaymentModal" class="text-text-light hover:text-heading-light">
              <span class="material-icons">close</span>
            </button>
          </div>
          <form id="paymentForm">
            <div class="mb-4">
              <label class="block text-sm font-medium text-heading-light mb-2">Payment Amount *</label>
              <input type="number" id="paymentAmount" step="0.01" min="0.01" required
                     class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-heading-light mb-2">Payment Method</label>
              <select id="paymentMethod" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="mobile">Mobile Payment</option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-heading-light mb-2">Payment Date</label>
              <input type="datetime-local" id="paymentDate" 
                     value="${new Date().toISOString().slice(0, 16)}"
                     class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-heading-light mb-2">Notes (Optional)</label>
              <textarea id="paymentNotes" rows="3" 
                        class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div class="flex gap-3">
              <button type="submit" class="flex-1 bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/90">
                Process Payment
              </button>
              <button type="button" id="cancelPayment" class="px-6 py-3 border border-border-light rounded-lg font-semibold text-text-light hover:bg-gray-50">
                Cancel
              </button>
            </div>
          </form>
        </div>
      `;
      document.body.appendChild(paymentModal);

      // Payment modal handlers (optional - only if elements exist)
      const makePaymentBtn = document.getElementById('makePaymentBtn');
      if (makePaymentBtn) {
        makePaymentBtn.addEventListener('click', () => {
          if (!currentOrderId) {
            alert('Please select an installment first');
            return;
          }
          paymentModal.classList.remove('hidden');
        });
      }

      const closePaymentModal = document.getElementById('closePaymentModal');
      if (closePaymentModal) {
        closePaymentModal.addEventListener('click', () => {
          paymentModal.classList.add('hidden');
        });
      }

      const cancelPayment = document.getElementById('cancelPayment');
      if (cancelPayment) {
        cancelPayment.addEventListener('click', () => {
          paymentModal.classList.add('hidden');
        });
      }

      paymentModal.addEventListener('click', (e) => {
        if (e.target === paymentModal) {
          paymentModal.classList.add('hidden');
        }
      });

      // Payment form submission
      const paymentForm = document.getElementById('paymentForm');
      if (paymentForm) {
        paymentForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          
          if (!currentOrderId) {
            alert('No order selected');
            return;
          }
          
          const amount = parseFloat(document.getElementById('paymentAmount').value);
          const method = document.getElementById('paymentMethod').value;
          const date = document.getElementById('paymentDate').value;
          const notes = document.getElementById('paymentNotes').value;
          
          if (amount <= 0) {
            alert('Payment amount must be greater than 0');
            return;
          }
          
          const submitBtn = e.target.querySelector('button[type="submit"]');
          submitBtn.disabled = true;
          submitBtn.textContent = 'Processing...';
          
          try {
            const response = await fetch('api/process-installment-payment.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                order_id: currentOrderId,
                amount: amount,
                payment_method: method,
                payment_date: date,
                notes: notes || null
              })
            });
            
            const data = await response.json();
            
            if (data.success) {
              if (typeof showNotificationDialog === 'function') {
                showNotificationDialog({
                  title: 'Success',
                  message: 'Payment processed successfully!',
                  type: 'success'
                });
              } else {
                alert('Payment processed successfully!');
              }
              
              paymentModal.classList.add('hidden');
              
              // Reload customer data (which will reload payment schedules)
              await loadCustomerData();
              
              if (data.is_completed) {
                setTimeout(() => {
                  alert('Installment completed! Customer is now eligible for new assignments.');
                }, 1000);
              }
            } else {
              alert('Error: ' + (data.error || 'Failed to process payment'));
              submitBtn.disabled = false;
              submitBtn.textContent = 'Process Payment';
            }
          } catch (error) {
            console.error('Error processing payment:', error);
            alert('Error: Failed to process payment. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Process Payment';
          }
        });
      }

      // Image Modal with Zoom Functionality
      let currentZoom = 1;
      const minZoom = 0.5;
      const maxZoom = 5;
      const zoomStep = 0.25;

      function openImageModal(imageSrc, imageTitle) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalImageTitle');
        
        modalImage.src = imageSrc;
        modalTitle.textContent = imageTitle;
        currentZoom = 1;
        modalImage.style.transform = `scale(${currentZoom})`;
        modal.classList.remove('hidden');
      }

      function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        currentZoom = 1;
        document.getElementById('modalImage').style.transform = `scale(${currentZoom})`;
      }

      function zoomIn() {
        if (currentZoom < maxZoom) {
          currentZoom = Math.min(currentZoom + zoomStep, maxZoom);
          document.getElementById('modalImage').style.transform = `scale(${currentZoom})`;
        }
      }

      function zoomOut() {
        if (currentZoom > minZoom) {
          currentZoom = Math.max(currentZoom - zoomStep, minZoom);
          document.getElementById('modalImage').style.transform = `scale(${currentZoom})`;
        }
      }

      function resetZoom() {
        currentZoom = 1;
        document.getElementById('modalImage').style.transform = `scale(${currentZoom})`;
      }

      // Set up image click handlers
      function setupImageClickHandlers() {
        const nicFrontContainer = document.getElementById('nicFrontContainer');
        const nicBackContainer = document.getElementById('nicBackContainer');
        const nicFrontImg = document.getElementById('nicFrontImg');
        const nicBackImg = document.getElementById('nicBackImg');

        // NIC Front click handler
        nicFrontContainer.addEventListener('click', function() {
          const imgSrc = nicFrontImg.src;
          if (imgSrc && imgSrc !== window.location.href && nicFrontImg.style.display !== 'none' && nicFrontImg.complete) {
            openImageModal(imgSrc, 'NIC Front');
          }
        });

        // NIC Back click handler
        nicBackContainer.addEventListener('click', function() {
          const imgSrc = nicBackImg.src;
          if (imgSrc && imgSrc !== window.location.href && nicBackImg.style.display !== 'none' && nicBackImg.complete) {
            openImageModal(imgSrc, 'NIC Back');
          }
        });

        // Modal controls
        document.getElementById('closeImageModal').addEventListener('click', closeImageModal);
        document.getElementById('zoomIn').addEventListener('click', zoomIn);
        document.getElementById('zoomOut').addEventListener('click', zoomOut);
        document.getElementById('resetZoom').addEventListener('click', resetZoom);

        // Close modal on backdrop click
        document.getElementById('imageModal').addEventListener('click', function(e) {
          if (e.target.id === 'imageModal') {
            closeImageModal();
          }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
          const modal = document.getElementById('imageModal');
          if (modal.classList.contains('hidden')) return;

          if (e.key === 'Escape') {
            closeImageModal();
          } else if (e.key === '+' || e.key === '=') {
            e.preventDefault();
            zoomIn();
          } else if (e.key === '-') {
            e.preventDefault();
            zoomOut();
          } else if (e.key === '0') {
            e.preventDefault();
            resetZoom();
          }
        });

        // Mouse wheel zoom (when modal is open)
        const modalImageContainer = document.querySelector('#imageModal > div > div');
        modalImageContainer.addEventListener('wheel', function(e) {
          const modal = document.getElementById('imageModal');
          if (modal.classList.contains('hidden')) return;

          e.preventDefault();
          if (e.deltaY < 0) {
            zoomIn();
          } else {
            zoomOut();
          }
        }, { passive: false });
      }

      

      // Load payment history for all installments
      async function loadPaymentHistory() {
        const customerId = getCustomerIdFromUrl();
        if (!customerId) {
          alert('Customer ID is required');
          return;
        }

        const modal = document.getElementById('paymentHistoryModal');
        const loading = document.getElementById('paymentHistoryLoading');
        const content = document.getElementById('paymentHistoryContent');
        const empty = document.getElementById('paymentHistoryEmpty');
        const installmentsContainer = document.getElementById('paymentHistoryInstallments');

        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        empty.classList.add('hidden');

        try {
          // Get customer details with installments
          const response = await fetch(`api/get-customer-detail.php?id=${customerId}`, {
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          
          if (!data.success || !data.customer) {
            throw new Error(data.error || 'Failed to load customer data');
          }

          const installments = data.active_installments || [];
          
          if (installments.length === 0) {
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
          }

          // Load schedules for all installments
          const schedulePromises = installments.map(async (installment) => {
            try {
              const scheduleRes = await fetch(`api/get-installment-schedule.php?order_id=${installment.id}`, {
                credentials: 'same-origin',
                headers: {
                  'Accept': 'application/json'
                }
              });
              
              if (scheduleRes.ok) {
                const scheduleData = await scheduleRes.json();
                if (scheduleData.success && scheduleData.schedules) {
                  return {
                    installment: installment,
                    schedules: scheduleData.schedules,
                    order: scheduleData.order || installment
                  };
                }
              }
              return null;
            } catch (err) {
              console.error(`Error loading schedule for order ${installment.id}:`, err);
              return null;
            }
          });

          const allSchedulesData = await Promise.all(schedulePromises);
          const validSchedulesData = allSchedulesData.filter(item => item !== null);

          if (validSchedulesData.length === 0) {
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
            return;
          }

          // Calculate totals
          let totalPaid = 0;
          let totalDue = 0;
          let completedCount = 0;
          let pendingCount = 0;
          let remainingBalance = 0;

          validSchedulesData.forEach(({ installment }) => {
            totalDue += parseFloat(installment.total_amount || 0);
            remainingBalance += parseFloat(installment.remaining_balance || 0);
          });

          validSchedulesData.forEach(({ schedules }) => {
            schedules.forEach(schedule => {
              const paid = parseFloat(schedule.paid_amount || 0);
              totalPaid += paid;
              
              if (schedule.status === 'paid') {
                completedCount++;
              } else if (schedule.status === 'pending' || schedule.status === 'missed') {
                pendingCount++;
              }
            });
          });

          // Update summary
          document.getElementById('historyTotalPaid').textContent = `Rs. ${totalPaid.toFixed(2)}`;
          document.getElementById('historyCompletedCount').textContent = completedCount;
          document.getElementById('historyPendingCount').textContent = pendingCount;
          document.getElementById('historyRemainingBalance').textContent = `Rs. ${remainingBalance.toFixed(2)}`;

          // Display installments with payment history
          installmentsContainer.innerHTML = validSchedulesData.map(({ installment, schedules, order }) => {
            const installmentSchedules = schedules.map(schedule => {
              const scheduleDate = new Date(schedule.schedule_date);
              const paymentDate = schedule.payment_date ? new Date(schedule.payment_date) : null;
              
              let statusClass = 'bg-yellow-100 text-yellow-700';
              let statusText = 'Pending';
              if (schedule.status === 'paid') {
                statusClass = 'bg-green-100 text-green-700';
                statusText = 'Paid';
              } else if (schedule.status === 'missed') {
                statusClass = 'bg-red-100 text-red-700';
                statusText = 'Missed';
              } else if (schedule.status === 'partial') {
                statusClass = 'bg-orange-100 text-orange-700';
                statusText = 'Partially Paid';
              }

              const dueAmount = parseFloat(schedule.due_amount || 0);
              const paidAmount = parseFloat(schedule.paid_amount || 0);

              return `
                <tr class="border-b border-border-light hover:bg-gray-50">
                  <td class="py-3 px-4 text-text-light">${scheduleDate.toLocaleDateString('en-GB', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric' 
                  })}</td>
                  <td class="py-3 px-4 text-heading-light font-semibold">Rs. ${dueAmount.toFixed(2)}</td>
                  <td class="py-3 px-4 text-green-600 font-medium">Rs. ${paidAmount.toFixed(2)}</td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${statusText}</span>
                  </td>
                  <td class="py-3 px-4 text-text-light">
                    ${paymentDate ? paymentDate.toLocaleDateString('en-GB', { 
                      day: '2-digit', 
                      month: '2-digit', 
                      year: 'numeric' 
                    }) : '-'}
                  </td>
                  <td class="py-3 px-4 text-text-light">
                    ${schedule.payment_method ? schedule.payment_method.charAt(0).toUpperCase() + schedule.payment_method.slice(1) : '-'}
                  </td>
                </tr>
              `;
            }).join('');

            return `
              <div class="mb-6 bg-card-light p-6 rounded-lg border border-border-light">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h4 class="text-lg font-semibold text-heading-light">Order: ${order.order_number || installment.order_number}</h4>
                    <p class="text-sm text-text-light">Total Amount: Rs. ${parseFloat(installment.total_amount || 0).toFixed(2)} | Remaining: Rs. ${parseFloat(installment.remaining_balance || 0).toFixed(2)}</p>
                  </div>
                  <span class="px-3 py-1 rounded-full text-sm font-medium ${
                    installment.status === 'completed' 
                      ? 'bg-green-100 text-green-700' 
                      : 'bg-blue-100 text-blue-700'
                  }">
                    ${installment.status === 'completed' ? 'Completed' : 'Active'}
                  </span>
                </div>
                <div class="overflow-x-auto">
                  <table class="w-full">
                    <thead>
                      <tr class="border-b border-border-light bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Due Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Due Amount</th>
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Paid Amount</th>
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Payment Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-heading-light">Method</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${installmentSchedules}
                    </tbody>
                  </table>
                </div>
              </div>
            `;
          }).join('');

          loading.classList.add('hidden');
          content.classList.remove('hidden');

        } catch (error) {
          console.error('Error loading payment history:', error);
          loading.classList.add('hidden');
          alert('Failed to load payment history: ' + error.message);
        }
      }

      // Payment History Modal handlers
      const paymentHistoryBtn = document.getElementById('paymentHistoryBtn');
      const paymentHistoryModal = document.getElementById('paymentHistoryModal');
      const closePaymentHistoryModal = document.getElementById('closePaymentHistoryModal');

      if (paymentHistoryBtn) {
        paymentHistoryBtn.addEventListener('click', loadPaymentHistory);
      }

      if (closePaymentHistoryModal) {
        closePaymentHistoryModal.addEventListener('click', function() {
          paymentHistoryModal.classList.add('hidden');
        });
      }

      if (paymentHistoryModal) {
        paymentHistoryModal.addEventListener('click', function(e) {
          if (e.target.id === 'paymentHistoryModal') {
            paymentHistoryModal.classList.add('hidden');
          }
        });
      }

      // Edit Schedule Modal Functions
      function openEditScheduleModal(scheduleId, scheduleDate, dueAmount, paidAmount, status) {
        console.log('openEditScheduleModal called with:', { scheduleId, scheduleDate, dueAmount, paidAmount, status });
        
        const modal = document.getElementById('editScheduleModal');
        if (!modal) {
          console.error('Edit schedule modal not found in DOM');
          alert('Error: Edit modal not found. Please refresh the page.');
          return;
        }
        
        console.log('Modal found:', modal);
        console.log('Modal classes before:', modal.className);
        
        const scheduleIdInput = document.getElementById('editScheduleId');
        const scheduleDateInput = document.getElementById('editScheduleDate');
        const dueAmountInput = document.getElementById('editDueAmount');
        const paidAmountInput = document.getElementById('editPaidAmount');
        const statusSelect = document.getElementById('editStatus');
        
        if (!scheduleIdInput || !scheduleDateInput || !dueAmountInput || !paidAmountInput || !statusSelect) {
          console.error('One or more form elements not found:', {
            scheduleIdInput: !!scheduleIdInput,
            scheduleDateInput: !!scheduleDateInput,
            dueAmountInput: !!dueAmountInput,
            paidAmountInput: !!paidAmountInput,
            statusSelect: !!statusSelect
          });
        }
        
        if (scheduleIdInput) scheduleIdInput.value = scheduleId || '';
        if (scheduleDateInput) scheduleDateInput.value = scheduleDate || '';
        if (dueAmountInput) dueAmountInput.value = parseFloat(dueAmount || 0).toFixed(2);
        if (paidAmountInput) paidAmountInput.value = parseFloat(paidAmount || 0).toFixed(2);
        if (statusSelect) statusSelect.value = status || 'pending';
        
        // Remove hidden class and ensure modal is visible
        modal.classList.remove('hidden');
        modal.style.display = 'flex'; // Force display
        modal.style.zIndex = '9999'; // Ensure it's on top
        
        console.log('Modal classes after:', modal.className);
        console.log('Modal display style:', modal.style.display);
        console.log('Modal should now be visible');
      }

      function closeEditScheduleModal() {
        const modal = document.getElementById('editScheduleModal');
        if (modal) {
          modal.classList.add('hidden');
          modal.style.display = 'none';
        }
      }
      
      // Event delegation for edit buttons (works with dynamically generated buttons)
      // Set up after DOM is ready
      function setupEditButtonHandlers() {
        // Remove any existing listeners by using a named function
        document.removeEventListener('click', handleEditButtonClick);
        document.addEventListener('click', handleEditButtonClick);
      }
      
      function handleEditButtonClick(e) {
        // Check if clicked element or its parent has the edit button class
        const btn = e.target.closest('.edit-schedule-btn');
        if (btn) {
          e.preventDefault();
          e.stopPropagation();
          
          console.log('Edit button clicked', btn);
          
          const scheduleId = btn.getAttribute('data-schedule-id');
          const scheduleDate = btn.getAttribute('data-schedule-date');
          const dueAmount = btn.getAttribute('data-due-amount');
          const paidAmount = btn.getAttribute('data-paid-amount');
          const status = btn.getAttribute('data-status');
          
          console.log('Schedule data:', { scheduleId, scheduleDate, dueAmount, paidAmount, status });
          
          if (scheduleId) {
            openEditScheduleModal(scheduleId, scheduleDate, dueAmount, paidAmount, status);
          } else {
            console.error('Schedule ID not found on edit button');
            alert('Error: Schedule ID not found');
          }
        }
      }
      
      // Initialize event handlers
      setupEditButtonHandlers();

      // Edit Schedule Form Handler
      const editScheduleForm = document.getElementById('editScheduleForm');
      if (editScheduleForm) {
        editScheduleForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          
          const scheduleId = document.getElementById('editScheduleId').value;
          const dueAmount = parseFloat(document.getElementById('editDueAmount').value);
          const paidAmount = parseFloat(document.getElementById('editPaidAmount').value);
          const status = document.getElementById('editStatus').value;
          
          if (!scheduleId) {
            alert('Schedule ID is required');
            return;
          }
          
          if (paidAmount > dueAmount) {
            alert('Paid amount cannot exceed due amount');
            return;
          }
          
          if (paidAmount < 0 || dueAmount < 0) {
            alert('Amounts cannot be negative');
            return;
          }
          
          const submitBtn = e.target.querySelector('button[type="submit"]');
          submitBtn.disabled = true;
          submitBtn.textContent = 'Updating...';
          
          try {
            const response = await fetch('api/update-schedule-entry.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              credentials: 'same-origin',
              body: JSON.stringify({
                schedule_id: scheduleId,
                due_amount: dueAmount,
                paid_amount: paidAmount,
                status: status
              })
            });
            
            const data = await response.json();
            
            if (data.success) {
              if (typeof showNotificationDialog === 'function') {
                showNotificationDialog({
                  title: 'Success',
                  message: 'Payment schedule updated successfully!',
                  type: 'success'
                });
              } else {
                alert('Payment schedule updated successfully!');
              }
              
              closeEditScheduleModal();
              
              // Reload customer data to refresh the schedule
              await loadCustomerData();
            } else {
              alert('Error: ' + (data.error || 'Failed to update schedule'));
              submitBtn.disabled = false;
              submitBtn.textContent = 'Update';
            }
          } catch (error) {
            console.error('Error updating schedule:', error);
            alert('Error: Failed to update schedule. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update';
          }
        });
      }

      // Close edit schedule modal handlers
      const closeEditScheduleModalBtn = document.getElementById('closeEditScheduleModal');
      const cancelEditScheduleBtn = document.getElementById('cancelEditSchedule');
      const editScheduleModal = document.getElementById('editScheduleModal');
      
      if (closeEditScheduleModalBtn) {
        closeEditScheduleModalBtn.addEventListener('click', closeEditScheduleModal);
      }
      
      if (cancelEditScheduleBtn) {
        cancelEditScheduleBtn.addEventListener('click', closeEditScheduleModal);
      }
      
      if (editScheduleModal) {
        editScheduleModal.addEventListener('click', function(e) {
          if (e.target.id === 'editScheduleModal') {
            closeEditScheduleModal();
          }
        });
      }

      // Initialize when page loads
      document.addEventListener('DOMContentLoaded', async function() {
        await loadCustomerData();
        setupImageClickHandlers();
        // Re-setup edit button handlers after data is loaded
        setupEditButtonHandlers();
      });

      // Sidebar toggle functionality
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');

      if (sidebarToggle && mobileSidebar && sidebarBackdrop) {
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
      }
    </script>
  </body>
</html>
