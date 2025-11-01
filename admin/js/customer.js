/* Customer page functionality */
(function () {
  // Load customers from database
  async function loadCustomers(searchQuery = '') {
    try {
      let url = 'api/get-customers.php?';
      const params = new URLSearchParams();
      
      if (searchQuery) {
        params.append('search', searchQuery);
      }
      
      url += params.toString();
      
      const response = await fetch(url);
      const data = await response.json();
      
      if (data.success) {
        displayCustomers(data.customers);
      } else {
        console.error('Failed to load customers:', data.error);
        showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to load customers. Please try again.',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error loading customers:', error);
      showNotificationDialog({
        title: 'Network Error',
        message: 'Failed to load customers. Please check your connection and try again.',
        type: 'error'
      });
    }
  }

  // Display customers in table
  function displayCustomers(customers) {
    const tbody = document.getElementById('customerTableBody');
    if (!tbody) return;
    
    if (customers.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="py-8 text-center text-text-light">
            <div class="flex flex-col items-center gap-2">
              <span class="material-icons text-4xl">people_outline</span>
              <p>No customers found</p>
            </div>
          </td>
        </tr>
      `;
      return;
    }
    
    tbody.innerHTML = customers.map(customer => {
      const remainingBalance = parseFloat(customer.remaining_balance || 0);
      const balanceClass = remainingBalance > 0 ? 'text-red-600' : 'text-green-600';
      
      return `
        <tr class="border-b border-border-light hover:bg-gray-50">
          <td class="py-3 px-4 text-text-light">${customer.customer_code || 'N/A'}</td>
          <td class="py-3 px-4 text-heading-light font-medium">${customer.full_name || 'N/A'}</td>
          <td class="py-3 px-4 text-text-light">${customer.email || 'N/A'}</td>
          <td class="py-3 px-4 text-text-light">${customer.mobile || 'N/A'}</td>
          <td class="py-3 px-4 ${balanceClass} font-semibold">Rs. ${remainingBalance.toFixed(2)}</td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <button onclick="viewCustomer(${customer.id})" class="text-blue-600 hover:text-blue-700 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="View">
                <span class="material-icons text-lg">visibility</span>
              </button>
              <button onclick="editCustomer(${customer.id})" class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                <span class="material-icons text-lg">edit</span>
              </button>
              <button onclick="deleteCustomer(${customer.id}, '${customer.full_name.replace(/'/g, "\\'")}' )" class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                <span class="material-icons text-lg">delete</span>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // View customer details (global)
  window.viewCustomer = async function(customerId) {
    try {
      const response = await fetch(`api/get-customer-detail.php?id=${customerId}`);
      const data = await response.json();
      
      if (data.success) {
        const customer = data.customer;
        const remainingBalance = parseFloat(customer.remaining_balance || 0);
        
        // Populate view modal
        document.getElementById('viewCustomerCode').textContent = customer.customer_code || 'N/A';
        document.getElementById('viewCustomerName').textContent = customer.full_name || 'N/A';
        document.getElementById('viewCustomerEmail').textContent = customer.email || 'N/A';
        document.getElementById('viewCustomerMobile').textContent = customer.mobile || 'N/A';
        document.getElementById('viewCustomerAddress').textContent = customer.address || 'N/A';
        document.getElementById('viewCustomerStatus').textContent = customer.status || 'active';
        document.getElementById('viewCustomerBalance').textContent = `Rs. ${remainingBalance.toFixed(2)}`;
        document.getElementById('viewCustomerTotalPurchased').textContent = `Rs. ${parseFloat(customer.total_purchased || 0).toFixed(2)}`;
        document.getElementById('viewCustomerTotalPaid').textContent = `Rs. ${parseFloat(customer.total_paid || 0).toFixed(2)}`;
        
        // Set status badge color
        const statusBadge = document.getElementById('viewCustomerStatus');
        statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium';
        if (customer.status === 'active') {
          statusBadge.classList.add('bg-green-100', 'text-green-700');
        } else if (customer.status === 'inactive') {
          statusBadge.classList.add('bg-gray-100', 'text-gray-700');
        } else if (customer.status === 'blocked') {
          statusBadge.classList.add('bg-red-100', 'text-red-700');
        }
        
        // Set balance color
        const balanceElement = document.getElementById('viewCustomerBalance');
        balanceElement.className = remainingBalance > 0 ? 'text-red-600 font-bold text-lg' : 'text-green-600 font-bold text-lg';
        
        // Show modal
        document.getElementById('viewCustomerModal').classList.remove('hidden');
      } else {
        showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to load customer details',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error loading customer:', error);
      showNotificationDialog({
        title: 'Network Error',
        message: 'Failed to load customer details. Please try again.',
        type: 'error'
      });
    }
  };

  // Edit customer function (global)
  window.editCustomer = async function(customerId) {
    try {
      const response = await fetch(`api/get-customer-detail.php?id=${customerId}`);
      const data = await response.json();
      
      if (data.success) {
        const customer = data.customer;
        
        // Populate edit form
        document.getElementById('editCustomerId').value = customer.id;
        document.getElementById('editCustomerName').value = customer.full_name;
        document.getElementById('editCustomerEmail').value = customer.email || '';
        document.getElementById('editCustomerMobile').value = customer.mobile;
        document.getElementById('editCustomerAddress').value = customer.address || '';
        document.getElementById('editCustomerStatus').value = customer.status;
        
        // Show modal
        document.getElementById('editCustomerModal').classList.remove('hidden');
      } else {
        showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to load customer details',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error loading customer:', error);
      showNotificationDialog({
        title: 'Network Error',
        message: 'Failed to load customer details. Please try again.',
        type: 'error'
      });
    }
  };

  // Delete customer function (global)
  window.deleteCustomer = async function(customerId, customerName) {
    // Show custom confirmation dialog
    const confirmed = await showConfirmDialog({
      title: 'Delete Customer?',
      message: `Are you sure you want to delete "${customerName}"? This action cannot be undone.`,
      confirmText: 'Delete',
      cancelText: 'Cancel',
      type: 'danger'
    });
    
    if (!confirmed) {
      return;
    }
    
    try {
      const response = await fetch('api/delete-customer.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${customerId}`
      });
      
      const data = await response.json();
      
      if (data.success) {
        await showNotificationDialog({
          title: 'Success!',
          message: 'Customer deleted successfully!',
          type: 'success'
        });
        // Reload customers
        performSearch();
      } else {
        await showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to delete customer. Please try again.',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error deleting customer:', error);
      await showNotificationDialog({
        title: 'Network Error',
        message: 'An error occurred while deleting the customer. Please check your connection and try again.',
        type: 'error'
      });
    }
  };

  // Search functionality
  function performSearch() {
    const searchQuery = document.getElementById('searchInput')?.value?.trim() || '';
    loadCustomers(searchQuery);
  }

  // Event listeners
  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');

  if (searchBtn) {
    searchBtn.addEventListener('click', performSearch);
  }

  if (searchInput) {
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });
  }

  // Add Customer button
  const addCustomerBtn = document.getElementById('addCustomerBtn');
  if (addCustomerBtn) {
    addCustomerBtn.addEventListener('click', () => {
      document.getElementById('addCustomerModal').classList.remove('hidden');
    });
  }

  // Modal control functions
  function closeAddModal() {
    document.getElementById('addCustomerModal').classList.add('hidden');
    document.getElementById('addCustomerForm').reset();
  }

  function closeEditModal() {
    document.getElementById('editCustomerModal').classList.add('hidden');
    document.getElementById('editCustomerForm').reset();
  }

  function closeViewModal() {
    document.getElementById('viewCustomerModal').classList.add('hidden');
  }

  // Close modal buttons
  document.getElementById('closeAddModal')?.addEventListener('click', closeAddModal);
  document.getElementById('cancelAddBtn')?.addEventListener('click', closeAddModal);
  
  document.getElementById('closeEditModal')?.addEventListener('click', closeEditModal);
  document.getElementById('cancelEditBtn')?.addEventListener('click', closeEditModal);
  
  document.getElementById('closeViewModal')?.addEventListener('click', closeViewModal);
  document.getElementById('closeViewBtn')?.addEventListener('click', closeViewModal);

  // Close modal on backdrop click
  document.getElementById('addCustomerModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'addCustomerModal') {
      closeAddModal();
    }
  });

  document.getElementById('editCustomerModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'editCustomerModal') {
      closeEditModal();
    }
  });

  document.getElementById('viewCustomerModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'viewCustomerModal') {
      closeViewModal();
    }
  });

  // Handle add form submission
  document.getElementById('addCustomerForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
      full_name: document.getElementById('addCustomerName').value,
      email: document.getElementById('addCustomerEmail').value,
      mobile: document.getElementById('addCustomerMobile').value,
      address: document.getElementById('addCustomerAddress').value,
      status: document.getElementById('addCustomerStatus').value
    };
    
    try {
      const response = await fetch('api/add-customer.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });
      
      const data = await response.json();
      
      if (data.success) {
        closeAddModal();
        await showNotificationDialog({
          title: 'Success!',
          message: `Customer added successfully! Customer Code: ${data.customer_code}`,
          type: 'success'
        });
        performSearch(); // Refresh the list
      } else {
        await showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to add customer',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error adding customer:', error);
      await showNotificationDialog({
        title: 'Network Error',
        message: 'An error occurred while adding the customer. Please try again.',
        type: 'error'
      });
    }
  });

  // Handle edit form submission
  document.getElementById('editCustomerForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
      id: document.getElementById('editCustomerId').value,
      full_name: document.getElementById('editCustomerName').value,
      email: document.getElementById('editCustomerEmail').value,
      mobile: document.getElementById('editCustomerMobile').value,
      address: document.getElementById('editCustomerAddress').value,
      status: document.getElementById('editCustomerStatus').value
    };
    
    try {
      const response = await fetch('api/update-customer.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
      });
      
      const data = await response.json();
      
      if (data.success) {
        closeEditModal();
        await showNotificationDialog({
          title: 'Success!',
          message: 'Customer updated successfully!',
          type: 'success'
        });
        performSearch(); // Refresh the list
      } else {
        await showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to update customer',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error updating customer:', error);
      await showNotificationDialog({
        title: 'Network Error',
        message: 'An error occurred while updating the customer. Please try again.',
        type: 'error'
      });
    }
  });

  // Initialize: Load customers on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadCustomers(); // Load all customers initially
  });
})();

