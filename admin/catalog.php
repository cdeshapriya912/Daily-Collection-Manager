<?php
/**
 * Product Catalog Page
 * 
 * Features:
 * - Real-time product listing from database
 * - Live data from products, categories, and suppliers tables
 * - No caching - always fresh data
 * - Search, filter, and sort capabilities
 * - Responsive grid layout
 * 
 * Data Source: MySQL database (SAHANALK.products + categories + suppliers)
 */

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
    <style>
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }
      #liveIndicator {
        animation: pulse 2s ease-in-out infinite;
      }
      .product-skeleton {
        animation: skeleton-loading 1s linear infinite alternate;
      }
      @keyframes skeleton-loading {
        0% { background-color: #f3f4f6; }
        100% { background-color: #e5e7eb; }
      }
    </style>
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
          <!-- Image Warning Banner (shown if products have no images) -->
          <div id="noImageWarning" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 hidden">
            <div class="flex items-start gap-3">
              <span class="material-icons text-yellow-600 text-2xl">warning</span>
              <div class="flex-1">
                <h3 class="font-semibold text-yellow-900">📸 Products Missing Images</h3>
                <p class="text-yellow-800 text-sm mt-1">Your products don't have images yet. Add images to make your catalog more attractive!</p>
                <a href="update-product-images.php" class="inline-flex items-center gap-2 bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors mt-3 text-sm font-medium">
                  <span class="material-icons text-lg">add_photo_alternate</span>
                  Add Product Images Now
                </a>
              </div>
            </div>
          </div>

          <!-- Real-time Data Indicator -->
          <div class="bg-primary/10 border border-primary/20 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="material-icons text-primary text-sm" id="liveIndicator" title="Live data indicator">fiber_manual_record</span>
              <div>
                <p class="font-semibold text-heading-light">Real-time Product Catalog</p>
                <p class="text-sm text-text-light" id="lastUpdateTime">Loading from database...</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 bg-white rounded-full text-sm font-semibold text-primary" id="totalProducts">0 Products</span>
              <button 
                id="refreshCatalogBtn" 
                class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2 font-medium"
                title="Refresh product list"
              >
                <span class="material-icons text-lg">refresh</span>
                Refresh
              </button>
            </div>
          </div>
          
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
                    <!-- Categories loaded dynamically from database -->
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
            <!-- Products loaded dynamically from database -->
            <div id="loadingState" class="col-span-full flex flex-col items-center justify-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
              <p class="text-text-light">Loading products from database...</p>
            </div>
            <div id="emptyState" class="col-span-full flex flex-col items-center justify-center py-12 hidden">
              <span class="material-icons text-6xl text-text-light mb-4">inventory_2</span>
              <p class="text-heading-light font-semibold text-lg mb-2">No Products Found</p>
              <p class="text-text-light">Try adjusting your search or filters</p>
            </div>
          </div>

          <!-- Load More Button (hidden by default, shown when needed) -->
          <div class="text-center mt-8 hidden" id="loadMoreContainer">
            <button id="loadMoreBtn" class="bg-primary text-white px-8 py-3 rounded-lg hover:bg-primary/90 transition-colors font-medium">
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
      // ========================================================================
      // CATALOG MANAGEMENT - REAL-TIME DATABASE DATA LOADING
      // ========================================================================
      // This page loads products with REAL-TIME data from the database.
      // Product data is fetched live from products, categories, and suppliers tables.
      // NO CACHING is applied - every request fetches fresh data from MySQL.
      // ========================================================================

      (function() {
        const searchInput = document.getElementById('catalogSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const sortBy = document.getElementById('sortBy');
        const productsGrid = document.getElementById('productsGrid');
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const refreshBtn = document.getElementById('refreshCatalogBtn');
        const totalProductsEl = document.getElementById('totalProducts');
        const lastUpdateTimeEl = document.getElementById('lastUpdateTime');
        
        let allProducts = [];
        let allCategories = [];
        
        // Update last update time indicator
        function updateLastUpdateTime() {
          const now = new Date();
          const timeString = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true 
          });
          
          if (lastUpdateTimeEl) {
            lastUpdateTimeEl.textContent = `Last updated: ${timeString}`;
          }
          
          // Pulse the live indicator
          const liveIndicator = document.getElementById('liveIndicator');
          if (liveIndicator) {
            liveIndicator.style.animation = 'none';
            setTimeout(() => {
              liveIndicator.style.animation = 'pulse 2s ease-in-out infinite';
            }, 10);
          }
        }

        // Load categories from database for filter dropdown
        async function loadCategories() {
          try {
            const response = await fetch(`api/get-categories.php?_t=${Date.now()}`, {
              method: 'GET',
              cache: 'no-store',
              headers: { 
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache'
              }
            });
            
            const result = await response.json();
            
            if (result.success && result.categories) {
              allCategories = result.categories;
              
              // Populate category dropdown
              if (categoryFilter) {
                // Keep the "All Categories" option
                categoryFilter.innerHTML = '<option value="">All Categories</option>';
                
                result.categories.forEach(cat => {
                  const option = document.createElement('option');
                  option.value = cat.name;
                  option.textContent = `${cat.name} (${cat.product_count || 0})`;
                  categoryFilter.appendChild(option);
                });
              }
            }
          } catch (error) {
            console.error('Failed to load categories:', error);
          }
        }

        // Load products from database
        async function loadProducts() {
          try {
            console.log('📊 Fetching real-time product data from database...');
            
            const params = new URLSearchParams();
            params.append('_t', Date.now());
            params.append('_r', Math.random());
            
            const searchTerm = searchInput?.value.trim();
            if (searchTerm) params.append('search', searchTerm);
            
            const category = categoryFilter?.value;
            if (category) params.append('category', category);
            
            const sort = sortBy?.value;
            if (sort) params.append('sort', sort);
            
            const url = `api/get-catalog-products.php?${params.toString()}`;
            
            // Show loading state
            if (loadingState) loadingState.classList.remove('hidden');
            if (emptyState) emptyState.classList.add('hidden');
            
            // Remove existing products
            const existingProducts = productsGrid.querySelectorAll('.product-card');
            existingProducts.forEach(card => {
              if (!card.id || (card.id !== 'loadingState' && card.id !== 'emptyState')) {
                card.remove();
              }
            });
            
            const response = await fetch(url, {
              method: 'GET',
              cache: 'no-store',
              headers: { 
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
              }
            });
            
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const result = await response.json();
            
            console.log(`✅ Loaded ${result.products?.length || 0} products from database`);
            
            // Debug: Log product image URLs
            console.log('📸 Product Image URLs:', result.products?.map(p => ({
              name: p.name,
              image_url: p.image_url || 'NO IMAGE',
              has_image: !!(p.image_url && p.image_url.trim())
            })));
            
            // Hide loading state
            if (loadingState) loadingState.classList.add('hidden');
            
            if (result.success && result.products && result.products.length > 0) {
              allProducts = result.products;
              renderProducts(result.products);
              
              // Update total count
              if (totalProductsEl) {
                totalProductsEl.textContent = `${result.products.length} ${result.products.length === 1 ? 'Product' : 'Products'}`;
              }
              
              // Check if products have images and show warning if not
              const productsWithoutImages = result.products.filter(p => !p.image_url || p.image_url.trim() === '');
              const noImageWarning = document.getElementById('noImageWarning');
              if (noImageWarning && productsWithoutImages.length > 0) {
                noImageWarning.classList.remove('hidden');
                console.warn(`⚠️ ${productsWithoutImages.length} products don't have images:`, 
                  productsWithoutImages.map(p => p.name));
              } else if (noImageWarning) {
                noImageWarning.classList.add('hidden');
              }
            } else {
              // Show empty state
              if (emptyState) emptyState.classList.remove('hidden');
              
              if (totalProductsEl) {
                totalProductsEl.textContent = '0 Products';
              }
            }
            
            // Update last update time
            updateLastUpdateTime();
            
          } catch (error) {
            console.error('❌ Failed to load products:', error);
            if (loadingState) loadingState.classList.add('hidden');
            if (emptyState) emptyState.classList.remove('hidden');
          }
        }

        // Render products to grid
        function renderProducts(products) {
          // Insert products before loading/empty states
          products.forEach(product => {
            const card = createProductCard(product);
            if (card) {
              productsGrid.insertBefore(card, loadingState);
            }
          });
        }

        // Generate placeholder image URL based on category
        function getPlaceholderImage(product) {
          const categoryImages = {
            'Electronics': 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop',
            'Accessories': 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
            'Cables': 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
            'Furniture': 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=400&h=400&fit=crop',
            'Clothing': 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=400&h=400&fit=crop'
          };
          
          // Return category-based image or generic product image
          return categoryImages[product.category_name] || 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop';
        }

        // Resolve image URL coming from DB to a proper browser URL
        function resolveImageUrl(rawUrl) {
          if (!rawUrl) return '';
          const url = String(rawUrl).trim();
          // Absolute or data URL → use as-is
          if (/^https?:\/\//i.test(url) || /^data:image\//i.test(url)) return url;
          // Compute app base path (e.g., "/Daily-Collection-Manager") from current location
          const appBase = (window.location.pathname.split('/admin/')[0]) || '';
          // Normalize leading './'
          let normalized = url.replace(/^\.\//, '');
          // If already starts with app base or domain-root absolute path, keep as-is
          if (normalized.startsWith('/')) return normalized; // e.g., /Daily-Collection-Manager/upload/...
          // Common case: stored as "upload/..." → prefix the app base
          return `${appBase}/${normalized}`;
        }
        
        // Create product card HTML
        function createProductCard(product) {
          const card = document.createElement('div');
          card.className = 'product-card bg-card-light rounded-lg border border-border-light overflow-hidden shadow-sm hover:shadow-md transition-shadow';
          
          // Determine stock status badge
          let stockBadge = '';
          if (product.stock_status === 'in_stock') {
            stockBadge = '<span class="bg-primary text-white px-2 py-1 rounded-full text-xs font-medium">In Stock</span>';
          } else if (product.stock_status === 'low_stock') {
            stockBadge = '<span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium">Low Stock</span>';
          } else {
            stockBadge = '<span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">Out of Stock</span>';
          }
          
          // Determine image URL - use product image (resolved) or category-based placeholder
          let imageUrl = product.image_url && product.image_url.trim() !== '' 
            ? resolveImageUrl(product.image_url) 
            : getPlaceholderImage(product);
          
          // Debug log for each product
          console.log(`🖼️ ${product.name}: ${product.image_url ? 'Using DB image' : 'Using placeholder'} - ${imageUrl}`);
          
          // Format prices
          const sellingPrice = parseFloat(product.price_selling || 0).toFixed(2);
          const buyingPrice = parseFloat(product.price_buying || 0).toFixed(2);
          
          card.innerHTML = `
            <div class="relative">
              <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                <img 
                  src="${escapeHtml(imageUrl)}" 
                  alt="${escapeHtml(product.name)}" 
                  class="product-image w-full h-full object-cover transition-opacity duration-300"
                  loading="lazy"
                  onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(product.name)}&size=400&background=10b981&color=fff&bold=true&format=svg';"
                >
              </div>
              <div class="absolute top-3 right-3">
                ${stockBadge}
              </div>
              ${product.category_name ? `
              <div class="absolute top-3 left-3">
                <span class="bg-white/90 backdrop-blur-sm text-heading-light px-2 py-1 rounded text-xs font-medium shadow-sm">${escapeHtml(product.category_name)}</span>
              </div>
              ` : ''}
            </div>
            <div class="p-4">
              <div class="flex items-start justify-between mb-2">
                <h3 class="text-lg font-semibold text-heading-light line-clamp-2">${escapeHtml(product.name)}</h3>
                <span class="text-xs text-text-light bg-gray-100 px-2 py-1 rounded whitespace-nowrap ml-2">${escapeHtml(product.sku)}</span>
              </div>
              <p class="text-sm text-text-light mb-3 line-clamp-2">${escapeHtml(product.description || 'No description available')}</p>
              <div class="flex items-center justify-between mb-3">
                <div class="flex flex-col">
                  <span class="text-2xl font-bold text-primary">Rs. ${sellingPrice}</span>
                  ${buyingPrice > 0 && buyingPrice != sellingPrice ? `<span class="text-xs text-text-light">Cost: Rs. ${buyingPrice}</span>` : ''}
                </div>
                <span class="text-sm ${product.quantity > 10 ? 'text-primary' : product.quantity > 0 ? 'text-orange-600' : 'text-red-600'} font-medium">${product.quantity} in stock</span>
              </div>
              <div class="flex gap-2">
                <button class="flex-1 bg-primary text-white py-2 px-4 rounded-lg hover:bg-primary/90 transition-colors font-medium text-sm" ${product.quantity <= 0 ? 'disabled' : ''}>
                  <span class="material-icons text-sm mr-1 align-middle">shopping_cart</span>
                  ${product.quantity > 0 ? 'Add to Cart' : 'Out of Stock'}
                </button>
                <button class="bg-gray-100 text-text-light p-2 rounded-lg hover:bg-gray-200 transition-colors" title="View Details">
                  <span class="material-icons text-lg">visibility</span>
                </button>
              </div>
            </div>
          `;
          
          return card;
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
          if (text === null || text === undefined) return '';
          const div = document.createElement('div');
          div.textContent = String(text);
          return div.innerHTML;
        }

        // Event listeners
        if (searchInput) {
          let searchTimeout;
          searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProducts(), 500); // Debounce search
          });
        }
        
        if (categoryFilter) {
          categoryFilter.addEventListener('change', loadProducts);
        }
        
        if (sortBy) {
          sortBy.addEventListener('change', loadProducts);
        }
        
        if (refreshBtn) {
          refreshBtn.addEventListener('click', async function() {
            refreshBtn.disabled = true;
            const originalHTML = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<span class="material-icons text-lg animate-spin">refresh</span> Refreshing...';
            try {
              await loadCategories();
              await loadProducts();
            } finally {
              refreshBtn.disabled = false;
              refreshBtn.innerHTML = originalHTML;
            }
          });
        }

        // Initialize - load categories then products
        async function initializeCatalog() {
          console.log('🔄 Initializing catalog page with real-time data...');
          await loadCategories();
          await loadProducts();
        }

        // Run initialization
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initializeCatalog);
        } else {
          initializeCatalog();
        }
      })();
    </script>
  </body>
</html>
