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
    <title>Test: Revert Payment</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
      body { 
        font-family: 'Inter', sans-serif; 
        background: #f3f4f6;
        min-height: 100vh;
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <header class="bg-red-600 text-white p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">⚠️ Test: Revert Payment</h1>
        <div class="flex items-center gap-2">
          <a href="collection.php" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors">
            Back to Collection
          </a>
          <a href="admin/logout.php" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors" title="Logout">
            <span class="material-icons">logout</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto p-6">
      <!-- Warning Banner -->
      <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-6">
        <div class="flex items-center">
          <span class="material-icons text-yellow-600 mr-2">warning</span>
          <p class="text-yellow-800 font-medium">
            <strong>TESTING ONLY:</strong> This page allows you to revert payments and mark customers as unpaid. Use only for testing purposes.
          </p>
        </div>
      </div>

      <!-- Customer Search Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Search Customer</h2>
        <div class="flex gap-3">
          <input 
            type="text" 
            id="customerSearch" 
            placeholder="Enter Customer ID or Name (e.g., C001)" 
            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
          >
          <button 
            id="searchCustomerBtn" 
            class="px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors"
          >
            Search
          </button>
        </div>
      </div>

      <!-- Customer Details -->
      <div id="customerDetails" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Customer Information</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <p class="text-sm text-gray-600">Name</p>
            <p class="font-semibold text-gray-800" id="customerName">-</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Code</p>
            <p class="font-semibold text-gray-800" id="customerCode">-</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Purchased</p>
            <p class="font-semibold text-gray-800" id="totalPurchased">Rs. 0.00</p>
          </div>
          <div>
            <p class="text-sm text-gray-600">Total Paid</p>
            <p class="font-semibold text-gray-800" id="totalPaid">Rs. 0.00</p>
          </div>
        </div>
      </div>

      <!-- Payments List -->
      <div id="paymentsSection" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-800">Recent Payments</h2>
          <button 
            id="revertAllBtn" 
            class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors"
          >
            Revert All Payments
          </button>
        </div>
        <div id="paymentsList" class="space-y-3">
          <!-- Payments will be loaded here -->
        </div>
      </div>

      <!-- Installment Schedules -->
      <div id="schedulesSection" class="hidden bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Installment Schedules</h2>
        <div id="schedulesList" class="space-y-3">
          <!-- Schedules will be loaded here -->
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div id="messageBox" class="hidden fixed top-4 right-4 max-w-md z-50">
        <div id="messageContent" class="p-4 rounded-lg shadow-lg"></div>
      </div>
    </main>

    <script>
      let selectedCustomer = null;
      let customerPayments = [];
      let customerSchedules = [];

      // Format currency
      const formatRs = (n) => `Rs. ${Number(n || 0).toFixed(2)}`;

      // Show message
      function showMessage(message, type = 'success') {
        const messageBox = document.getElementById('messageBox');
        const messageContent = document.getElementById('messageContent');
        
        messageBox.classList.remove('hidden');
        messageContent.className = `p-4 rounded-lg shadow-lg ${
          type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' :
          type === 'error' ? 'bg-red-100 text-red-800 border border-red-300' :
          'bg-blue-100 text-blue-800 border border-blue-300'
        }`;
        messageContent.textContent = message;
        
        setTimeout(() => {
          messageBox.classList.add('hidden');
        }, 5000);
      }

      // Search customer
      document.getElementById('searchCustomerBtn').addEventListener('click', async function() {
        const searchTerm = document.getElementById('customerSearch').value.trim();
        if (!searchTerm) {
          showMessage('Please enter a customer ID or name', 'error');
          return;
        }

        const btn = document.getElementById('searchCustomerBtn');
        btn.disabled = true;
        btn.textContent = 'Searching...';

        try {
          // Search for customer
          const searchUrl = `admin/api/get-customers.php?search=${encodeURIComponent(searchTerm)}`;
          const searchRes = await fetch(searchUrl, { 
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          
          if (!searchRes.ok) {
            throw new Error('Customer not found');
          }

          const searchData = await searchRes.json();
          if (!searchData?.success || !Array.isArray(searchData.customers) || searchData.customers.length === 0) {
            throw new Error('Customer not found');
          }

          const customer = searchData.customers[0];
          
          // Get full customer details
          const detailUrl = `admin/api/get-customer-detail.php?id=${customer.id}`;
          const detailRes = await fetch(detailUrl, { 
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          
          if (!detailRes.ok) {
            throw new Error('Failed to get customer details');
          }

          const detailData = await detailRes.json();
          if (!detailData?.success || !detailData?.customer) {
            throw new Error('Failed to get customer details');
          }

          selectedCustomer = detailData.customer;
          
          // Display customer info
          document.getElementById('customerName').textContent = selectedCustomer.full_name || '-';
          document.getElementById('customerCode').textContent = selectedCustomer.customer_code || '-';
          document.getElementById('totalPurchased').textContent = formatRs(selectedCustomer.total_purchased || 0);
          document.getElementById('totalPaid').textContent = formatRs(selectedCustomer.total_paid || 0);
          document.getElementById('customerDetails').classList.remove('hidden');

          // Load payments
          await loadPayments(selectedCustomer.id);
          
          // Load schedules
          await loadSchedules(selectedCustomer.id);

        } catch (error) {
          console.error('Search error:', error);
          showMessage('Failed to find customer: ' + error.message, 'error');
        } finally {
          btn.disabled = false;
          btn.textContent = 'Search';
        }
      });

      // Load payments
      async function loadPayments(customerId) {
        try {
          const response = await fetch(`admin/api/get-payments.php?customer_id=${customerId}`, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });

          if (response.ok) {
            const data = await response.json();
            customerPayments = data.payments || [];
            displayPayments();
          } else {
            // If API doesn't exist, try alternative approach
            customerPayments = [];
            displayPayments();
          }
        } catch (error) {
          console.error('Error loading payments:', error);
          customerPayments = [];
          displayPayments();
        }
      }

      // Display payments
      function displayPayments() {
        const paymentsList = document.getElementById('paymentsList');
        const paymentsSection = document.getElementById('paymentsSection');

        if (customerPayments.length === 0) {
          paymentsList.innerHTML = '<p class="text-gray-600">No payments found</p>';
          paymentsSection.classList.remove('hidden');
          return;
        }

        paymentsList.innerHTML = customerPayments.map(payment => {
          const paymentDate = new Date(payment.payment_date);
          return `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="font-semibold text-gray-800">${formatRs(payment.amount)}</span>
                    <span class="text-sm text-gray-600">${paymentDate.toLocaleDateString('en-GB')}</span>
                    <span class="text-sm text-gray-600">Order: ${payment.order_id || 'N/A'}</span>
                  </div>
                  <p class="text-sm text-gray-600">Method: ${payment.payment_method || 'cash'}</p>
                </div>
                <button 
                  onclick="revertPayment(${payment.id}, ${payment.amount}, ${payment.order_id || 'null'})"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                >
                  Revert
                </button>
              </div>
            </div>
          `;
        }).join('');

        paymentsSection.classList.remove('hidden');
      }

      // Load schedules
      async function loadSchedules(customerId) {
        try {
          // Get customer installments
          const detailUrl = `admin/api/get-customer-detail.php?id=${customerId}`;
          const detailRes = await fetch(detailUrl, { 
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          
          if (!detailRes.ok) return;

          const detailData = await detailRes.json();
          if (!detailData?.success || !detailData?.active_installments) return;

          const installments = detailData.active_installments || [];
          customerSchedules = [];

          // Load schedules for each installment
          for (const installment of installments) {
            try {
              const scheduleRes = await fetch(`admin/api/get-installment-schedule.php?order_id=${installment.id}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
              });

              if (scheduleRes.ok) {
                const scheduleData = await scheduleRes.json();
                if (scheduleData?.success && scheduleData.schedules) {
                  customerSchedules.push(...scheduleData.schedules.map(s => ({
                    ...s,
                    order_id: installment.id,
                    order_number: installment.order_number
                  })));
                }
              }
            } catch (err) {
              console.error(`Error loading schedule for order ${installment.id}:`, err);
            }
          }

          displaySchedules();
        } catch (error) {
          console.error('Error loading schedules:', error);
        }
      }

      // Display schedules
      function displaySchedules() {
        const schedulesList = document.getElementById('schedulesList');
        const schedulesSection = document.getElementById('schedulesSection');

        if (customerSchedules.length === 0) {
          schedulesList.innerHTML = '<p class="text-gray-600">No schedules found</p>';
          schedulesSection.classList.remove('hidden');
          return;
        }

        // Filter to show only paid/partial schedules
        const paidSchedules = customerSchedules.filter(s => 
          s.status === 'paid' || s.status === 'partial' || (parseFloat(s.paid_amount || 0) > 0)
        );

        if (paidSchedules.length === 0) {
          schedulesList.innerHTML = '<p class="text-gray-600">No paid schedules found</p>';
          schedulesSection.classList.remove('hidden');
          return;
        }

        schedulesList.innerHTML = paidSchedules.map(schedule => {
          const scheduleDate = new Date(schedule.schedule_date);
          const statusClass = schedule.status === 'paid' ? 'bg-green-100 text-green-700' : 
                             schedule.status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 
                             'bg-gray-100 text-gray-700';
          
          return `
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="font-semibold text-gray-800">${scheduleDate.toLocaleDateString('en-GB')}</span>
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${schedule.status || 'pending'}</span>
                    <span class="text-sm text-gray-600">Order: ${schedule.order_number || schedule.order_id || 'N/A'}</span>
                  </div>
                  <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600">Due:</span>
                      <span class="font-semibold ml-1">${formatRs(schedule.due_amount)}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Paid:</span>
                      <span class="font-semibold ml-1 text-green-600">${formatRs(schedule.paid_amount)}</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Remaining:</span>
                      <span class="font-semibold ml-1 text-red-600">${formatRs((parseFloat(schedule.due_amount || 0) - parseFloat(schedule.paid_amount || 0)))}</span>
                    </div>
                  </div>
                </div>
                <button 
                  onclick="revertSchedule(${schedule.id}, ${schedule.order_id}, ${parseFloat(schedule.paid_amount || 0)})"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors ml-4"
                >
                  Revert
                </button>
              </div>
            </div>
          `;
        }).join('');

        schedulesSection.classList.remove('hidden');
      }

      // Revert payment
      async function revertPayment(paymentId, amount, orderId) {
        if (!confirm(`Are you sure you want to revert this payment of ${formatRs(amount)}? This will mark schedules as unpaid and update customer totals.`)) {
          return;
        }

        try {
          const response = await fetch('admin/api/revert-payment.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              payment_id: paymentId,
              order_id: orderId
            })
          });

          if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          console.log('Revert payment response:', data);
          
          if (data.success) {
            showMessage('Payment reverted successfully', 'success');
            // Reload data
            if (selectedCustomer) {
              await loadPayments(selectedCustomer.id);
              await loadSchedules(selectedCustomer.id);
              // Reload customer details
              const detailRes = await fetch(`admin/api/get-customer-detail.php?id=${selectedCustomer.id}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
              });
              if (detailRes.ok) {
                const detailData = await detailRes.json();
                if (detailData?.success && detailData?.customer) {
                  selectedCustomer = detailData.customer;
                  document.getElementById('totalPurchased').textContent = formatRs(selectedCustomer.total_purchased || 0);
                  document.getElementById('totalPaid').textContent = formatRs(selectedCustomer.total_paid || 0);
                }
              }
            }
          } else {
            showMessage('Failed to revert payment: ' + (data.error || 'Unknown error'), 'error');
          }
        } catch (error) {
          console.error('Revert payment error:', error);
          showMessage('Failed to revert payment: ' + error.message, 'error');
        }
      }

      // Revert schedule
      async function revertSchedule(scheduleId, orderId, paidAmount) {
        if (!confirm(`Are you sure you want to revert this schedule payment of ${formatRs(paidAmount)}? This will mark it as unpaid.`)) {
          return;
        }

        try {
          const response = await fetch('admin/api/revert-payment.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              schedule_id: scheduleId,
              order_id: orderId,
              amount: paidAmount
            })
          });

          if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          console.log('Revert schedule response:', data);
          
          if (data.success) {
            showMessage('Schedule payment reverted successfully', 'success');
            // Reload data
            if (selectedCustomer) {
              await loadSchedules(selectedCustomer.id);
              // Reload customer details
              const detailRes = await fetch(`admin/api/get-customer-detail.php?id=${selectedCustomer.id}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
              });
              if (detailRes.ok) {
                const detailData = await detailRes.json();
                if (detailData?.success && detailData?.customer) {
                  selectedCustomer = detailData.customer;
                  document.getElementById('totalPurchased').textContent = formatRs(selectedCustomer.total_purchased || 0);
                  document.getElementById('totalPaid').textContent = formatRs(selectedCustomer.total_paid || 0);
                }
              }
            }
          } else {
            showMessage('Failed to revert schedule: ' + (data.error || 'Unknown error'), 'error');
          }
        } catch (error) {
          console.error('Revert schedule error:', error);
          showMessage('Failed to revert schedule: ' + error.message, 'error');
        }
      }

      // Revert all payments
      document.getElementById('revertAllBtn')?.addEventListener('click', async function() {
        if (!selectedCustomer) {
          showMessage('Please search for a customer first', 'error');
          return;
        }

        if (!confirm(`⚠️ WARNING: This will revert ALL payments for ${selectedCustomer.full_name}. This action cannot be undone. Are you absolutely sure?`)) {
          return;
        }

        try {
          const response = await fetch('admin/api/revert-payment.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              customer_id: selectedCustomer.id,
              revert_all: true
            })
          });

          if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          console.log('Revert all response:', data);
          
          if (data.success) {
            showMessage('All payments reverted successfully', 'success');
            // Reload all data
            if (selectedCustomer) {
              await loadPayments(selectedCustomer.id);
              await loadSchedules(selectedCustomer.id);
              // Reload customer details
              const detailRes = await fetch(`admin/api/get-customer-detail.php?id=${selectedCustomer.id}`, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
              });
              if (detailRes.ok) {
                const detailData = await detailRes.json();
                if (detailData?.success && detailData?.customer) {
                  selectedCustomer = detailData.customer;
                  document.getElementById('totalPurchased').textContent = formatRs(selectedCustomer.total_purchased || 0);
                  document.getElementById('totalPaid').textContent = formatRs(selectedCustomer.total_paid || 0);
                }
              }
            }
          } else {
            showMessage('Failed to revert payments: ' + (data.error || 'Unknown error'), 'error');
          }
        } catch (error) {
          console.error('Revert all error:', error);
          showMessage('Failed to revert payments: ' + error.message, 'error');
        }
      });

      // Allow Enter key to search
      document.getElementById('customerSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          document.getElementById('searchCustomerBtn').click();
        }
      });
    </script>
  </body>
</html>

