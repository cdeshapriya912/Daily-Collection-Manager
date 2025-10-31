<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Collect Money</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
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
        <h1 class="text-2xl font-bold text-white">Hello Amila !</h1>
        <button class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
          <span class="material-icons text-gray-600">arrow_forward</span>
        </button>
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
              <button id="infoBtn" class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                <span class="material-icons text-white text-sm">info</span>
              </button>
            </div>
            <p>NIC: <span id="customerNIC">199125664V</span></p>
            <p>Mobile: <span id="customerMobile">0778553032</span></p>
          </div>
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
          <p class="text-gray-600 mb-4" id="successMessage">Payment collected successfully</p>
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

    <script>
      // Mock data for customers
      const customers = [
        { id: 'C001', name: 'Chinthaka', nic: '199125664V', mobile: '0778553032', balance: 250.00, totalAmount: 1900.00 },
        { id: 'C002', name: 'Amila', nic: '199125664V', mobile: '0772525453', balance: 380.00, totalAmount: 1900.00 }
      ];

      let selectedCustomer = null;

      // Customer search functionality
      document.getElementById('searchCustomerBtn').addEventListener('click', function() {
        const searchTerm = document.getElementById('customerSearch').value.toLowerCase();
        const customer = customers.find(c => 
          c.id.toLowerCase() === searchTerm ||
          c.name.toLowerCase().includes(searchTerm) ||
          c.mobile.includes(searchTerm) ||
          c.nic.includes(searchTerm)
        );
        
        if (customer) {
          selectCustomer(customer);
        } else {
          alert('Customer not found');
        }
      });

      function selectCustomer(customer) {
        selectedCustomer = customer;
        
        // Show customer details
        document.getElementById('customerName').textContent = customer.name;
        document.getElementById('customerNIC').textContent = customer.nic;
        document.getElementById('customerMobile').textContent = customer.mobile;
        document.getElementById('customerDetails').classList.remove('hidden');
        
        // Show due amount
        document.getElementById('dueAmountValue').textContent = `Rs. ${customer.balance.toFixed(2)}`;
        document.getElementById('dueAmount').classList.remove('hidden');
        
        // Update popup data
        const paidAmount = customer.totalAmount - customer.balance;
        const daysLeft = Math.ceil(customer.balance / (customer.totalAmount / 30));
        
        document.getElementById('popupRemainingAmount').textContent = customer.balance.toFixed(2);
        document.getElementById('popupDaysLeft').textContent = `${daysLeft} Days`;
        document.getElementById('popupPaidAmount').textContent = paidAmount.toFixed(2);
      }

      // Pay button functionality
      document.getElementById('payBtn').addEventListener('click', function() {
        const paymentInput = document.getElementById('paymentAmount');
        const modal = document.getElementById('paymentModal');
        modal.classList.remove('hidden');
        
        // Set max attribute to current balance
        paymentInput.setAttribute('max', selectedCustomer.balance.toFixed(2));
        
        // Set value and focus with selection
        paymentInput.value = selectedCustomer.balance.toFixed(2);
        
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
      document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        const paymentInput = document.getElementById('paymentAmount');
        const paymentAmount = parseFloat(paymentInput.value);
        
        // Validate payment amount
        if (isNaN(paymentAmount) || paymentAmount <= 0) {
          alert('Please enter a valid payment amount greater than 0');
          paymentInput.focus();
          paymentInput.select();
          return;
        }
        
        if (paymentAmount > selectedCustomer.balance) {
          alert(`Payment amount cannot exceed the balance of Rs. ${selectedCustomer.balance.toFixed(2)}`);
          paymentInput.focus();
          paymentInput.select();
          return;
        }
        
        // Update customer balance
        selectedCustomer.balance -= paymentAmount;
        
        // Update display
        document.getElementById('dueAmountValue').textContent = `Rs. ${selectedCustomer.balance.toFixed(2)}`;
        
        // Update popup data after payment
        const paidAmount = selectedCustomer.totalAmount - selectedCustomer.balance;
        const daysLeft = Math.ceil(selectedCustomer.balance / (selectedCustomer.totalAmount / 30));
        
        document.getElementById('popupRemainingAmount').textContent = selectedCustomer.balance.toFixed(2);
        document.getElementById('popupDaysLeft').textContent = `${daysLeft} Days`;
        document.getElementById('popupPaidAmount').textContent = paidAmount.toFixed(2);
        
        // Show success modal
        document.getElementById('successMessage').textContent = `Payment of Rs. ${paymentAmount.toFixed(2)} collected successfully`;
        document.getElementById('successModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('hidden');
        
        // Clear payment input
        paymentInput.value = '';
        
        // Send SMS notification
        sendPaymentSMS(selectedCustomer, paymentAmount);
        
        if (selectedCustomer.balance <= 0) {
          setTimeout(() => {
            alert('Customer has fully paid their balance!');
          }, 1000);
        }
      });

      /**
       * Send SMS notification after payment
       */
      async function sendPaymentSMS(customer, paymentAmount) {
        try {
          const response = await fetch('api/send-sms.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              customer_mobile: customer.mobile,
              customer_name: customer.name,
              payment_amount: paymentAmount,
              remaining_balance: customer.balance
            })
          });

          const result = await response.json();

          if (result.success) {
            console.log('[SMS] Sent successfully', { recipient: result.recipient });
          } else {
            console.error('[SMS] Failed', {
              error: result.error,
              http_code: result.http_code,
              response: result.response,
              raw: result.raw
            });
            // Do not interrupt UX; errors are logged for diagnostics
          }
        } catch (error) {
          console.error('[SMS] Network or unexpected error', { message: error?.message || String(error) });
        }
      }

      // Info button functionality
      document.getElementById('infoBtn').addEventListener('click', function() {
        document.getElementById('balancePopup').classList.remove('hidden');
      });

      // Close balance popup
      document.getElementById('closeBalancePopup').addEventListener('click', function() {
        document.getElementById('balancePopup').classList.add('hidden');
      });

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
    </script>
  </body>
</html>
