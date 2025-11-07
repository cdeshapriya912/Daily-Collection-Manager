<?php
  $activePage = isset($activePage) ? $activePage : '';
  $activeSubPage = isset($activeSubPage) ? $activeSubPage : '';
  
  // Determine if product submenu should be expanded
  $productSubmenuExpanded = in_array($activePage, ['product', 'add-product', 'category']) || in_array($activeSubPage, ['product-list', 'add-product', 'category']);
  
  // Determine if customer submenu should be expanded
  $customerSubmenuExpanded = in_array($activePage, ['customer', 'add-customer', 'list-customers']) || in_array($activeSubPage, ['customer-list', 'add-customer']);
?>
<aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-card-light border-r border-border-light transform -translate-x-full transition-transform duration-200 md:static md:translate-x-0 md:w-64 flex-shrink-0 md:transform-none flex flex-col">
  <div class="p-6 flex items-center gap-3">
    <div class="w-10 h-10 rounded-lg overflow-hidden">
      <img src="img/package.png" alt="Daily Collection" class="w-full h-full object-contain">
    </div>
    <h1 class="text-xl font-bold text-heading-light">Daily Collection</h1>
  </div>
  <nav class="mt-8 px-4">
    <ul>
      <li>
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'dashboard' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">dashboard</span>
          Dashboard
        </a>
      </li>
      <li class="mt-2">
        <a href="../collection.php" target="_blank" class="flex items-center gap-3 px-4 py-3 text-text-light hover:bg-gray-100 rounded-lg">
          <span class="material-icons">collections_bookmark</span>
          Collection
        </a>
      </li>
      <li class="mt-2">
        <button id="productMenuToggle" class="w-full flex items-center justify-between px-4 py-3 <?php echo $productSubmenuExpanded ? 'text-primary bg-primary/10 rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <div class="flex items-center gap-3">
            <span class="material-icons">inventory_2</span>
            Product
          </div>
          <span class="material-icons transform transition-transform <?php echo $productSubmenuExpanded ? 'rotate-180' : ''; ?>" id="productMenuIcon">expand_more</span>
        </button>
        <ul id="productSubmenu" class="ml-6 mt-2 space-y-1 <?php echo $productSubmenuExpanded ? '' : 'hidden'; ?>">
          <li>
            <a href="product.php" class="flex items-center gap-2 px-4 py-2 text-sm <?php echo ($activePage === 'product' && $activeSubPage !== 'add-product' && $activeSubPage !== 'category') ? 'bg-primary/10 text-primary rounded-lg font-medium' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
              <span class="material-icons text-sm">list</span>
              Product List
            </a>
          </li>
          <li>
            <a href="add-product.php" class="flex items-center gap-2 px-4 py-2 text-sm <?php echo ($activePage === 'add-product' || $activeSubPage === 'add-product') ? 'bg-primary/10 text-primary rounded-lg font-medium' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
              <span class="material-icons text-sm">add_circle</span>
              Add Product
            </a>
          </li>
          <li>
            <a href="category.php" class="flex items-center gap-2 px-4 py-2 text-sm <?php echo ($activePage === 'category' || $activeSubPage === 'category') ? 'bg-primary/10 text-primary rounded-lg font-medium' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
              <span class="material-icons text-sm">category</span>
              Category
            </a>
          </li>
        </ul>
      </li>
      <li class="mt-2">
        <a href="catalog.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'catalog' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">store</span>
          Catalog
        </a>
      </li>
      <li class="mt-2">
        <button id="customerMenuToggle" class="w-full flex items-center justify-between px-4 py-3 <?php echo $customerSubmenuExpanded ? 'text-primary bg-primary/10 rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <div class="flex items-center gap-3">
            <span class="material-icons">group</span>
            Customer
          </div>
          <span class="material-icons transform transition-transform <?php echo $customerSubmenuExpanded ? 'rotate-180' : ''; ?>" id="customerMenuIcon">expand_more</span>
        </button>
        <ul id="customerSubmenu" class="ml-6 mt-2 space-y-1 <?php echo $customerSubmenuExpanded ? '' : 'hidden'; ?>">
          <li>
            <a href="customer.php" class="flex items-center gap-2 px-4 py-2 text-sm <?php echo ($activePage === 'customer' && $activeSubPage !== 'add-customer') ? 'bg-primary/10 text-primary rounded-lg font-medium' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
              <span class="material-icons text-sm">list</span>
              List Customers
            </a>
          </li>
          <li>
            <a href="add-customer.php" class="flex items-center gap-2 px-4 py-2 text-sm <?php echo ($activePage === 'add-customer' || $activeSubPage === 'add-customer') ? 'bg-primary/10 text-primary rounded-lg font-medium' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
              <span class="material-icons text-sm">add_circle</span>
              Add New Customer
            </a>
          </li>
        </ul>
      </li>
      <li class="mt-2">
        <a href="assign-installment.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'assign-installment' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">credit_card</span>
          Installment
        </a>
      </li>
      <li class="mt-2">
        <a href="supplier.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'supplier' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">local_shipping</span>
          Suppliers
        </a>
      </li>
      <li class="mt-2">
        <a href="user.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'user' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">people</span>
          Staff
        </a>
      </li>
      <li class="mt-2">
        <a href="settings.php" class="flex items-center gap-3 px-4 py-3 <?php echo $activePage === 'settings' ? 'bg-primary/10 text-primary rounded-lg font-semibold' : 'text-text-light hover:bg-gray-100 rounded-lg'; ?>">
          <span class="material-icons">settings</span>
          Settings
        </a>
      </li>
    </ul>
  </nav>
  
  <!-- Logout Section -->
  <div class="mt-auto pt-6 px-4 border-t border-border-light">
    <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
      <span class="material-icons">logout</span>
      <span class="font-medium">Logout</span>
    </a>
  </div>
</aside>

<script>
  // Submenu toggle functionality (only if element exists)
  document.addEventListener('DOMContentLoaded', function() {
    const productMenuToggle = document.getElementById('productMenuToggle');
    if (productMenuToggle) {
      productMenuToggle.addEventListener('click', function() {
        const submenu = document.getElementById('productSubmenu');
        const icon = document.getElementById('productMenuIcon');
        if (submenu && icon) {
          submenu.classList.toggle('hidden');
          icon.classList.toggle('rotate-180');
        }
      });
    }
    
    const customerMenuToggle = document.getElementById('customerMenuToggle');
    if (customerMenuToggle) {
      customerMenuToggle.addEventListener('click', function() {
        const submenu = document.getElementById('customerSubmenu');
        const icon = document.getElementById('customerMenuIcon');
        if (submenu && icon) {
          submenu.classList.toggle('hidden');
          icon.classList.toggle('rotate-180');
        }
      });
    }
  });
</script>


