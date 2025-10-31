# Setup Folder

This folder contains all database installation and setup utilities for the SAHANALK database.

## 📁 Contents

### Installation Scripts

- **`install.php`** - Main database installation script
  - Creates SAHANALK database
  - Installs all tables, triggers, stored procedures
  - Sets up default data
  - **Access:** `http://localhost:8888/Daily-Collection-Manager/setup/install.php`

- **`test-db-connection.php`** - Database connection tester
  - Verifies MySQL connection
  - Checks all tables exist
  - Tests default data
  - **Access:** `http://localhost:8888/Daily-Collection-Manager/setup/test-db-connection.php`

- **`database-status.php`** - Quick database status checker
  - Checks if MySQL is running
  - Verifies database exists
  - Shows table count
  - **Access:** `http://localhost:8888/Daily-Collection-Manager/setup/database-status.php`

### Documentation

- **`INSTALL_NOW.md`** - Quick installation guide
- **`database/README.md`** - Complete database schema documentation
- **`database/QUICK_START.md`** - Quick start guide for MAMP
- **`database/MAMP_SETUP_INSTRUCTIONS.md`** - Detailed MAMP setup instructions
- **`database/PASSWORD_MANAGEMENT.md`** - Password management guide

### Database Files

- **`database/database_schema.sql`** - Complete database schema SQL file (in `setup/database/`)
- **`database/change-password.php`** - Web-based password change utility
- **`database/generate-password-hash.php`** - Password hash generator tool

## 🚀 Quick Installation

1. **Start MAMP servers** (Apache & MySQL)

2. **Run installation:**
   ```
   http://localhost:8888/Daily-Collection-Manager/setup/install.php
   ```

3. **Verify installation:**
   ```
   http://localhost:8888/Daily-Collection-Manager/setup/test-db-connection.php
   ```

## 📋 Installation Steps

1. Make sure MAMP MySQL server is running
2. Open `install.php` in your browser
3. Click "Install Database"
4. Wait for completion
5. Test connection
6. **Delete or protect `install.php` after installation** (security)

## 🔐 Default Credentials

- **Username:** `admin`
- **Password:** `admin123`

⚠️ **Change password immediately after first login!**

## 🔗 Related Files

- Database config: `admin/config/db.php`
- Login page: `login.php` (one level up)
- Application root: `../` (parent directory)

## 📝 Notes

- All paths in these files are relative to this `setup/` folder
- The `database/` subfolder contains SQL schema and utilities
- After installation, you can safely delete `install.php` or move it outside web root
- Keep documentation files for reference

---

**Need Help?** Check `database/MAMP_SETUP_INSTRUCTIONS.md` for detailed instructions.

