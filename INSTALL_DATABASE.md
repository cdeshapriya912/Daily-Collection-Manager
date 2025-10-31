# 🚀 Install Database - Quick Guide

## The Problem
The database `SAHANALK` doesn't exist yet. You need to create it first before you can login.

## Solution: Run the Installation Script

### Step 1: Make sure MAMP is running
1. Open the **MAMP** application
2. Click **"Start Servers"** button
3. Wait for both **Apache** and **MySQL** to show **green lights** ✅

### Step 2: Open the installation page
Open this URL in your web browser:

```
http://localhost:8888/Daily-Collection-Manager/setup/install.php
```

**Note:** If MAMP uses a different port, adjust:
- Check your Apache port in MAMP → Preferences → Ports
- Common ports: `8888`, `80`, or `8080`
- Adjust the URL accordingly: `http://localhost:YOUR_PORT/Daily-Collection-Manager/setup/install.php`

### Step 3: Install the database
1. You'll see the installation page
2. Verify the database settings (should be pre-filled for MAMP):
   - **Host:** 127.0.0.1
   - **Port:** 8889 (or your MAMP MySQL port)
   - **Username:** root
   - **Password:** root
   - **Database Name:** SAHANALK
3. Click the **"🚀 Install Database"** button
4. Wait for installation to complete (progress bar will show)
5. You should see **"✅ Installation Complete!"** message

### Step 4: Login
After installation, go back to:
```
http://localhost:8888/Daily-Collection-Manager/login.php
```

**Default Login Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT:** Change the password immediately after first login!

---

## Troubleshooting

### "Can't connect to MySQL"
- ✅ Make sure MAMP MySQL server is running (green light in MAMP)
- ✅ Check MAMP → Preferences → Ports for MySQL port
- ✅ Try port `3306` if `8889` doesn't work
- ✅ Update `admin/config/db.php` if port is different

### "Database already exists"
- That's OK! The script will continue
- Or drop it first: `DROP DATABASE SAHANALK;` in phpMyAdmin

### Still having issues?
Run the diagnostic script:
```
http://localhost:8888/Daily-Collection-Manager/test-login.php
```

This will show exactly what's wrong.

