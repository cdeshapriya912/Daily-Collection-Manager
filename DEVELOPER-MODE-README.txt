===============================================
DEVELOPER MODE - OTP AUTO-FILL CONFIGURATION
===============================================

This system includes a DEVELOPER MODE feature that automatically fills OTP codes
during development to save time. You don't need to check email repeatedly!

===============================================
HOW TO ENABLE/DISABLE
===============================================

📍 LOCATION: login.php (around line 782)

const DEVELOPER_MODE = true;  // For development (OTP auto-fills)
const DEVELOPER_MODE = false; // For production (normal behavior)

===============================================
WHAT HAPPENS IN EACH MODE
===============================================

✅ DEVELOPER MODE = true (Development)
   - OTP code automatically fills in the input field
   - Yellow banner appears showing the OTP code
   - Green message: "OTP Code Auto-Filled!"
   - Console logs show the OTP code
   - Just click "Verify OTP" - no need to check email!

❌ DEVELOPER MODE = false (Production)
   - Normal behavior - no auto-fill
   - User must check email for OTP
   - No testing banner or messages appear
   - OTP codes not logged to console

===============================================
CONSOLE INDICATORS
===============================================

When you load the login page, check the browser console (F12):

DEVELOPER MODE ON:
🔧 DEVELOPER MODE ENABLED
✅ OTP auto-fill is ACTIVE
⚠️ Remember to set DEVELOPER_MODE = false for production!

DEVELOPER MODE OFF:
🔒 PRODUCTION MODE
❌ OTP auto-fill is DISABLED

===============================================
IMPORTANT NOTES
===============================================

⚠️ ALWAYS set DEVELOPER_MODE = false before deploying to production!

⚠️ The OTP 'otp' field in API responses (send-otp.php, resend-login-otp.php, 
   login.php) should also be removed or commented out in production for security.

✅ During development, enjoy the time saved by not checking email!

===============================================
FILES AFFECTED
===============================================

- login.php (main configuration at line 782)
- api/send-otp.php (returns OTP in response)
- api/resend-login-otp.php (returns OTP in response)
- api/test-otp.html (standalone testing page)

===============================================
TESTING PAGE
===============================================

For API testing, visit: api/test-otp.html
This page lets you test the send-otp.php and verify-otp.php endpoints directly.

===============================================



