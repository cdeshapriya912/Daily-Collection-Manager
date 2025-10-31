# 🚀 Quick Start Guide - SAHANALK Database Setup

## For MAMP Users (Recommended)

### Step 1: Start MAMP
1. Open MAMP application
2. Click "Start Servers"
3. Wait for Apache and MySQL to start (green lights)

### Step 2: Install Database
Open in browser:
```
http://localhost:8888/Daily-Collection-Manager/setup/install.php
```

Click **"Install Database"** button and wait for completion.

### Step 3: Verify Installation
Open in browser:
```
http://localhost:8888/Daily-Collection-Manager/test-db-connection.php
```

You should see all tables created successfully.

### Step 4: Login
Go to:
```
http://localhost:8888/Daily-Collection-Manager/login.php
```

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **Change password immediately after first login!**

**Change Password:**
- Web tool: `database/change-password.php`
- Hash generator: `database/generate-password-hash.php`
- Passwords use secure **BCrypt** encryption

---

## Troubleshooting

### Port Issues
If MAMP uses a different port:
- Check MAMP → Preferences → Ports
- Update `admin/config/db.php`:
  ```php
  $DB_PORT = '8889'; // Your MAMP MySQL port
  ```

### Connection Failed
1. Make sure MAMP MySQL is running
2. Check username/password (default: root/root)
3. Verify port number

### Tables Missing
Run `setup/install.php` again or manually import `setup/database/database_schema.sql` via phpMyAdmin

---

## Files Created

✅ `database/database_schema.sql` - Complete database schema  
✅ `setup/install.php` - Automated installation script  
✅ `setup/test-db-connection.php` - Connection test utility  
✅ `database/MAMP_SETUP_INSTRUCTIONS.md` - Detailed setup guide  

---

**Need Help?** Check `database/MAMP_SETUP_INSTRUCTIONS.md` for detailed instructions.

