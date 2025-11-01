# 🔧 Developer Mode Configuration

## Overview
Developer Mode allows you to bypass login authentication and directly access the dashboard during development and testing. This is extremely useful for rapid testing and development.

## ⚠️ IMPORTANT WARNING
**NEVER ENABLE DEVELOPER MODE IN PRODUCTION!**

Developer Mode completely bypasses authentication and security checks. Always disable it before deploying to production.

---

## Quick Start

### Enable Developer Mode
1. Open `admin/config/developer-mode.php`
2. Set `DEVELOPER_MODE` to `true`:
```php
define('DEVELOPER_MODE', true);
```

3. Access any admin page directly without login:
   - `http://localhost/admin/index.php` - Dashboard
   - `http://localhost/admin/user.php` - User Management
   - `http://localhost/admin/product.php` - Product Management
   - etc.

### Disable Developer Mode
1. Open `admin/config/developer-mode.php`
2. Set `DEVELOPER_MODE` to `false`:
```php
define('DEVELOPER_MODE', false);
```

---

## Configuration Options

### Main Settings

```php
// Enable/disable developer mode
define('DEVELOPER_MODE', true);  // true = enabled, false = disabled
```

### Auto-Login User Configuration

When Developer Mode is enabled, you'll be automatically logged in with these credentials:

```php
define('DEV_USER_ID', 1);                    // User ID
define('DEV_USERNAME', 'admin');             // Username
define('DEV_FULL_NAME', 'Developer Admin');  // Display name
define('DEV_EMAIL', 'dev@example.com');      // Email
define('DEV_ROLE_ID', 1);                    // 1 = Admin, 2 = Staff
define('DEV_STATUS', 'active');              // Account status
```

**Customize these values** to test different user roles:

#### Test as Admin:
```php
define('DEV_ROLE_ID', 1);  // Full admin access
```

#### Test as Staff:
```php
define('DEV_ROLE_ID', 2);  // Limited staff access
```

### Visual Banner

```php
define('SHOW_DEV_BANNER', true);  // Show yellow banner at top of pages
```

When enabled, a bright yellow banner appears at the top of all admin pages:
- Shows you're in developer mode
- Displays current logged-in user
- Reminds you to disable before production

---

## Features

### ✅ What Developer Mode Does:

1. **Skip Login Page**
   - No need to enter username/password
   - Direct access to all pages

2. **Bypass OTP Verification**
   - No email verification required
   - Instant access

3. **Auto-Login**
   - Automatically creates session
   - Sets up user credentials

4. **Visual Indicators**
   - Yellow banner shows developer mode is active
   - Console logs remind you it's enabled

5. **Role Testing**
   - Easily switch between Admin and Staff roles
   - Test different permission levels

### ❌ What Developer Mode Does NOT Affect:

- Database operations (still secure)
- API endpoints (still require valid data)
- File uploads (still validated)
- Business logic (works normally)

---

## Usage Examples

### Example 1: Test as Admin User
```php
// In admin/config/developer-mode.php
define('DEVELOPER_MODE', true);
define('DEV_USER_ID', 1);
define('DEV_ROLE_ID', 1);  // Admin
define('DEV_FULL_NAME', 'Test Admin');
```

Now visit `http://localhost/admin/index.php` - you're instantly logged in as admin!

### Example 2: Test as Staff User
```php
// In admin/config/developer-mode.php
define('DEVELOPER_MODE', true);
define('DEV_USER_ID', 2);
define('DEV_ROLE_ID', 2);  // Staff
define('DEV_FULL_NAME', 'Test Staff');
```

Now visit any admin page - you'll be redirected to collection panel (staff access only).

### Example 3: Test Without Banner
```php
// In admin/config/developer-mode.php
define('DEVELOPER_MODE', true);
define('SHOW_DEV_BANNER', false);  // Hide banner for clean screenshots
```

---

## Console Indicators

When Developer Mode is enabled, you'll see console messages:

```
🔧 DEVELOPER MODE ENABLED
✅ OTP auto-fill is ACTIVE
⚠️ Remember to set DEVELOPER_MODE = false for production!
```

When disabled (production):

```
🔒 PRODUCTION MODE
❌ OTP auto-fill is DISABLED
```

---

## Security Checklist

Before deploying to production:

- [ ] Set `DEVELOPER_MODE` to `false` in `developer-mode.php`
- [ ] Check console for production mode message
- [ ] Verify login page requires authentication
- [ ] Test OTP verification works
- [ ] Verify staff users can't access admin pages
- [ ] Check no yellow developer banner appears

---

## Troubleshooting

### "Still asking for login even with developer mode enabled"

**Solution:**
1. Clear browser cache and cookies
2. Check `admin/config/developer-mode.php` - ensure `DEVELOPER_MODE = true`
3. Check browser console for developer mode message
4. Try incognito/private browsing window

### "Session not persisting"

**Solution:**
1. Check PHP session configuration
2. Ensure session cookies are enabled
3. Clear browser data
4. Restart your local server (MAMP)

### "Yellow banner not showing"

**Solution:**
1. Check `SHOW_DEV_BANNER` is set to `true`
2. Clear browser cache
3. Hard refresh (Ctrl+Shift+R / Cmd+Shift+R)

---

## Production Deployment

### Pre-Deployment Checklist:

1. **Disable Developer Mode:**
```php
define('DEVELOPER_MODE', false);
```

2. **Verify Authentication:**
- Test login page works
- Test OTP verification
- Test role-based access control

3. **Remove Debug Code:**
- Remove console.log statements
- Disable error display
- Enable error logging only

4. **Test Security:**
- Verify unauthorized access is blocked
- Test session timeout
- Verify CSRF protection

---

## Support

If you encounter issues with Developer Mode:
1. Check this README
2. Review `admin/config/developer-mode.php` configuration
3. Check browser console for errors
4. Review PHP error logs

---

**Last Updated:** 2025-11-01
**Version:** 1.0.0

