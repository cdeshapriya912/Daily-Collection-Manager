/* Product page functionality */
(function () {
  // Load categories dynamically
  async function loadCategories() {
    try {
      const response = await fetch('api/get-categories.php');
      const data = await response.json();
      
      if (data.success) {
        const categorySelect = document.getElementById('categorySelect');
        if (categorySelect) {
          // Clear existing options except "All Categories"
          categorySelect.innerHTML = '<option value="">All Categories</option>';
          
          data.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
          });
        }
      } else {
        console.error('Failed to load categories:', data.error);
      }
    } catch (error) {
      console.error('Error loading categories:', error);
      showNotificationDialog({
        title: 'Loading Error',
        message: 'Failed to load categories. Please refresh the page.',
        type: 'error'
      });
    }
  }

  // Load products from database
  async function loadProducts(searchQuery = '', categoryId = '') {
    try {
      let url = 'api/get-products.php?';
      const params = new URLSearchParams();
      
      if (searchQuery) {
        params.append('search', searchQuery);
      }
      if (categoryId) {
        params.append('category', categoryId);
      }
      
      url += params.toString();
      
      const response = await fetch(url);
      const data = await response.json();
      
      if (data.success) {
        displayProducts(data.products);
      } else {
        console.error('Failed to load products:', data.error);
        showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to load products. Please try again.',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error loading products:', error);
      showNotificationDialog({
        title: 'Network Error',
        message: 'Failed to load products. Please check your connection and try again.',
        type: 'error'
      });
    }
  }

  // Display products in table
  function displayProducts(products) {
    const tbody = document.getElementById('productTableBody');
    if (!tbody) return;
    
    if (products.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="py-8 text-center text-text-light">
            <div class="flex flex-col items-center gap-2">
              <span class="material-icons text-4xl">inventory_2</span>
              <p>No products found</p>
            </div>
          </td>
        </tr>
      `;
      return;
    }
    
    tbody.innerHTML = products.map(product => {
      // Fix image path - if it starts with 'upload/', prepend '../'
      let imagePath = product.image_url;
      if (imagePath && imagePath.startsWith('upload/')) {
        imagePath = '../' + imagePath;
      }
      
      return `
        <tr class="border-b border-border-light hover:bg-gray-50">
          <td class="py-3 px-4 text-text-light">${product.sku || 'N/A'}</td>
          <td class="py-3 px-4">
            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
              ${imagePath 
                ? `<img src="${imagePath}" alt="${product.name}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\\'material-icons text-gray-400\\'>image</span>'">` 
                : '<span class="material-icons text-gray-400">image</span>'
              }
            </div>
          </td>
          <td class="py-3 px-4 text-heading-light font-medium">${product.name || 'N/A'}</td>
          <td class="py-3 px-4 text-text-light">Rs. ${parseFloat(product.price_buying || 0).toFixed(2)}</td>
          <td class="py-3 px-4 text-primary font-semibold">Rs. ${parseFloat(product.price_selling || 0).toFixed(2)}</td>
          <td class="py-3 px-4 text-text-light">${product.quantity || 0}</td>
          <td class="py-3 px-4">
            <div class="flex items-center gap-2">
              <button onclick="editProduct(${product.id})" class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
                <span class="material-icons text-lg">edit</span>
              </button>
              <button onclick="deleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}' )" class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                <span class="material-icons text-lg">delete</span>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Edit product function (global)
  window.editProduct = async function(productId) {
    try {
      // Fetch product data
      const response = await fetch(`api/get-product-detail.php?id=${productId}`);
      const data = await response.json();
      
      if (data.success) {
        const product = data.product;
        
        // Populate form
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editProductSKU').value = product.sku;
        document.getElementById('editCategory').value = product.category_id || '';
        document.getElementById('editSupplier').value = product.supplier_id || '';
        document.getElementById('editBuyingPrice').value = product.price_buying;
        document.getElementById('editSellingPrice').value = product.price_selling;
        document.getElementById('editQuantity').value = product.quantity;
        document.getElementById('editStatus').value = product.status;
        document.getElementById('editDescription').value = product.description || '';
        
        // Show modal
        document.getElementById('editProductModal').classList.remove('hidden');
      } else {
        showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to load product details',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error loading product:', error);
      showNotificationDialog({
        title: 'Network Error',
        message: 'Failed to load product details. Please try again.',
        type: 'error'
      });
    }
  };

  // Delete product function (global)
  window.deleteProduct = async function(productId, productName) {
    // Show custom confirmation dialog
    const confirmed = await showConfirmDialog({
      title: 'Delete Product?',
      message: `Are you sure you want to delete "${productName}"? This action cannot be undone.`,
      confirmText: 'Delete',
      cancelText: 'Cancel',
      type: 'danger'
    });
    
    if (!confirmed) {
      return;
    }
    
    try {
      const response = await fetch('api/delete-product.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${productId}`
      });
      
      const data = await response.json();
      
      if (data.success) {
        await showNotificationDialog({
          title: 'Success!',
          message: 'Product deleted successfully!',
          type: 'success'
        });
        // Reload products
        performSearch();
      } else {
        await showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to delete product. Please try again.',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error deleting product:', error);
      await showNotificationDialog({
        title: 'Network Error',
        message: 'An error occurred while deleting the product. Please check your connection and try again.',
        type: 'error'
      });
    }
  };

  // Search functionality
  function performSearch() {
    const searchQuery = document.getElementById('searchInput')?.value?.trim() || '';
    const categoryId = document.getElementById('categorySelect')?.value || '';
    
    loadProducts(searchQuery, categoryId);
  }

  // Event listeners
  const searchBtn = document.getElementById('searchBtn');
  const searchInput = document.getElementById('searchInput');
  const categorySelect = document.getElementById('categorySelect');

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

  if (categorySelect) {
    categorySelect.addEventListener('change', performSearch);
  }

  // Add Product button
  const addProductBtn = document.getElementById('addProductBtn');
  if (addProductBtn) {
    addProductBtn.addEventListener('click', () => {
      window.location.href = 'add-product.php';
    });
  }

  // Modal control functions
  function closeEditModal() {
    document.getElementById('editProductModal').classList.add('hidden');
    document.getElementById('editProductForm').reset();
  }

  // Close modal on button click
  document.getElementById('closeEditModal')?.addEventListener('click', closeEditModal);
  document.getElementById('cancelEditBtn')?.addEventListener('click', closeEditModal);

  // Close modal on backdrop click
  document.getElementById('editProductModal')?.addEventListener('click', function(e) {
    if (e.target.id === 'editProductModal') {
      closeEditModal();
    }
  });

  // Real-time validation for edit form
  document.getElementById('editSellingPrice')?.addEventListener('input', function() {
    validateEditPrices();
  });
  
  document.getElementById('editBuyingPrice')?.addEventListener('input', function() {
    validateEditPrices();
  });
  
  function validateEditPrices() {
    const buyingPrice = parseFloat(document.getElementById('editBuyingPrice').value) || 0;
    const sellingPrice = parseFloat(document.getElementById('editSellingPrice').value) || 0;
    const sellingPriceInput = document.getElementById('editSellingPrice');
    
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

  // Handle edit form submission
  document.getElementById('editProductForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const buyingPrice = parseFloat(document.getElementById('editBuyingPrice').value) || 0;
    const sellingPrice = parseFloat(document.getElementById('editSellingPrice').value) || 0;
    
    // Validate: Selling price must be greater than buying price
    if (sellingPrice <= buyingPrice) {
      await showNotificationDialog({
        title: 'Invalid Price',
        message: `Selling Price (Rs. ${sellingPrice.toFixed(2)}) must be greater than Buying Price (Rs. ${buyingPrice.toFixed(2)}). Please adjust your prices.`,
        type: 'warning'
      });
      document.getElementById('editSellingPrice').focus();
      return;
    }
    
    const formData = {
      id: document.getElementById('editProductId').value,
      name: document.getElementById('editProductName').value,
      category_id: document.getElementById('editCategory').value,
      supplier_id: document.getElementById('editSupplier').value,
      price_buying: buyingPrice,
      price_selling: sellingPrice,
      quantity: document.getElementById('editQuantity').value,
      status: document.getElementById('editStatus').value,
      description: document.getElementById('editDescription').value
    };
    
    try {
      const response = await fetch('api/update-product.php', {
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
          message: 'Product updated successfully!',
          type: 'success'
        });
        performSearch(); // Refresh the list
      } else {
        await showNotificationDialog({
          title: 'Error',
          message: data.error || 'Failed to update product',
          type: 'error'
        });
      }
    } catch (error) {
      console.error('Error updating product:', error);
      await showNotificationDialog({
        title: 'Network Error',
        message: 'An error occurred while updating the product. Please try again.',
        type: 'error'
      });
    }
  });

  // Load categories and suppliers for edit modal
  async function loadEditFormDropdowns() {
    try {
      // Load categories
      const catResponse = await fetch('api/get-categories.php');
      const catData = await catResponse.json();
      if (catData.success) {
        const editCategorySelect = document.getElementById('editCategory');
        catData.categories.forEach(category => {
          const option = document.createElement('option');
          option.value = category.id;
          option.textContent = category.name;
          editCategorySelect.appendChild(option);
        });
      }
      
      // Load suppliers
      const suppResponse = await fetch('api/get-suppliers.php');
      const suppData = await suppResponse.json();
      if (suppData.success) {
        const editSupplierSelect = document.getElementById('editSupplier');
        suppData.suppliers.forEach(supplier => {
          const option = document.createElement('option');
          option.value = supplier.id;
          option.textContent = supplier.company_name;
          editSupplierSelect.appendChild(option);
        });
      }
    } catch (error) {
      console.error('Error loading dropdowns:', error);
    }
  }

  // Initialize: Load categories and products on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadProducts(); // Load all products initially
    loadEditFormDropdowns(); // Load dropdowns for edit modal
  });
})();


