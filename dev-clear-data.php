<?php
/**
 * Development Tool: Clear All Cookies and Session Data
 * Use this page during development to clear all stored login/panel data
 */

session_start();

// Clear all cookies
$cookiesToClear = [
    'remember_user',
    'remember_me_active',
    'last_panel',
    'PHPSESSID' // Session cookie
];

foreach ($cookiesToClear as $cookieName) {
    // Clear cookie by setting expiration to past
    setcookie($cookieName, '', time() - 3600, '/', '', false, true);
    // Also try clearing with different path variations
    setcookie($cookieName, '', time() - 3600, '/');
}

// Destroy session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Get all cookies for display
$allCookies = $_COOKIE ?? [];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clear Development Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
      body {
        font-family: system-ui, -apple-system, sans-serif;
        background: #f3f4f6;
      }
    </style>
  </head>
  <body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl p-8 max-w-md w-full">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="material-icons text-red-600 text-3xl">delete_sweep</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Development Data Cleaner</h1>
        <p class="text-gray-600 text-sm">Clears cookies, localStorage, and session data</p>
      </div>

      <div class="space-y-4 mb-6">
        <!-- Cookies Status -->
        <div class="bg-gray-50 rounded-lg p-4">
          <h3 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
            <span class="material-icons text-sm">cookie</span>
            Cookies Found: <?php echo count($allCookies); ?>
          </h3>
          <?php if (count($allCookies) > 0): ?>
            <ul class="text-xs text-gray-600 space-y-1">
              <?php foreach ($allCookies as $name => $value): ?>
                <li class="flex justify-between">
                  <span class="font-mono"><?php echo htmlspecialchars($name); ?></span>
                  <span class="text-gray-400"><?php echo strlen($value); ?> chars</span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-sm text-gray-500">No cookies found</p>
          <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2">
          <button 
            onclick="clearAllData()" 
            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
          >
            <span class="material-icons text-sm">delete_forever</span>
            Clear All Data & Reload
          </button>
          
          <button 
            onclick="clearLocalStorageOnly()" 
            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2"
          >
            <span class="material-icons text-sm">storage</span>
            Clear localStorage Only
          </button>
          
          <a 
            href="login.php" 
            class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-semibold transition-colors text-center"
          >
            Go to Login Page
          </a>
        </div>

        <!-- Status Message -->
        <div id="statusMessage" class="hidden bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-800 text-center"></div>
      </div>

      <div class="border-t pt-4">
        <p class="text-xs text-gray-500 text-center">
          This tool clears:<br>
          • Browser cookies (remember_user, last_panel, etc.)<br>
          • localStorage data<br>
          • PHP session data
        </p>
      </div>
    </div>

    <script>
      // Clear all data function
      function clearAllData() {
        // Clear localStorage
        const itemsToRemove = [
          'remember_user',
          'remember_me_active',
          'last_panel',
          'pwa-install-dismissed'
        ];
        
        itemsToRemove.forEach(item => {
          localStorage.removeItem(item);
        });

        // Clear all cookies by setting them to expire
        document.cookie.split(";").forEach(function(c) { 
          document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); 
        });

        // Show status
        showStatus('All data cleared! Redirecting...');

        // Reload page after a short delay
        setTimeout(() => {
          window.location.href = 'login.php';
        }, 1000);
      }

      // Clear localStorage only
      function clearLocalStorageOnly() {
        const itemsToRemove = [
          'remember_user',
          'remember_me_active',
          'last_panel',
          'pwa-install-dismissed'
        ];
        
        itemsToRemove.forEach(item => {
          localStorage.removeItem(item);
        });

        showStatus('localStorage cleared!');
      }

      // Show status message
      function showStatus(message) {
        const statusEl = document.getElementById('statusMessage');
        statusEl.textContent = message;
        statusEl.classList.remove('hidden');
        setTimeout(() => {
          statusEl.classList.add('hidden');
        }, 3000);
      }

      // Auto-detect and display localStorage data
      window.addEventListener('load', () => {
        const localStorageData = {};
        for (let i = 0; i < localStorage.length; i++) {
          const key = localStorage.key(i);
          if (key.includes('remember') || key.includes('panel') || key.includes('pwa')) {
            localStorageData[key] = localStorage.getItem(key);
          }
        }

        if (Object.keys(localStorageData).length > 0) {
          console.log('localStorage data found:', localStorageData);
        }
      });
    </script>
  </body>
</html>

