<?php
/**
 * Product Image Manager
 * Quick tool to add/update product images without SQL
 */

// Admin-only page
require_once __DIR__ . '/config/admin-auth.php';
require_once __DIR__ . '/config/db.php';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_images'])) {
    try {
        $updates = 0;
        foreach ($_POST['images'] as $productId => $imageUrl) {
            if (!empty(trim($imageUrl))) {
                $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = ?");
                $stmt->execute([trim($imageUrl), $productId]);
                $updates++;
            }
        }
        $message = "✅ Successfully updated $updates product image(s)!";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Get all products
try {
    $stmt = $pdo->query("
        SELECT p.id, p.sku, p.name, p.image_url, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id ASC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

$full_name = $_SESSION['full_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Image Manager - Daily Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .preview-img { max-width: 100px; max-height: 100px; object-fit: cover; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">📸 Product Image Manager</h1>
                        <p class="text-gray-600 mt-1">Add or update product images quickly</p>
                    </div>
                    <a href="catalog.php" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors inline-flex items-center gap-2">
                        <span class="material-icons">arrow_back</span>
                        Back to Catalog
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2">📝 How to Use:</h3>
                <ol class="list-decimal list-inside space-y-1 text-blue-800">
                    <li>Paste image URLs in the boxes below (get free images from <a href="https://unsplash.com" target="_blank" class="underline font-semibold">Unsplash.com</a>)</li>
                    <li>Click "Update All Images" button at the bottom</li>
                    <li>Go back to the catalog page and click "Refresh"</li>
                    <li>Your product images will appear! 🎉</li>
                </ol>
            </div>

            <!-- Form -->
            <form method="POST" class="space-y-4">
                <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                        <!-- Product Info -->
                        <div class="md:col-span-3">
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></p>
                            <p class="text-sm text-gray-500">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>
                            <p class="text-xs text-gray-400"><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></p>
                        </div>

                        <!-- Image URL Input -->
                        <div class="md:col-span-7">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                            <input 
                                type="url" 
                                name="images[<?php echo $product['id']; ?>]" 
                                value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>"
                                placeholder="https://images.unsplash.com/photo-xxx?w=400&h=400"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                onchange="previewImage(this, 'preview-<?php echo $product['id']; ?>')"
                            >
                            <p class="text-xs text-gray-500 mt-1">
                                💡 Tip: Right-click image on Unsplash → Copy Image Address
                            </p>
                        </div>

                        <!-- Preview -->
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-700 mb-1">Preview</p>
                            <div class="border border-gray-200 rounded-lg p-2 bg-gray-50 h-24 flex items-center justify-center">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img 
                                        src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                        alt="Preview" 
                                        class="preview-img rounded"
                                        id="preview-<?php echo $product['id']; ?>"
                                        onerror="this.src='https://via.placeholder.com/100?text=Error'"
                                    >
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs" id="preview-<?php echo $product['id']; ?>">No image</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Submit Button -->
                <div class="sticky bottom-4 bg-white rounded-lg shadow-lg p-6 border-2 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">Ready to update?</p>
                            <p class="text-sm text-gray-600">This will save all changes to the database</p>
                        </div>
                        <button 
                            type="submit" 
                            name="update_images"
                            class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition-colors font-semibold inline-flex items-center gap-2 shadow-md"
                        >
                            <span class="material-icons">save</span>
                            Update All Images
                        </button>
                    </div>
                </div>
            </form>

            <!-- Sample URLs -->
            <div class="bg-gray-100 rounded-lg p-6 mt-6">
                <h3 class="font-semibold text-gray-900 mb-3">🎨 Sample Image URLs (Copy & Paste):</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="font-medium text-gray-700">Electronics:</p>
                        <code class="text-xs bg-white px-2 py-1 rounded block mt-1">https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop</code>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Phone:</p>
                        <code class="text-xs bg-white px-2 py-1 rounded block mt-1">https://images.unsplash.com/photo-1592286849809-26c0415422cc?w=400&h=400&fit=crop</code>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Shoes:</p>
                        <code class="text-xs bg-white px-2 py-1 rounded block mt-1">https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop</code>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Accessories:</p>
                        <code class="text-xs bg-white px-2 py-1 rounded block mt-1">https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const url = input.value.trim();
            
            if (url) {
                // Create new image element
                const img = document.createElement('img');
                img.src = url;
                img.className = 'preview-img rounded';
                img.alt = 'Preview';
                img.onerror = function() {
                    preview.innerHTML = '<span class="text-red-500 text-xs">Invalid URL</span>';
                };
                
                preview.innerHTML = '';
                preview.appendChild(img);
            } else {
                preview.innerHTML = '<span class="text-gray-400 text-xs">No image</span>';
            }
        }
    </script>
</body>
</html>

