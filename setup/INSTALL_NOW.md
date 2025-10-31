# 🚀 Install SAHANALK Database Now

## Quick Installation Steps

### Step 1: Make sure MAMP is running
1. Open MAMP application
2. Click **"Start Servers"**
3. Wait for Apache and MySQL to show green lights ✅

### Step 2: Open Installation Page

Open in your web browser:
```
http://localhost:8888/Daily-Collection-Manager/setup/install.php
```

**Note:** If MAMP uses a different port, adjust accordingly:
- Apache port might be: `8888` or `80`
- MySQL port might be: `8889` or `3306`

### Step 3: Install Database

1. You'll see the installation page with database configuration
2. Verify the settings (should be correct for MAMP)
3. Click **"🚀 Install Database"** button
4. Wait for installation to complete (progress bar will show)
5. You should see "✅ Installation Successful!" message

### Step 4: Verify Installation

Test the database connection:
```
http://localhost:8888/Daily-Collection-Manager/setup/test-db-connection.php
```

You should see all 14 tables listed and verified.

### Step 5: Delete Install Script (Security)

After successful installation, delete or protect `setup/install.php`:
- Delete the file, OR
- Rename it to `install.php.bak`, OR  
- Move it outside the web root

---

## Troubleshooting

### "Can't connect to MySQL"
- ✅ Make sure MAMP MySQL server is running
- ✅ Check MAMP → Preferences → Ports for MySQL port
- ✅ Verify username: `root`, password: `root`

### "Port 8889 failed, try 3306"
- If port 8889 doesn't work, MAMP might use port 3306
- Update `admin/config/db.php` and set `$DB_PORT = '3306'`

### "Database already exists"
- That's OK! The script will continue
- Or drop it first in phpMyAdmin: `DROP DATABASE SAHANALK;`

---

## After Installation

**Login Credentials:**
- Username: `admin`
- Password: `admin123`

**⚠️ CHANGE PASSWORD IMMEDIATELY!**

Change password using:
- `setup/database/change-password.php` (web interface)
- `setup/database/generate-password-hash.php` (generate SQL)

---

**Ready?** Open `install.php` in your browser now! 🎯

