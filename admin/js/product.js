/* Product page functionality */
(function () {
  const searchInput = document.getElementById('searchInput');
  const searchBtn = document.getElementById('searchBtn');
  const searchResults = document.getElementById('searchResults');
  const noResults = document.getElementById('noResults');
  const resultsList = document.getElementById('resultsList');

  // Mock product data
  const mockProducts = [
    { id: 'P001', name: 'Wireless Headphones', price: 'Rs. 99.99', category: 'Electronics', stock: 25 },
    { id: 'P002', name: 'Laptop Stand', price: 'Rs. 49.99', category: 'Accessories', stock: 12 },
    { id: 'P003', name: 'USB-C Cable', price: 'Rs. 19.99', category: 'Cables', stock: 50 },
    { id: 'P004', name: 'Bluetooth Speaker', price: 'Rs. 79.99', category: 'Electronics', stock: 8 },
    { id: 'P005', name: 'Phone Case', price: 'Rs. 29.99', category: 'Accessories', stock: 30 },
    { id: 'P006', name: 'Power Bank', price: 'Rs. 59.99', category: 'Electronics', stock: 15 },
    { id: 'P007', name: 'Keyboard', price: 'Rs. 89.99', category: 'Accessories', stock: 20 },
    { id: 'P008', name: 'Mouse Pad', price: 'Rs. 12.99', category: 'Accessories', stock: 40 }
  ];

  function performSearch() {
    const query = searchInput.value.trim().toLowerCase();
    
    if (!query) {
      showNoResults();
      return;
    }

    const results = mockProducts.filter(product => 
      product.name.toLowerCase().includes(query) || 
      product.id.toLowerCase().includes(query)
    );

    if (results.length === 0) {
      showNoResults();
    } else {
      showResults(results);
    }
  }

  function showNoResults() {
    noResults.classList.remove('hidden');
    resultsList.classList.add('hidden');
  }

  function showResults(products) {
    noResults.classList.add('hidden');
    resultsList.classList.remove('hidden');
    
    resultsList.innerHTML = products.map(product => `
      <div class="border border-border-light rounded-lg p-4 mb-4 hover:bg-gray-50 transition-colors">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h4 class="text-lg font-semibold text-heading-light">Rs. {product.name}</h4>
              <span class="bg-primary/10 text-primary px-2 py-1 rounded text-sm font-medium">Rs. {product.id}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-text-light">
              <div>
                <span class="font-medium">Price:</span> Rs. {product.price}
              </div>
              <div>
                <span class="font-medium">Category:</span> Rs. {product.category}
              </div>
              <div>
                <span class="font-medium">Stock:</span> 
                <span class="font-semibold Rs. {product.stock < 10 ? 'text-red-600' : product.stock < 20 ? 'text-amber-600' : 'text-green-600'}">
                  Rs. {product.stock} units
                </span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2 ml-4">
            <button class="text-primary hover:text-primary/80 p-2 rounded-lg hover:bg-primary/10 transition-colors" title="Edit">
              <span class="material-icons">edit</span>
            </button>
            <button class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
              <span class="material-icons">delete</span>
            </button>
          </div>
        </div>
      </div>
    `).join('');
  }

  // Event listeners
  if (searchBtn) {
    searchBtn.addEventListener('click', performSearch);
  }

  if (searchInput) {
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });

    // Clear results when input is empty
    searchInput.addEventListener('input', (e) => {
      if (e.target.value.trim() === '') {
        showNoResults();
      }
    });
  }

  // Initialize with no results
  showNoResults();
})();


