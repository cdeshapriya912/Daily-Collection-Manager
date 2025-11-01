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
    <title>Product Catalog - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Product Catalog - Daily Collection Manager">
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
      <?php $activePage = 'catalog'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Product Catalog</h2>
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
          <!-- Search and Filter Section -->
          <div class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <div class="flex flex-col lg:flex-row gap-4">
              <div class="flex-1">
                <label for="catalogSearch" class="block text-sm font-medium text-text-light mb-2">Search Products</label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="catalogSearch" 
                    placeholder="Search by name, category, or ID..." 
                    class="w-full px-4 py-3 pl-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                  >
                  <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-text-light">
                    <span class="material-icons text-lg">search</span>
                  </span>
                </div>
              </div>
              <div class="w-full lg:w-48">
                <label for="categoryFilter" class="block text-sm font-medium text-text-light mb-2">Category</label>
                <div class="relative">
                  <select 
                    id="categoryFilter" 
                    class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="">All Categories</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Cables">Cables</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Clothing">Clothing</option>
                  </select>
                  <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-text-light">
                    <span class="material-icons">expand_more</span>
                  </span>
                </div>
              </div>
              <div class="w-full lg:w-48">
                <label for="sortBy" class="block text-sm font-medium text-text-light mb-2">Sort By</label>
                <div class="relative">
                  <select 
                    id="sortBy" 
                    class="w-full px-4 py-3 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none appearance-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="name">Name A-Z</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                  </select>
                  <span class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-text-light">
                    <span class="material-icons">expand_more</span>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Products Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productsGrid">
            <!-- Product Card 1 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop&crop=center" alt="Wireless Headphones" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Wireless Headphones</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P001</span>
                </div>
                <p class="text-sm text-text-light mb-3">High-quality wireless headphones with noise cancellation</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 99.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 120.00</span>
                  </div>
                  <span class="text-sm text-text-light">25 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 2 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=300&fit=crop&crop=center" alt="Laptop Stand" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Laptop Stand</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P002</span>
                </div>
                <p class="text-sm text-text-light mb-3">Adjustable aluminum laptop stand for better ergonomics</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 49.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 60.00</span>
                  </div>
                  <span class="text-sm text-text-light">12 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 3 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&crop=center" alt="USB-C Cable" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">USB-C Cable</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P003</span>
                </div>
                <p class="text-sm text-text-light mb-3">Fast charging USB-C cable with data transfer capability</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 19.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 25.00</span>
                  </div>
                  <span class="text-sm text-text-light">50 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 4 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=300&h=300&fit=crop&crop=center" alt="Bluetooth Speaker" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Bluetooth Speaker</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P004</span>
                </div>
                <p class="text-sm text-text-light mb-3">Portable Bluetooth speaker with excellent sound quality</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 79.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 95.00</span>
                  </div>
                  <span class="text-sm text-text-light">8 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 5 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop&crop=center" alt="Phone Case" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Phone Case</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P005</span>
                </div>
                <p class="text-sm text-text-light mb-3">Protective phone case with shock absorption</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 29.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 35.00</span>
                  </div>
                  <span class="text-sm text-text-light">30 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 6 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1609592807891-4a0a8b0b8b0b?w=300&h=300&fit=crop&crop=center" alt="Power Bank" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Power Bank</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P006</span>
                </div>
                <p class="text-sm text-text-light mb-3">High capacity portable power bank for mobile devices</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 59.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 75.00</span>
                  </div>
                  <span class="text-sm text-text-light">15 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 7 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=300&h=300&fit=crop&crop=center" alt="Keyboard" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Mechanical Keyboard</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P007</span>
                </div>
                <p class="text-sm text-text-light mb-3">RGB mechanical keyboard with tactile switches</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 89.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 110.00</span>
                  </div>
                  <span class="text-sm text-text-light">20 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Card 8 -->
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="https://images.unsplash.com/photo-1527814050087-3793815479db?w=300&h=300&fit=crop&crop=center" alt="Mouse Pad" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">Gaming Mouse Pad</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">P008</span>
                </div>
                <p class="text-sm text-text-light mb-3">Large gaming mouse pad with smooth surface</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. 12.99</span>
                    <span class="text-sm text-text-light line-through">Rs. 18.00</span>
                  </div>
                  <span class="text-sm text-text-light">40 in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Load More Button -->
          <div class="text-center mt-8">
            <button class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">
              <span class="material-icons text-lg mr-2">expand_more</span>
              Load More Products
            </button>
          </div>
        </main>
      </div>
      <!-- mobile backdrop -->
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
    <script>
      // Catalog functionality
      (function() {
        const searchInput = document.getElementById('catalogSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortBy = document.getElementById('sortBy');
        const productsGrid = document.getElementById('productsGrid');
        
        // Mock product data
        const products = [
          {
            id: 'P001',
            name: 'Wireless Headphones',
            description: 'High-quality wireless headphones with noise cancellation',
            price: 99.99,
            originalPrice: 120.00,
            category: 'Electronics',
            stock: 25,
            image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P002',
            name: 'Laptop Stand',
            description: 'Adjustable aluminum laptop stand for better ergonomics',
            price: 49.99,
            originalPrice: 60.00,
            category: 'Accessories',
            stock: 12,
            image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P003',
            name: 'USB-C Cable',
            description: 'Fast charging USB-C cable with data transfer capability',
            price: 19.99,
            originalPrice: 25.00,
            category: 'Cables',
            stock: 50,
            image: 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P004',
            name: 'Bluetooth Speaker',
            description: 'Portable Bluetooth speaker with excellent sound quality',
            price: 79.99,
            originalPrice: 95.00,
            category: 'Electronics',
            stock: 8,
            image: 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P005',
            name: 'Phone Case',
            description: 'Protective phone case with shock absorption',
            price: 29.99,
            originalPrice: 35.00,
            category: 'Accessories',
            stock: 30,
            image: 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P006',
            name: 'Power Bank',
            description: 'High capacity portable power bank for mobile devices',
            price: 59.99,
            originalPrice: 75.00,
            category: 'Electronics',
            stock: 15,
            image: 'https://images.unsplash.com/photo-1609592807891-4a0a8b0b8b0b?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P007',
            name: 'Mechanical Keyboard',
            description: 'RGB mechanical keyboard with tactile switches',
            price: 89.99,
            originalPrice: 110.00,
            category: 'Accessories',
            stock: 20,
            image: 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=300&h=300&fit=crop&crop=center'
          },
          {
            id: 'P008',
            name: 'Gaming Mouse Pad',
            description: 'Large gaming mouse pad with smooth surface',
            price: 12.99,
            originalPrice: 18.00,
            category: 'Accessories',
            stock: 40,
            image: 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=300&h=300&fit=crop&crop=center'
          }
        ];

        function filterAndSortProducts() {
          const searchTerm = searchInput.value.toLowerCase();
          const selectedCategory = categoryFilter.value;
          const sortOption = sortBy.value;

          let filteredProducts = products.filter(product => {
            const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                 product.id.toLowerCase().includes(searchTerm) ||
                                 product.description.toLowerCase().includes(searchTerm);
            const matchesCategory = !selectedCategory || product.category === selectedCategory;
            return matchesSearch && matchesCategory;
          });

          // Sort products
          switch(sortOption) {
            case 'name':
              filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
              break;
            case 'price-low':
              filteredProducts.sort((a, b) => a.price - b.price);
              break;
            case 'price-high':
              filteredProducts.sort((a, b) => b.price - a.price);
              break;
            case 'newest':
              // For demo purposes, we'll sort by ID
              filteredProducts.sort((a, b) => b.id.localeCompare(a.id));
              break;
          }

          renderProducts(filteredProducts);
        }

        function renderProducts(productsToRender) {
          productsGrid.innerHTML = productsToRender.map(product => `
            <div class="product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm">
              <div class="relative">
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                  <img src="${product.image}" alt="${product.name}" class="product-image w-full h-full object-cover">
                </div>
                <div class="absolute top-3 right-3">
                  <span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>
                </div>
              </div>
              <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                  <h3 class="text-lg font-semibold text-heading-light">${product.name}</h3>
                  <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded">${product.id}</span>
                </div>
                <p class="text-sm text-text-light mb-3">${product.description}</p>
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary">Rs. ${product.price.toFixed(2)}</span>
                    <span class="text-sm text-text-light line-through">Rs. ${product.originalPrice.toFixed(2)}</span>
                  </div>
                  <span class="text-sm text-text-light">${product.stock} in stock</span>
                </div>
                <div class="flex gap-2">
                  <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium">
                    <span class="material-icons text-sm mr-1">shopping_cart</span>
                    Add to Cart
                  </button>
                  <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                    <span class="material-icons text-lg">visibility</span>
                  </button>
                </div>
              </div>
            </div>
          `).join('');
        }

        // Event listeners
        if (searchInput) {
          searchInput.addEventListener('input', filterAndSortProducts);
        }
        
        if (categoryFilter) {
          categoryFilter.addEventListener('change', filterAndSortProducts);
        }
        
        if (sortBy) {
          sortBy.addEventListener('change', filterAndSortProducts);
        }

        // Initialize with all products
        renderProducts(products);
      })();
    </script>
  </body>
</html>
