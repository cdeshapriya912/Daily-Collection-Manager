# 🔧 Fix: Login Issue - MySQL Connection Refused

## Problem
You're getting this error:
```
SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it
```

This means **MySQL server is not running**.

## ✅ Solution

### Step 1: Start MAMP MySQL Server
1. **Open MAMP application**
2. **Click "Start Servers"** button
3. **Wait for both Apache and MySQL to show GREEN lights** ✅

### Step 2: Verify MySQL is Running
Check that MySQL shows a green status indicator in the MAMP window.

### Step 3: Test Connection
Open this diagnostic tool in your browser:
```
http://localhost/www/Daily-Collection-Manager/check-mysql.php
```

This will:
- Test multiple ports (8889, 3306, 3307)
- Find which port MySQL is using
- Check if database exists
- Give you specific instructions

### Step 4: After MySQL is Running
Once MySQL is running, the login should work. If the database doesn't exist yet, you'll see a message with a link to run the installation.

---

## What I Fixed

1. **Auto-detection of MySQL port** - The code now tries multiple ports automatically
2. **Better error messages** - Clear instructions on how to fix the issue
3. **Diagnostic tool** - Created `check-mysql.php` to help identify connection problems
4. **Improved error handling** - Better detection of connection vs database errors

---

## Quick Actions

1. **Start MAMP MySQL** ← Most important!
2. **Run diagnostic**: `check-mysql.php`
3. **Install database**: `setup/install.php` (if needed)
4. **Login**: `login.php` (username: `admin`, password: `admin123`)

---

## Common Issues

### "Port 8889 doesn't work"
- Check MAMP → Preferences → Ports
- The code now tries 8889, 3306, and 3307 automatically

### "MySQL won't start"
- Check if another MySQL instance is running
- Check MAMP logs for errors
- Try restarting MAMP

### "Database doesn't exist"
- Run `setup/install.php` to create it
- Or use phpMyAdmin to import `setup/database/database_schema.sql`

