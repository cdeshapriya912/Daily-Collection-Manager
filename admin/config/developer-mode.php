<?php
/**
 * Developer Mode Configuration
 * 
 * IMPORTANT: Set DEVELOPER_MODE to false in production!
 * 
 * When enabled:
 * - Skip login authentication
 * - Auto-login as specified user
 * - Direct access to all pages
 * - Bypass OTP verification
 */

// ============================================================
// DEVELOPER MODE SETTING
// Set to true for development, false for production
// ============================================================
define('DEVELOPER_MODE', true); // Change to false for production

// ============================================================
// AUTO-LOGIN USER CONFIGURATION (when DEVELOPER_MODE is true)
// ============================================================
define('DEV_USER_ID', 1);                    // User ID to auto-login as
define('DEV_USERNAME', 'admin');             // Username
define('DEV_FULL_NAME', 'Developer Admin');  // Full name
define('DEV_EMAIL', 'dev@example.com');      // Email
define('DEV_ROLE_ID', 1);                    // Role: 1 = admin, 2 = staff
define('DEV_STATUS', 'active');              // Status

// ============================================================
// VISUAL INDICATORS
// ============================================================
define('SHOW_DEV_BANNER', true);             // Show developer mode banner

/**
 * Initialize Developer Mode Session
 * Automatically logs in the developer user
 */
function initDeveloperMode() {
    if (!DEVELOPER_MODE) {
        return false;
    }
    
    // Check if already logged in
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return true;
    }
    
    // Auto-login as developer user
    $_SESSION['user_id'] = DEV_USER_ID;
    $_SESSION['username'] = DEV_USERNAME;
    $_SESSION['full_name'] = DEV_FULL_NAME;
    $_SESSION['email'] = DEV_EMAIL;
    $_SESSION['role_id'] = DEV_ROLE_ID;
    $_SESSION['logged_in'] = true;
    $_SESSION['developer_mode'] = true;
    
    return true;
}

/**
 * Check if developer mode is active
 */
function isDeveloperMode() {
    return DEVELOPER_MODE === true;
}

/**
 * Get developer mode banner HTML
 */
function getDeveloperBanner() {
    if (!DEVELOPER_MODE || !SHOW_DEV_BANNER) {
        return '';
    }
    
    return '
    <div id="developerModeBanner" class="fixed top-0 left-0 right-0 bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-4 py-2 z-[9999] shadow-lg">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-icons animate-pulse">developer_mode</span>
                <div>
                    <span class="font-bold">🔧 DEVELOPER MODE ACTIVE</span>
                    <span class="ml-3 text-sm opacity-90">|</span>
                    <span class="ml-3 text-sm">Logged in as: ' . htmlspecialchars(DEV_FULL_NAME) . '</span>
                    <span class="ml-3 text-sm opacity-90">|</span>
                    <span class="ml-3 text-sm">Role: ' . (DEV_ROLE_ID === 1 ? 'Admin' : 'Staff') . '</span>
                </div>
            </div>
            <div class="text-sm font-semibold bg-black text-yellow-400 px-3 py-1 rounded-full">
                ⚠️ DISABLE IN PRODUCTION
            </div>
        </div>
    </div>
    <div class="h-12"></div>
    ';
}

// Log developer mode status
if (DEVELOPER_MODE) {
    error_log('⚠️ DEVELOPER MODE IS ENABLED - Remember to disable in production!');
}
?>

