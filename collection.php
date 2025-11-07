<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get user's full name
$full_name = $_SESSION['full_name'] ?? 'User';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Collect Money</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="admin/img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="admin/img/package.png" type="image/png">
    <meta name="description" content="Simple Money Collection App">
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
              "green-bright": "#99EF02",
              "green-dark": "#16a34a",
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
    <style>
      body { 
        font-family: 'Inter', sans-serif; 
        background: #22c55e;
        min-height: 100vh;
      }
      
      .text-green-dark {
        --tw-text-opacity: 1;
        color: rgb(13 71 34);
      }
      
      .text-green-darker {
        --tw-text-opacity: 1;
        color: rgb(8 50 20);
      }
      
      .font-bold {
        font-weight: 700;
      }
      
      .text-lg {
        font-size: 1.5rem;
        line-height: 1.75rem;
      }
      
      .text-xl {
        font-size: 1.75rem;
        line-height: 2rem;
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <header class="p-6 pt-12">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Hello <?php echo htmlspecialchars($full_name); ?> !</h1>
        <div class="flex items-center gap-2">
          <a href="admin/logout.php" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors" title="Logout">
            <span class="material-icons text-white">logout</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 pb-6">
      <!-- ID/Name Search Section -->
      <div class="mb-6">
        <label class="block text-white text-lg font-medium mb-3">ID/Name</label>
        <input 
          type="text" 
          id="customerSearch" 
          placeholder="C001" 
          class="w-full px-4 py-4 border-0 rounded-lg text-lg bg-white text-gray-600 mb-4"
        >
        <button 
          id="searchCustomerBtn" 
          class="w-full bg-green-bright text-black px-6 py-4 rounded-full font-bold text-lg"
        >
          Search
        </button>
      </div>

      <!-- Customer Details Section -->
      <div id="customerDetails" class="hidden mb-6">
        <div class="border-t border-white/20 pt-4 mb-4">
          <h2 class="text-green-darker font-bold text-xl text-center mb-4">Customer Details</h2>
          <div class="text-white space-y-2">
            <div class="flex items-center gap-2">
              <p>Name: <span id="customerName">Robert Anderson</span></p>
              <button id="infoBtn" class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors" title="Balance Information">
                <span class="material-icons text-white text-sm">info</span>
              </button>
              <button id="paymentHistoryBtn" class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors" title="Payment History">
                <span class="material-icons text-white text-sm">history</span>
              </button>
            </div>
            <p>NIC: <span id="customerNIC">199125664V</span></p>
            <p>Mobile: <span id="customerMobile">0778553032</span></p>
          </div>
        </div>
      </div>

      <!-- Missed Payments Alert Section -->
      <div id="missedPaymentsAlert" class="hidden mb-6">
        <div class="bg-red-500 border-2 border-red-700 rounded-lg p-4 mb-4">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <span class="material-icons text-white text-2xl">warning</span>
              <h3 class="text-white font-bold text-lg">Missed Payments Alert</h3>
            </div>
            <button id="closeMissedAlert" class="text-white hover:text-red-200">
              <span class="material-icons">close</span>
            </button>
          </div>
          <p class="text-white mb-3">Customer has <span id="missedCount" class="font-bold">0</span> missed payment(s) totaling <span id="missedTotal" class="font-bold">Rs. 0.00</span></p>
          <button id="viewMissedDetailsBtn" class="w-full bg-white text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-50 transition-colors">
            View Missed Payments Details
          </button>
        </div>
      </div>

      <!-- Due Amount Section -->
      <div id="dueAmount" class="hidden mb-6">
        <div class="border-t border-white/20 pt-4 mb-4">
          <h2 class="text-green-darker font-bold text-lg text-center mb-4">Due Amount</h2>
          <div class="text-center mb-4">
            <p class="text-white text-4xl font-bold" id="dueAmountValue">Rs. 250.00</p>
          </div>
          <button 
            id="payBtn" 
            class="w-full bg-green-bright text-black px-6 py-4 rounded-full font-bold text-lg"
          >
            Pay
          </button>
        </div>
      </div>

      <!-- Payment Input Modal -->
      <div id="paymentModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg max-w-sm w-full">
          <h3 class="text-xl font-bold text-gray-800 mb-4 text-center">Enter Payment Amount</h3>
          <div class="mb-4">
            <input 
              type="number" 
              id="paymentAmount" 
              placeholder="Enter amount" 
              min="0"
              step="0.01"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            >
          </div>
          <div class="flex gap-3">
            <button id="cancelPaymentBtn" class="flex-1 bg-gray-300 text-gray-700 px-4 py-3 rounded-lg font-medium">
              Cancel
            </button>
            <button id="confirmPaymentBtn" class="flex-1 bg-green-bright text-black px-4 py-3 rounded-full font-bold">
              Confirm
            </button>
          </div>
        </div>
      </div>

      <!-- Success Modal -->
      <div id="successModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg max-w-sm w-full text-center">
          <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
            <span class="material-icons text-green-600 text-3xl">check_circle</span>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Payment Successful!</h3>
          <p class="text-gray-600 mb-4 whitespace-pre-line" id="successMessage">Payment collected successfully</p>
          <button id="closeSuccessModal" class="w-full bg-green-bright text-black px-4 py-3 rounded-full font-bold">
            Continue
          </button>
        </div>
      </div>
    </main>

    <!-- Balance Info Popup -->
    <div id="balancePopup" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
      <div class="bg-white p-6 rounded-lg max-w-sm w-full">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-gray-800">Balance Information</h3>
          <button id="closeBalancePopup" class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
            <span class="material-icons text-gray-600 text-sm">close</span>
          </button>
        </div>
        <div class="space-y-3">
          <div class="flex justify-between items-center py-2 border-b border-gray-200">
            <span class="text-gray-600">Remaining (Rs):</span>
            <span class="text-red-600 font-bold" id="popupRemainingAmount">250.00</span>
          </div>
          <div class="flex justify-between items-center py-2 border-b border-gray-200">
            <span class="text-gray-600">Days Left:</span>
            <span class="text-green-600 font-bold" id="popupDaysLeft">4 Days</span>
          </div>
          <div class="flex justify-between items-center py-2">
            <span class="text-gray-600">Paid Amount (Rs):</span>
            <span class="text-green-600 font-bold" id="popupPaidAmount">1650.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Missed Payments Details Modal -->
    <div id="missedPaymentsModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
      <div class="relative w-full max-w-4xl max-h-[90vh] bg-white rounded-lg overflow-hidden">
        <button id="closeMissedModal" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
          <span class="material-icons text-gray-800">close</span>
        </button>
        <div class="p-6 overflow-y-auto" style="max-height: 90vh;">
          <div class="flex items-center gap-3 mb-6">
            <span class="material-icons text-red-600 text-3xl">warning</span>
            <h3 class="text-2xl font-bold text-gray-800">Missed Payments</h3>
          </div>
          
          <!-- Summary -->
          <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-6">
            <div class="flex justify-between items-center mb-2">
              <p class="text-gray-800">
                <span class="font-bold">Total Missed Amount:</span> 
                <span class="text-red-600 font-bold text-xl" id="missedModalTotal">Rs. 0.00</span>
              </p>
              <p class="text-gray-800">
                <span class="font-bold">Selected Amount:</span> 
                <span class="text-green-600 font-bold text-xl" id="missedSelectedTotal">Rs. 0.00</span>
              </p>
            </div>
            <p class="text-gray-600 text-sm mt-1">
              <span class="font-bold" id="missedModalCount">0</span> payment(s) have been missed
            </p>
          </div>
          
          <!-- Missed Payments List -->
          <div id="missedPaymentsList" class="mb-4">
            <!-- Missed payments will be loaded here -->
          </div>
          
          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button id="selectAllMissedBtn" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300">
              Select All
            </button>
            <button id="deselectAllMissedBtn" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300">
              Deselect All
            </button>
            <button id="paySelectedMissedBtn" class="flex-1 bg-green-bright text-black px-4 py-2 rounded-lg font-bold hover:opacity-90">
              Pay Selected (Rs. <span id="paySelectedAmount">0.00</span>)
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment History Modal -->
    <div id="paymentHistoryModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
      <div class="relative w-full max-w-5xl max-h-[90vh] bg-white rounded-lg overflow-hidden">
        <button id="closePaymentHistoryModal" class="absolute top-4 right-4 z-10 bg-white/90 hover:bg-white rounded-full p-2 transition-colors shadow-lg">
          <span class="material-icons text-gray-800">close</span>
        </button>
        <div class="p-6 overflow-y-auto" style="max-height: 90vh;">
          <h3 class="text-2xl font-bold text-gray-800 mb-6">Payment History</h3>
          
          <!-- Loading State -->
          <div id="paymentHistoryLoading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
            <p class="mt-4 text-gray-600">Loading payment history...</p>
          </div>
          
          <!-- Payment History Content -->
          <div id="paymentHistoryContent" class="hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <p class="text-sm text-gray-600">Total Paid</p>
                <p class="text-2xl font-bold text-blue-600" id="historyTotalPaid">Rs. 0.00</p>
              </div>
              <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <p class="text-sm text-gray-600">Completed Payments</p>
                <p class="text-2xl font-bold text-green-600" id="historyCompletedCount">0</p>
              </div>
              <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-sm text-gray-600">Pending Payments</p>
                <p class="text-2xl font-bold text-yellow-600" id="historyPendingCount">0</p>
              </div>
              <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <p class="text-sm text-gray-600">Remaining Balance</p>
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
            <p class="text-gray-600 text-lg">No payment history available</p>
          </div>
        </div>
      </div>
    </div>

    <!-- PWA Install Prompt -->
    <div id="installPrompt" class="fixed bottom-4 left-4 right-4 z-50 hidden">
      <div class="bg-white rounded-xl shadow-2xl p-4 flex items-center gap-4 border border-gray-200 max-w-md mx-auto">
        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
          <span class="material-icons text-white text-2xl">get_app</span>
        </div>
        <div class="flex-1">
          <h3 class="font-bold text-gray-800 text-sm">Install App</h3>
          <p class="text-gray-600 text-xs">Install for quick access and offline use</p>
        </div>
        <div class="flex gap-2">
          <button id="installAppBtn" class="bg-green-bright text-black px-4 py-2 rounded-lg font-bold text-sm hover:opacity-90 transition-opacity">
            Install
          </button>
          <button id="dismissInstallBtn" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-300 transition-colors">
            <span class="material-icons text-sm">close</span>
          </button>
        </div>
      </div>
    </div>

    <script>
      // Utilities
      const formatRs = (n) => `Rs. ${Number(n || 0).toFixed(2)}`;
      const toISODate = (d) => {
        const t = new Date(d);
        t.setHours(0,0,0,0);
        return t.toISOString().slice(0,10);
      };

      let selectedCustomer = null; // { code,name,nic,mobile,active_installments, dueToday }

      // Normalize customer data structure
      function normalizeCustomer(customer, searchTerm = '') {
        return {
          id: customer.id,
          code: customer.customer_code || customer.id || customer.code || searchTerm,
          name: customer.full_name || [customer.first_name, customer.last_name].filter(Boolean).join(' ') || customer.name || '-',
          nic: customer.nic || '-',
          mobile: customer.mobile || customer.phone || '-',
          email: customer.email || '',
          active_installments: customer.active_installments || customer.installments || []
        };
      }

      // Search customer by code or name using available admin APIs
      async function fetchCustomerByQuery(query) {
        try {
          // First, try to search using get-customers.php (searches by code, name, mobile, email)
          const searchUrl = `admin/api/get-customers.php?search=${encodeURIComponent(query)}`;
          const searchRes = await fetch(searchUrl, { 
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json'
            }
          });
          
          if (searchRes.ok) {
            const searchData = await searchRes.json();
            
            if (searchData?.success && Array.isArray(searchData.customers) && searchData.customers.length > 0) {
              // Found customer(s) - get the first one's full details
              const foundCustomer = searchData.customers[0];
              const customerId = foundCustomer.id;
              
              // Now get full details with installments
              const detailUrl = `admin/api/get-customer-detail.php?id=${customerId}`;
              const detailRes = await fetch(detailUrl, { 
                credentials: 'same-origin',
                headers: {
                  'Accept': 'application/json'
                }
              });
              
              if (detailRes.ok) {
                const detailData = await detailRes.json();
                
                if (detailData?.success && detailData?.customer) {
                  // Attach active installments
                  if (Array.isArray(detailData.active_installments)) {
                    detailData.customer.active_installments = detailData.active_installments;
                  }
                  return detailData.customer;
                }
              } else {
                const errorText = await detailRes.text();
                console.error('[Search] Detail fetch failed:', detailRes.status, errorText);
              }
              
              // If detail fetch fails, return the basic customer info from search
              return foundCustomer;
            }
          } else {
            const errorText = await searchRes.text();
            console.error('[Search] Search failed:', searchRes.status, errorText);
          }
          
          // Fallback: Try direct ID lookup if query is numeric
          const numericId = parseInt(query);
          if (!isNaN(numericId) && numericId > 0) {
            const detailUrl = `admin/api/get-customer-detail.php?id=${numericId}`;
            const detailRes = await fetch(detailUrl, { 
              credentials: 'same-origin',
              headers: {
                'Accept': 'application/json'
              }
            });
            
            if (detailRes.ok) {
              const detailData = await detailRes.json();
              if (detailData?.success && detailData?.customer) {
                if (Array.isArray(detailData.active_installments)) {
                  detailData.customer.active_installments = detailData.active_installments;
                }
                return detailData.customer;
              }
            }
          }
          
          return null;
        } catch (error) {
          console.error('[Search] Error fetching customer:', error);
          return null;
        }
      }

      // Compute today's due across all active installments
      async function computeTodaysDue(active_installments) {
        if (!Array.isArray(active_installments) || active_installments.length === 0) {
          return { totalDue: 0, schedulesToday: [] };
        }
        const todayISO = toISODate(new Date());
        const requests = active_installments.map(i => fetch(`admin/api/get-installment-schedule.php?order_id=${i.id}`)
          .then(r => r.ok ? r.json() : null)
          .catch(() => null));
        const responses = await Promise.all(requests);
        let totalDue = 0;
        const schedulesToday = [];
        for (const data of responses) {
          if (!data?.success || !Array.isArray(data.schedules)) continue;
          for (const s of data.schedules) {
            const sISO = toISODate(s.schedule_date);
            if (sISO !== todayISO) continue;
            // Count only unpaid/partial
            const due = Number(s.due_amount || 0);
            const paid = Number(s.paid_amount || 0);
            const remaining = Math.max(0, due - paid);
            if (s.status === 'paid' || remaining <= 0) continue;
            totalDue += remaining;
            schedulesToday.push({ 
              order_id: data.order?.id || null, 
              remaining,
              schedule_id: s.id,
              due_amount: due,
              paid_amount: paid
            });
          }
        }
        return { totalDue, schedulesToday };
      }

      // Compute missed payments across all active installments
      async function computeMissedPayments(active_installments) {
        if (!Array.isArray(active_installments) || active_installments.length === 0) {
          return { missedPayments: [], totalMissed: 0 };
        }
        const todayISO = toISODate(new Date());
        const requests = active_installments.map(i => fetch(`admin/api/get-installment-schedule.php?order_id=${i.id}`)
          .then(r => r.ok ? r.json() : null)
          .catch(() => null));
        const responses = await Promise.all(requests);
        const missedPayments = [];
        let totalMissed = 0;
        
        for (const data of responses) {
          if (!data?.success || !Array.isArray(data.schedules)) continue;
          const orderNumber = data.order?.order_number || 'N/A';
          
          for (const s of data.schedules) {
            const sISO = toISODate(s.schedule_date);
            const scheduleDate = new Date(s.schedule_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            scheduleDate.setHours(0, 0, 0, 0);
            
            // Check if payment is missed (status is 'missed' or pending with date in past)
            const isMissed = s.status === 'missed' || (s.status === 'pending' && scheduleDate < today);
            
            if (isMissed) {
              const due = Number(s.due_amount || 0);
              const paid = Number(s.paid_amount || 0);
              const remaining = Math.max(0, due - paid);
              
              if (remaining > 0) {
                const daysOverdue = Math.floor((today - scheduleDate) / (1000 * 60 * 60 * 24));
                missedPayments.push({
                  order_number: orderNumber,
                  order_id: data.order?.id || null,
                  schedule_date: s.schedule_date,
                  due_amount: due,
                  paid_amount: paid,
                  remaining: remaining,
                  days_overdue: daysOverdue,
                  status: s.status
                });
                totalMissed += remaining;
              }
            }
          }
        }
        
        // Sort by days overdue (most overdue first)
        missedPayments.sort((a, b) => b.days_overdue - a.days_overdue);
        
        return { missedPayments, totalMissed };
      }

      // Customer search functionality
      document.getElementById('searchCustomerBtn').addEventListener('click', async function() {
        const searchTerm = document.getElementById('customerSearch').value.trim();
        if (!searchTerm) {
          alert('Please enter a customer ID or name');
          return;
        }

        // Show loading state
        const searchBtn = document.getElementById('searchCustomerBtn');
        const originalText = searchBtn.textContent;
        searchBtn.disabled = true;
        searchBtn.textContent = 'Searching...';

        try {
          // 1) Find customer
          const customer = await fetchCustomerByQuery(searchTerm);
          if (!customer) {
            // Check console for detailed error messages
            const errorMsg = 'Customer not found. Please:\n' +
                           '1. Check the customer ID or name is correct\n' +
                           '2. Open browser console (F12) for detailed error messages\n' +
                           '3. Ensure you are logged in';
            alert(errorMsg);
            return;
          }

          // Normalize fields
          const norm = normalizeCustomer(customer, searchTerm);

          // 2) Compute today's due from schedules
          const { totalDue, schedulesToday } = await computeTodaysDue(norm.active_installments);
          norm.dueToday = totalDue;
          norm.schedulesToday = schedulesToday;

          // 3) Check for missed payments
          const { missedPayments, totalMissed } = await computeMissedPayments(norm.active_installments);
          norm.missedPayments = missedPayments;
          norm.totalMissed = totalMissed;

          selectCustomer(norm);
        } catch (err) {
          console.error('Search error:', err);
          alert('Failed to search. Please try again. Error: ' + (err.message || 'Unknown error'));
        } finally {
          // Restore button state
          searchBtn.disabled = false;
          searchBtn.textContent = originalText;
        }
      });

      function selectCustomer(customer) {
        selectedCustomer = customer;
        
        // Show customer details
        document.getElementById('customerName').textContent = customer.name;
        document.getElementById('customerNIC').textContent = customer.nic || '-';
        document.getElementById('customerMobile').textContent = customer.mobile || '-';
        document.getElementById('customerDetails').classList.remove('hidden');
        
        // Show today's due amount
        document.getElementById('dueAmountValue').textContent = formatRs(customer.dueToday || 0);
        document.getElementById('dueAmount').classList.remove('hidden');
        
        // Update popup data
        document.getElementById('popupRemainingAmount').textContent = (customer.dueToday || 0).toFixed(2);
        document.getElementById('popupDaysLeft').textContent = 'Today';
        document.getElementById('popupPaidAmount').textContent = '-';
        
        // Show missed payments alert if any
        if (customer.missedPayments && customer.missedPayments.length > 0) {
          const missedAlert = document.getElementById('missedPaymentsAlert');
          const missedCount = document.getElementById('missedCount');
          const missedTotal = document.getElementById('missedTotal');
          
          missedCount.textContent = customer.missedPayments.length;
          missedTotal.textContent = formatRs(customer.totalMissed);
          missedAlert.classList.remove('hidden');
        } else {
          document.getElementById('missedPaymentsAlert').classList.add('hidden');
        }
      }

      // Pay button functionality
      document.getElementById('payBtn').addEventListener('click', function() {
        const paymentInput = document.getElementById('paymentAmount');
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('hidden');
        
        // Set max attribute to today's due
        const maxDue = Math.max(0, Number(selectedCustomer?.dueToday || 0));
        paymentInput.setAttribute('max', maxDue.toFixed(2));
        paymentInput.removeAttribute('data-selected-missed');
        paymentInput.removeAttribute('data-missed-total');
        paymentInput.removeAttribute('data-today-due');
        
        // Set value and focus with selection
        paymentInput.value = maxDue.toFixed(2);
        
        // Use setTimeout to ensure modal is visible before focusing
        setTimeout(() => {
          paymentInput.focus();
          paymentInput.select();
        }, 100);
      });

      // Cancel payment
      document.getElementById('cancelPaymentBtn').addEventListener('click', function() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentAmount').value = '';
      });

      // Confirm payment
      document.getElementById('confirmPaymentBtn').addEventListener('click', async function() {
        const paymentInput = document.getElementById('paymentAmount');
        const confirmBtn = document.getElementById('confirmPaymentBtn');
        const paymentAmount = parseFloat(paymentInput.value);
        
        // Validate payment amount
        if (isNaN(paymentAmount) || paymentAmount <= 0) {
          alert('Please enter a valid payment amount greater than 0');
          paymentInput.focus();
          paymentInput.select();
          return;
        }
        
        // Check if missed payments are included
        const missedTotal = parseFloat(paymentInput.getAttribute('data-missed-total') || 0);
        const todayDue = parseFloat(paymentInput.getAttribute('data-today-due') || selectedCustomer?.dueToday || 0);
        const minDue = todayDue + missedTotal;
        
        // Validate minimum payment (must cover selected missed payments if any)
        if (missedTotal > 0 && paymentAmount < missedTotal) {
          alert(`Payment amount must be at least Rs. ${missedTotal.toFixed(2)} to cover selected missed payments`);
          paymentInput.focus();
          paymentInput.select();
          return;
        }
        
        // Allow advance payments - if payment is more than due, it will be applied to future schedules
        if (paymentAmount > minDue) {
          const advanceAmount = paymentAmount - minDue;
          if (!confirm(`You are paying Rs. ${advanceAmount.toFixed(2)} in advance. This amount will be applied to reduce future payment schedules. Continue?`)) {
            paymentInput.focus();
            paymentInput.select();
            return;
          }
        }

        // Disable button and show processing state
        confirmBtn.disabled = true;
        const originalBtnText = confirmBtn.textContent;
        confirmBtn.textContent = 'Processing...';

        try {
          // Get selected missed payments from payment input data attribute
          const selectedMissedJson = paymentInput.getAttribute('data-selected-missed');
          const selectedMissedPayments = selectedMissedJson ? JSON.parse(selectedMissedJson) : [];
          const missedTotal = selectedMissedPayments.reduce((sum, p) => sum + (p.remaining || 0), 0);
          const todayDue = parseFloat(paymentInput.getAttribute('data-today-due') || selectedCustomer?.dueToday || 0);
          
          let remainingPayment = paymentAmount;
          const paymentResults = [];
          
          // First, process missed payments if any selected
          if (selectedMissedPayments.length > 0 && missedTotal > 0) {
            // Group missed payments by order_id
            const missedOrderGroups = {};
            selectedMissedPayments.forEach(missed => {
              if (!missedOrderGroups[missed.order_id]) {
                missedOrderGroups[missed.order_id] = [];
              }
              missedOrderGroups[missed.order_id].push(missed);
            });

            // Process missed payments for each order
            for (const orderId in missedOrderGroups) {
              if (remainingPayment <= 0) break;
              
              // Validate order_id
              const parsedOrderId = parseInt(orderId);
              if (isNaN(parsedOrderId) || parsedOrderId <= 0) {
                console.error(`Invalid order_id for missed payment: ${orderId}`);
                continue;
              }
              
              const missedForOrder = missedOrderGroups[orderId];
              const orderMissedTotal = missedForOrder.reduce((sum, m) => sum + (m.remaining || 0), 0);
              const paymentForMissed = Math.min(remainingPayment, orderMissedTotal);
              
              if (paymentForMissed <= 0) continue;

              try {
                const response = await fetch('admin/api/process-installment-payment.php', {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                  },
                  body: JSON.stringify({
                    order_id: parsedOrderId,
                    amount: paymentForMissed,
                    payment_method: 'cash',
                    payment_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                    notes: 'Payment collected via collection page - Missed payments'
                  })
                });

                if (!response.ok) {
                  const errorText = await response.text();
                  let errorData;
                  try {
                    errorData = JSON.parse(errorText);
                  } catch (e) {
                    errorData = { error: errorText || `HTTP error! status: ${response.status}` };
                  }
                  throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (!data.success) {
                  throw new Error(data.error || 'Payment processing failed');
                }

                paymentResults.push({
                  order_id: orderId,
                  amount: paymentForMissed,
                  success: true,
                  data: data,
                  type: 'missed'
                });

                remainingPayment -= paymentForMissed;
              } catch (error) {
                console.error(`Error processing missed payment for order ${orderId}:`, error);
                paymentResults.push({
                  order_id: orderId,
                  amount: paymentForMissed,
                  success: false,
                  error: error.message,
                  type: 'missed'
                });
              }
            }
          }

          // Then, process today's due if payment remains
          // IMPORTANT: Send full remaining payment amount to allow advance payment processing
          if (remainingPayment > 0) {
            const schedulesToday = selectedCustomer.schedulesToday || [];
            
            if (schedulesToday.length > 0) {
              // Sort schedules by order_id and remaining amount
              const orderGroups = {};
              schedulesToday.forEach(schedule => {
                // Skip if order_id is null or invalid
                if (!schedule.order_id || schedule.order_id === 'null' || schedule.order_id === null) {
                  console.warn('Skipping schedule with invalid order_id:', schedule);
                  return;
                }
                
                const orderId = schedule.order_id.toString();
                if (!orderGroups[orderId]) {
                  orderGroups[orderId] = [];
                }
                orderGroups[orderId].push(schedule);
              });

              // Process payments for each order
              // For advance payments, send the full remaining amount to the first order
              // The API will handle distributing it across schedules and future payments
              let isFirstOrder = true;
              for (const orderId in orderGroups) {
                if (remainingPayment <= 0) break;
                
                // Validate order_id
                const parsedOrderId = parseInt(orderId);
                if (isNaN(parsedOrderId) || parsedOrderId <= 0) {
                  console.error(`Invalid order_id: ${orderId}`);
                  continue;
                }
                
                const orderSchedules = orderGroups[orderId];
                const orderDue = orderSchedules.reduce((sum, s) => sum + (s.remaining || 0), 0);
                
                // For the first order, send full remaining payment (allows advance payment)
                // For subsequent orders, only send their due amount
                const paymentForThisOrder = isFirstOrder ? remainingPayment : Math.min(remainingPayment, orderDue);
                
                if (paymentForThisOrder <= 0) continue;

                try {
                  const response = await fetch('admin/api/process-installment-payment.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                      'Content-Type': 'application/json',
                      'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                      order_id: parsedOrderId,
                      amount: paymentForThisOrder,
                      payment_method: 'cash',
                      payment_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                      notes: 'Payment collected via collection page' + (paymentForThisOrder > orderDue ? ' (includes advance payment)' : '')
                    })
                  });

                  if (!response.ok) {
                    const errorText = await response.text();
                    let errorData;
                    try {
                      errorData = JSON.parse(errorText);
                    } catch (e) {
                      errorData = { error: errorText || `HTTP error! status: ${response.status}` };
                    }
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                  }

                  const data = await response.json();
                  
                  if (!data.success) {
                    throw new Error(data.error || 'Payment processing failed');
                  }

                  paymentResults.push({
                    order_id: orderId,
                    amount: paymentForThisOrder,
                    success: true,
                    data: data,
                    type: 'today'
                  });

                  // For first order with advance, subtract only the order's due amount
                  // The advance portion is handled by the API
                  if (isFirstOrder && paymentForThisOrder > orderDue) {
                    remainingPayment -= orderDue; // Only subtract the due amount
                  } else {
                    remainingPayment -= paymentForThisOrder;
                  }
                  
                  isFirstOrder = false; // Mark that we've processed the first order
                } catch (error) {
                  console.error(`Error processing payment for order ${orderId}:`, error);
                  paymentResults.push({
                    order_id: orderId,
                    amount: paymentForThisOrder,
                    success: false,
                    error: error.message,
                    type: 'today'
                  });
                  // On error, still mark first order as processed to avoid infinite loop
                  isFirstOrder = false;
                }
              }
            } else if (remainingPayment > 0 && selectedMissedPayments.length === 0) {
              // No schedules today and no missed payments selected, but payment amount was entered
              // This could be a pure advance payment - need to find an order to apply it to
              if (selectedCustomer.active_installments && selectedCustomer.active_installments.length > 0) {
                // Apply advance payment to the first active installment
                const firstInstallment = selectedCustomer.active_installments[0];
                const parsedOrderId = parseInt(firstInstallment.id || firstInstallment.order_id);
                
                if (!isNaN(parsedOrderId) && parsedOrderId > 0) {
                  try {
                    const response = await fetch('admin/api/process-installment-payment.php', {
                      method: 'POST',
                      credentials: 'same-origin',
                      headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                      },
                      body: JSON.stringify({
                        order_id: parsedOrderId,
                        amount: remainingPayment,
                        payment_method: 'cash',
                        payment_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                        notes: 'Advance payment collected via collection page'
                      })
                    });

                    if (response.ok) {
                      const data = await response.json();
                      if (data.success) {
                        paymentResults.push({
                          order_id: parsedOrderId,
                          amount: remainingPayment,
                          success: true,
                          data: data,
                          type: 'advance'
                        });
                        remainingPayment = 0;
                      }
                    }
                  } catch (error) {
                    console.error('Error processing advance payment:', error);
                  }
                }
              } else {
                console.warn('Payment amount entered but no schedules found for today and no active installments');
                throw new Error('No payment due today and no active installments found. Please check if the customer has active orders.');
              }
            }
          }
          
          // Validate that we processed something
          if (paymentResults.length === 0) {
            // Check if we have any schedules to process
            const hasSchedulesToday = selectedCustomer.schedulesToday && selectedCustomer.schedulesToday.length > 0;
            const hasSelectedMissed = selectedMissedPayments.length > 0;
            
            if (!hasSchedulesToday && !hasSelectedMissed) {
              throw new Error('No payment schedules found. Please check if the customer has active installments with due payments.');
            }
            
            throw new Error('No payment schedules found to process. Please try again.');
          }

          // Check if any payments succeeded
          const successfulPayments = paymentResults.filter(r => r.success);
          const failedPayments = paymentResults.filter(r => !r.success);
          
          // Check if failed payments are due to trigger errors (updated_at issue)
          const triggerErrors = failedPayments.filter(r => 
            r.error && (
              r.error.includes('updated_at') || 
              r.error.includes('trigger') || 
              r.error.includes('1054') ||
              r.error.includes('42S22')
            )
          );
          
          // If all failures are trigger errors, check if payment was actually processed
          if (successfulPayments.length === 0 && failedPayments.length > 0) {
            // Check if it's just trigger errors - payment might have been processed anyway
            if (triggerErrors.length === failedPayments.length) {
              // All errors are trigger-related - check if payment was actually processed
              // by reloading customer data and checking if due amount decreased
              try {
                const checkCustomer = await fetchCustomerByQuery(selectedCustomer.code || selectedCustomer.id);
                if (checkCustomer) {
                  const checkNorm = normalizeCustomer(checkCustomer, selectedCustomer.code);
                  
                  const { totalDue: checkDue } = await computeTodaysDue(checkNorm.active_installments);
                  const originalDue = selectedCustomer.dueToday || 0;
                  
                  // If due amount decreased, payment was processed successfully
                  if (checkDue < originalDue) {
                    // Payment was processed! Continue with success flow
                    selectedCustomer = checkNorm;
                    selectedCustomer.dueToday = checkDue;
                    const actualProcessed = originalDue - checkDue;
                    
                    // Reload full customer data
                    const customer = await fetchCustomerByQuery(selectedCustomer.code || selectedCustomer.id);
                    if (customer) {
                      const norm = normalizeCustomer(customer, selectedCustomer.code);
                      
                      const { totalDue, schedulesToday: newSchedulesToday } = await computeTodaysDue(norm.active_installments);
                      const { missedPayments, totalMissed } = await computeMissedPayments(norm.active_installments);
                      
                      norm.dueToday = totalDue;
                      norm.schedulesToday = newSchedulesToday;
                      norm.missedPayments = missedPayments;
                      norm.totalMissed = totalMissed;
                      
                      selectedCustomer = norm;
                      
                      // Update display
                      document.getElementById('dueAmountValue').textContent = formatRs(norm.dueToday || 0);
                      document.getElementById('popupRemainingAmount').textContent = (norm.dueToday || 0).toFixed(2);
                      
                      // Update missed payments alert if needed
                      if (norm.missedPayments && norm.missedPayments.length > 0) {
                        document.getElementById('missedCount').textContent = norm.missedPayments.length;
                        document.getElementById('missedTotal').textContent = formatRs(norm.totalMissed);
                        document.getElementById('missedPaymentsAlert').classList.remove('hidden');
                      } else {
                        document.getElementById('missedPaymentsAlert').classList.add('hidden');
                      }
                      
                      // Show success - payment was processed despite trigger error
                      document.getElementById('successMessage').textContent = `Payment of Rs. ${actualProcessed.toFixed(2)} processed successfully!`;
                      document.getElementById('successModal').classList.remove('hidden');
                      document.getElementById('paymentModal').classList.add('hidden');
                      paymentInput.value = '';
                      
                      // Restore button state
                      confirmBtn.disabled = false;
                      confirmBtn.textContent = originalBtnText;
                      return; // Exit early - payment was successful
                    }
                  }
                }
              } catch (checkError) {
                console.error('Error checking payment status:', checkError);
                // Continue to show error
              }
            }
            
            // Get detailed error messages
            const errorMessages = failedPayments.map(r => `Order ${r.order_id}: ${r.error || 'Unknown error'}`).join('\n');
            throw new Error(`Payment processing failed for all orders:\n${errorMessages}\n\nPlease check the console for more details.`);
          }
          
          // If some payments failed, show warning but continue
          if (failedPayments.length > 0) {
            const failedTotal = failedPayments.reduce((sum, r) => sum + r.amount, 0);
            console.warn(`Some payments failed:`, failedPayments);
            // Don't throw error, just log - we'll process successful ones
          }

          const totalProcessed = successfulPayments.reduce((sum, r) => sum + r.amount, 0);
          
          // Reload customer data to get updated due amounts
          const customer = await fetchCustomerByQuery(selectedCustomer.code || selectedCustomer.id);
          if (customer) {
            const norm = normalizeCustomer(customer, selectedCustomer.code);

            // Recalculate today's due and missed payments
            const { totalDue, schedulesToday: newSchedulesToday } = await computeTodaysDue(norm.active_installments);
            const { missedPayments, totalMissed } = await computeMissedPayments(norm.active_installments);
            
            norm.dueToday = totalDue;
            norm.schedulesToday = newSchedulesToday;
            norm.missedPayments = missedPayments;
            norm.totalMissed = totalMissed;

            // Update selected customer
            selectedCustomer = norm;
            
            // Update display
            document.getElementById('dueAmountValue').textContent = formatRs(norm.dueToday || 0);
            document.getElementById('popupRemainingAmount').textContent = (norm.dueToday || 0).toFixed(2);
            
            // Update missed payments alert if needed
            if (norm.missedPayments && norm.missedPayments.length > 0) {
              document.getElementById('missedCount').textContent = norm.missedPayments.length;
              document.getElementById('missedTotal').textContent = formatRs(norm.totalMissed);
              document.getElementById('missedPaymentsAlert').classList.remove('hidden');
            } else {
              document.getElementById('missedPaymentsAlert').classList.add('hidden');
            }
          }
          
          // Show success modal with advance payment info if applicable
          const advanceAmount = paymentAmount - (todayDue + missedTotal);
          let successMessage = `Payment of Rs. ${totalProcessed.toFixed(2)} processed successfully!`;
          if (advanceAmount > 0) {
            successMessage += `\n\nAdvance payment of Rs. ${advanceAmount.toFixed(2)} has been applied to reduce future payment schedules.`;
          }
          document.getElementById('successMessage').textContent = successMessage;
          document.getElementById('successModal').classList.remove('hidden');
          document.getElementById('paymentModal').classList.add('hidden');
          
          // Clear payment input
          paymentInput.value = '';
          
          if (selectedCustomer.dueToday <= 0) {
            setTimeout(() => {
              alert("Today's due fully paid!");
            }, 1000);
          }

        } catch (error) {
          console.error('Payment processing error:', error);
          
          // Check if error is trigger-related and payment might have been processed anyway
          const isTriggerError = error.message && (
            error.message.includes('updated_at') || 
            error.message.includes('trigger') || 
            error.message.includes('1054') ||
            error.message.includes('42S22')
          );
          
          if (isTriggerError) {
            // Check if payment was actually processed by reloading customer data
            try {
              const checkCustomer = await fetchCustomerByQuery(selectedCustomer.code || selectedCustomer.id);
              if (checkCustomer) {
                const checkNorm = normalizeCustomer(checkCustomer, selectedCustomer.code);
                
                const { totalDue: checkDue } = await computeTodaysDue(checkNorm.active_installments);
                const originalDue = selectedCustomer.dueToday || 0;
                
                // If due amount decreased, payment was processed successfully
                if (checkDue < originalDue) {
                  // Payment was processed! Show success instead of error
                  const actualProcessed = originalDue - checkDue;
                  
                  // Reload full customer data
                  const customer = await fetchCustomerByQuery(selectedCustomer.code || selectedCustomer.id);
                  if (customer) {
                    const norm = normalizeCustomer(customer, selectedCustomer.code);
                    
                    const { totalDue, schedulesToday: newSchedulesToday } = await computeTodaysDue(norm.active_installments);
                    const { missedPayments, totalMissed } = await computeMissedPayments(norm.active_installments);
                    
                    norm.dueToday = totalDue;
                    norm.schedulesToday = newSchedulesToday;
                    norm.missedPayments = missedPayments;
                    norm.totalMissed = totalMissed;
                    
                    selectedCustomer = norm;
                    
                    // Update display
                    document.getElementById('dueAmountValue').textContent = formatRs(norm.dueToday || 0);
                    document.getElementById('popupRemainingAmount').textContent = (norm.dueToday || 0).toFixed(2);
                    
                    // Update missed payments alert if needed
                    if (norm.missedPayments && norm.missedPayments.length > 0) {
                      document.getElementById('missedCount').textContent = norm.missedPayments.length;
                      document.getElementById('missedTotal').textContent = formatRs(norm.totalMissed);
                      document.getElementById('missedPaymentsAlert').classList.remove('hidden');
                    } else {
                      document.getElementById('missedPaymentsAlert').classList.add('hidden');
                    }
                    
                    // Show success - payment was processed despite trigger error
                    document.getElementById('successMessage').textContent = `Payment of Rs. ${actualProcessed.toFixed(2)} processed successfully!`;
                    document.getElementById('successModal').classList.remove('hidden');
                    document.getElementById('paymentModal').classList.add('hidden');
                    paymentInput.value = '';
                    
                  }
                  return; // Exit early - payment was successful
                }
              }
            } catch (checkError) {
              console.error('Error checking payment status:', checkError);
              // Continue to show error if check fails
            }
          }
          
          // Only show error if payment was not actually processed
          alert('Payment failed: ' + error.message + '\n\nPlease try again.');
        } finally {
          // Restore button state
          confirmBtn.disabled = false;
          confirmBtn.textContent = originalBtnText;
        }
      });

      // Info button functionality
      document.getElementById('infoBtn').addEventListener('click', function() {
        document.getElementById('balancePopup').classList.remove('hidden');
      });

      // Close balance popup
      document.getElementById('closeBalancePopup').addEventListener('click', function() {
        document.getElementById('balancePopup').classList.add('hidden');
      });

      // Show missed payments details modal
      function showMissedPaymentsDetails() {
        if (!selectedCustomer || !selectedCustomer.missedPayments || selectedCustomer.missedPayments.length === 0) {
          alert('No missed payments found');
          return;
        }

        const modal = document.getElementById('missedPaymentsModal');
        const missedList = document.getElementById('missedPaymentsList');
        const missedTotal = document.getElementById('missedModalTotal');
        const missedCount = document.getElementById('missedModalCount');

        missedTotal.textContent = formatRs(selectedCustomer.totalMissed);
        missedCount.textContent = selectedCustomer.missedPayments.length;

        // Display missed payments list with checkboxes
        missedList.innerHTML = selectedCustomer.missedPayments.map((payment, index) => {
          const scheduleDate = new Date(payment.schedule_date);
          return `
            <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-lg mb-3 hover:bg-red-100 transition-colors">
              <div class="flex items-start gap-3">
                <input 
                  type="checkbox" 
                  class="missed-payment-checkbox w-5 h-5 mt-1 cursor-pointer" 
                  data-index="${index}"
                  data-amount="${payment.remaining}"
                  data-order-id="${payment.order_id}"
                  data-schedule-date="${payment.schedule_date}"
                  id="missedCheckbox${index}"
                >
                <label for="missedCheckbox${index}" class="flex-1 cursor-pointer">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-2">
                        <span class="material-icons text-red-600">error</span>
                        <h4 class="font-semibold text-gray-800">Order: ${payment.order_number}</h4>
                        <span class="px-2 py-1 bg-red-600 text-white rounded-full text-xs font-medium">
                          ${payment.days_overdue} Day${payment.days_overdue !== 1 ? 's' : ''} Overdue
                        </span>
                      </div>
                      <p class="text-gray-600 text-sm mb-1">
                        <strong>Due Date:</strong> ${scheduleDate.toLocaleDateString('en-GB', { 
                          day: '2-digit', 
                          month: '2-digit', 
                          year: 'numeric' 
                        })}
                      </p>
                      <p class="text-gray-600 text-sm mb-1">
                        <strong>Due Amount:</strong> <span class="font-bold text-red-600">${formatRs(payment.due_amount)}</span>
                      </p>
                      <p class="text-gray-600 text-sm">
                        <strong>Paid:</strong> ${formatRs(payment.paid_amount)} | 
                        <strong>Remaining:</strong> <span class="font-bold text-red-600">${formatRs(payment.remaining)}</span>
                      </p>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          `;
        }).join('');

        // Update selected total when checkboxes change
        updateMissedSelectedTotal();
        
        // Add event listeners to checkboxes
        document.querySelectorAll('.missed-payment-checkbox').forEach(checkbox => {
          checkbox.addEventListener('change', updateMissedSelectedTotal);
        });

        modal.classList.remove('hidden');
      }

      // Update selected missed payments total
      function updateMissedSelectedTotal() {
        const checkboxes = document.querySelectorAll('.missed-payment-checkbox:checked');
        let total = 0;
        checkboxes.forEach(checkbox => {
          total += parseFloat(checkbox.getAttribute('data-amount') || 0);
        });
        document.getElementById('missedSelectedTotal').textContent = formatRs(total);
        document.getElementById('paySelectedAmount').textContent = total.toFixed(2);
      }

      // Get selected missed payments
      function getSelectedMissedPayments() {
        const checkboxes = document.querySelectorAll('.missed-payment-checkbox:checked');
        const selected = [];
        checkboxes.forEach(checkbox => {
          const index = parseInt(checkbox.getAttribute('data-index'));
          if (selectedCustomer.missedPayments[index]) {
            selected.push({
              ...selectedCustomer.missedPayments[index],
              remaining: parseFloat(checkbox.getAttribute('data-amount') || 0)
            });
          }
        });
        return selected;
      }

      // Missed payments modal handlers
      const viewMissedDetailsBtn = document.getElementById('viewMissedDetailsBtn');
      const closeMissedAlert = document.getElementById('closeMissedAlert');
      const missedPaymentsModal = document.getElementById('missedPaymentsModal');
      const closeMissedModal = document.getElementById('closeMissedModal');
      const selectAllMissedBtn = document.getElementById('selectAllMissedBtn');
      const deselectAllMissedBtn = document.getElementById('deselectAllMissedBtn');
      const paySelectedMissedBtn = document.getElementById('paySelectedMissedBtn');

      if (viewMissedDetailsBtn) {
        viewMissedDetailsBtn.addEventListener('click', showMissedPaymentsDetails);
      }

      if (closeMissedAlert) {
        closeMissedAlert.addEventListener('click', function() {
          document.getElementById('missedPaymentsAlert').classList.add('hidden');
        });
      }

      if (closeMissedModal) {
        closeMissedModal.addEventListener('click', function() {
          missedPaymentsModal.classList.add('hidden');
        });
      }

      if (missedPaymentsModal) {
        missedPaymentsModal.addEventListener('click', function(e) {
          if (e.target.id === 'missedPaymentsModal') {
            missedPaymentsModal.classList.add('hidden');
          }
        });
      }

      if (selectAllMissedBtn) {
        selectAllMissedBtn.addEventListener('click', function() {
          document.querySelectorAll('.missed-payment-checkbox').forEach(checkbox => {
            checkbox.checked = true;
          });
          updateMissedSelectedTotal();
        });
      }

      if (deselectAllMissedBtn) {
        deselectAllMissedBtn.addEventListener('click', function() {
          document.querySelectorAll('.missed-payment-checkbox').forEach(checkbox => {
            checkbox.checked = false;
          });
          updateMissedSelectedTotal();
        });
      }

      if (paySelectedMissedBtn) {
        paySelectedMissedBtn.addEventListener('click', async function() {
          const selected = getSelectedMissedPayments();
          if (selected.length === 0) {
            alert('Please select at least one missed payment to pay');
            return;
          }

          const totalSelected = selected.reduce((sum, p) => sum + p.remaining, 0);
          
          // Close missed payments modal
          missedPaymentsModal.classList.add('hidden');
          
          // Open payment modal with pre-filled amount including missed payments
          const paymentModal = document.getElementById('paymentModal');
          const paymentInput = document.getElementById('paymentAmount');
          
          // Calculate total: today's due + selected missed payments
          const todayDue = Math.max(0, Number(selectedCustomer?.dueToday || 0));
          const totalAmount = todayDue + totalSelected;
          
          paymentInput.value = totalAmount.toFixed(2);
          paymentInput.setAttribute('data-missed-total', totalSelected.toFixed(2));
          paymentInput.setAttribute('data-today-due', todayDue.toFixed(2));
          
          // Store selected missed payments for payment processing
          paymentInput.setAttribute('data-selected-missed', JSON.stringify(selected));
          
          paymentModal.classList.remove('hidden');
          
          setTimeout(() => {
            paymentInput.focus();
            paymentInput.select();
          }, 100);
        });
      }

      // Load payment history for all installments
      async function loadPaymentHistory() {
        if (!selectedCustomer || !selectedCustomer.id) {
          alert('Please search for a customer first');
          return;
        }

        const customerId = selectedCustomer.id;
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
          const response = await fetch(`admin/api/get-customer-detail.php?id=${customerId}`, {
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
              const scheduleRes = await fetch(`admin/api/get-installment-schedule.php?order_id=${installment.id}`, {
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
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                  <td class="py-3 px-4 text-gray-700">${scheduleDate.toLocaleDateString('en-GB', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric' 
                  })}</td>
                  <td class="py-3 px-4 text-gray-800 font-semibold">Rs. ${dueAmount.toFixed(2)}</td>
                  <td class="py-3 px-4 text-green-600 font-medium">Rs. ${paidAmount.toFixed(2)}</td>
                  <td class="py-3 px-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${statusText}</span>
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                    ${paymentDate ? paymentDate.toLocaleDateString('en-GB', { 
                      day: '2-digit', 
                      month: '2-digit', 
                      year: 'numeric' 
                    }) : '-'}
                  </td>
                  <td class="py-3 px-4 text-gray-600">
                    ${schedule.payment_method ? schedule.payment_method.charAt(0).toUpperCase() + schedule.payment_method.slice(1) : '-'}
                  </td>
                </tr>
              `;
            }).join('');

            return `
              <div class="mb-6 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                  <div>
                    <h4 class="text-lg font-semibold text-gray-800">Order: ${order.order_number || installment.order_number}</h4>
                    <p class="text-sm text-gray-600">Total Amount: Rs. ${parseFloat(installment.total_amount || 0).toFixed(2)} | Remaining: Rs. ${parseFloat(installment.remaining_balance || 0).toFixed(2)}</p>
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
                      <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Due Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Due Amount</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Paid Amount</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Payment Date</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-800">Method</th>
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
        paymentHistoryBtn.addEventListener('click', function() {
          if (!selectedCustomer) {
            alert('Please search for a customer first');
            return;
          }
          loadPaymentHistory();
        });
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

      // Close success modal
      document.getElementById('closeSuccessModal').addEventListener('click', function() {
        document.getElementById('successModal').classList.add('hidden');
      });

      // Keyboard support for payment modal
      document.getElementById('paymentAmount').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('confirmPaymentBtn').click();
        } else if (e.key === 'Escape') {
          e.preventDefault();
          document.getElementById('cancelPaymentBtn').click();
        }
      });

      // Close payment modal on Escape key
      document.addEventListener('keydown', function(e) {
        const paymentModal = document.getElementById('paymentModal');
        if (e.key === 'Escape' && !paymentModal.classList.contains('hidden')) {
          document.getElementById('cancelPaymentBtn').click();
        }
      });

      // Auto-focus search input
      document.getElementById('customerSearch').focus();

      // PWA - Service Worker Registration
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('./service-worker.js')
            .then((registration) => {
              console.log('[PWA] Service Worker registered:', registration.scope);
            })
            .catch((error) => {
              console.log('[PWA] Service Worker registration failed:', error);
            });
        });
      }

      // PWA - Install Prompt
      let deferredPrompt;
      const installPromptContainer = document.getElementById('installPrompt');

      window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        // Show our custom install prompt
        if (installPromptContainer) {
          installPromptContainer.classList.remove('hidden');
        }
      });

      // Handle install button click
      document.getElementById('installAppBtn')?.addEventListener('click', async () => {
        if (!deferredPrompt) {
          return;
        }

        // Show the install prompt
        deferredPrompt.prompt();

        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;

        console.log(`[PWA] User response to install prompt: ${outcome}`);

        // Hide our custom install prompt
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }

        // Clear the deferredPrompt variable
        deferredPrompt = null;
      });

      // Hide install prompt if user dismisses it
      document.getElementById('dismissInstallBtn')?.addEventListener('click', () => {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
        // Store dismissal in localStorage
        localStorage.setItem('pwa-install-dismissed', 'true');
      });

      // Check if user already dismissed the prompt
      if (localStorage.getItem('pwa-install-dismissed') === 'true') {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
      }

      // Hide prompt if app is already installed (running in standalone mode)
      if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
        if (installPromptContainer) {
          installPromptContainer.classList.add('hidden');
        }
      }
    </script>
  </body>
</html>
