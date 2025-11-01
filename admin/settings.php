<?php
// Admin-only page - requires admin role
require_once __DIR__ . '/config/admin-auth.php';

// Get user information from session
$full_name = $_SESSION['full_name'] ?? 'User';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">
    <title>Settings - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Settings - Daily Collection Manager">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#10b981",
              "background-light": "#f7fafc",
              "background-dark": "#1a202c",
              "card-light": "#ffffff",
              "card-dark": "#2d3748",
              "text-light": "#4a5568",
              "text-dark": "#a0aec0",
              "heading-light": "#1a202c",
              "heading-dark": "#f7fafc",
              "border-light": "#e2e8f0",
              "border-dark": "#4a5568",
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
            },
            borderRadius: {
              lg: "0.75rem",
            },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
  </head>
  <body class="bg-background-light">
    <div class="flex h-screen">
      <?php $activePage = 'settings'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu" aria-controls="mobileSidebar" aria-expanded="false">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Settings</h2>
          <div class="flex items-center gap-4">
            <button class="text-text-light">
              <span class="material-icons">notifications</span>
            </button>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                <span class="material-icons text-text-light">person</span>
              </div>
              <div>
                <p class="font-semibold text-heading-light" id="userName"><?php echo htmlspecialchars($full_name); ?></p>
                <p class="text-sm text-text-light">Admin</p>
              </div>
            </div>
          </div>
        </header>
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
          <!-- Settings Tabs -->
          <div class="bg-card-light rounded-lg border border-border-light mb-6">
            <div class="flex flex-wrap">
              <button data-tab="general" class="settings-tab px-5 py-3 text-sm font-semibold text-primary bg-primary/10 border-b-2 border-primary">
                General
              </button>
              <button data-tab="notify" class="settings-tab px-5 py-3 text-sm font-semibold text-text-light hover:text-primary hover:bg-primary/5">
                Notifications
              </button>
              <button data-tab="system" class="settings-tab px-5 py-3 text-sm font-semibold text-text-light hover:text-primary hover:bg-primary/5">
                System
              </button>
              <button data-tab="backup" class="settings-tab px-5 py-3 text-sm font-semibold text-text-light hover:text-primary hover:bg-primary/5">
                Backup
              </button>
            </div>
          </div>

          <!-- General Settings -->
          <div id="tab-general" class="bg-card-light p-6 rounded-lg border border-border-light mb-6">
            <h3 class="text-lg font-semibold text-heading-light mb-6">General Settings</h3>
            <div class="space-y-6">
              <!-- Company Name -->
              <div>
                <label for="companyName" class="block text-sm font-medium text-heading-light mb-2">Company Name</label>
                <input 
                  type="text" 
                  id="companyName" 
                  value="Daily Collection Manager"
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                >
              </div>

              <!-- Email -->
              <div>
                <label for="companyEmail" class="block text-sm font-medium text-heading-light mb-2">Company Email</label>
                <input 
                  type="email" 
                  id="companyEmail" 
                  value="info@dailycollection.com"
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                >
              </div>

              <!-- Phone -->
              <div>
                <label for="companyPhone" class="block text-sm font-medium text-heading-light mb-2">Company Phone</label>
                <input 
                  type="tel" 
                  id="companyPhone" 
                  value="+94 77 123 4567"
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
                >
              </div>

              <!-- Address -->
              <div>
                <label for="companyAddress" class="block text-sm font-medium text-heading-light mb-2">Company Address</label>
                <textarea 
                  id="companyAddress" 
                  rows="3"
                  class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"
                >123 Main Street, Colombo, Sri Lanka</textarea>
              </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-4">
              <button id="saveGeneralBtn" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                <span class="material-icons">save</span>
                Save General
              </button>
            </div>
          </div>

          <!-- Notification Settings -->
          <div id="tab-notify" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
            <h3 class="text-lg font-semibold text-heading-light mb-6">Notification Settings</h3>
            <div class="space-y-4">
              <!-- Email Notifications -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <label for="emailNotifications" class="block text-sm font-medium text-heading-light">Email Notifications</label>
                  <p class="text-xs text-text-light mt-1">Receive email alerts for important events</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="emailNotifications" checked>
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Payment Reminders -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <label for="paymentReminders" class="block text-sm font-medium text-heading-light">Payment Reminders</label>
                  <p class="text-xs text-text-light mt-1">Send automatic payment reminders</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="paymentReminders" checked>
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Low Stock Alerts -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <label for="lowStockAlerts" class="block text-sm font-medium text-heading-light">Low Stock Alerts</label>
                  <p class="text-xs text-text-light mt-1">Notify when product stock is low</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="lowStockAlerts" checked>
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-4">
              <button id="saveNotifyBtn" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                <span class="material-icons">save</span>
                Save Notifications
              </button>
            </div>
          </div>

          <!-- System Settings -->
          <div id="tab-system" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
            <h3 class="text-lg font-semibold text-heading-light mb-6">System Settings</h3>
            <div class="space-y-6">
              <!-- Currency -->
              <div>
                <label for="currency" class="block text-sm font-medium text-heading-light mb-2">Currency</label>
                <div class="custom-select-wrapper relative">
                  <select 
                    id="currency" 
                    class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="LKR" selected>LKR - Sri Lankan Rupee</option>
                    <option value="USD">USD - US Dollar</option>
                    <option value="EUR">EUR - Euro</option>
                  </select>
                  <span class="select-arrow material-icons">expand_more</span>
                </div>
              </div>

              <!-- Date Format -->
              <div>
                <label for="dateFormat" class="block text-sm font-medium text-heading-light mb-2">Date Format</label>
                <div class="custom-select-wrapper relative">
                  <select 
                    id="dateFormat" 
                    class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="Y-m-d" selected>YYYY-MM-DD</option>
                    <option value="d-m-Y">DD-MM-YYYY</option>
                    <option value="m/d/Y">MM/DD/YYYY</option>
                    <option value="d/m/Y">DD/MM/YYYY</option>
                  </select>
                  <span class="select-arrow material-icons">expand_more</span>
                </div>
              </div>

              <!-- Timezone -->
              <div>
                <label for="timezone" class="block text-sm font-medium text-heading-light mb-2">Timezone</label>
                <div class="custom-select-wrapper relative">
                  <select 
                    id="timezone" 
                    class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white cursor-pointer hover:border-primary/50 transition-colors"
                  >
                    <option value="Asia/Colombo" selected>Asia/Colombo (IST)</option>
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">America/New_York (EST)</option>
                  </select>
                  <span class="select-arrow material-icons">expand_more</span>
                </div>
              </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-4">
              <button id="saveSystemBtn" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                <span class="material-icons">save</span>
                Save System
              </button>
            </div>
          </div>

          <!-- Backup & Maintenance -->
          <div id="tab-backup" class="bg-card-light p-6 rounded-lg border border-border-light mb-6 hidden">
            <h3 class="text-lg font-semibold text-heading-light mb-6">Backup & Maintenance</h3>
            <div class="space-y-4">
              <!-- Auto Backup -->
              <div class="flex items-center justify-between py-3">
                <div>
                  <label for="autoBackup" class="block text-sm font-medium text-heading-light">Automatic Backup</label>
                  <p class="text-xs text-text-light mt-1">Automatically backup database daily</p>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="autoBackup" checked>
                  <span class="toggle-slider"></span>
                </label>
              </div>

              <!-- Backup Now Button -->
              <div class="flex items-center justify-between pt-4 border-t border-border-light">
                <div>
                  <p class="text-sm font-medium text-heading-light">Manual Backup</p>
                  <p class="text-xs text-text-light mt-1">Last backup: Never</p>
                </div>
                <button id="backupNowBtn" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                  <span class="material-icons text-lg">backup</span>
                  Backup Now
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
      <!-- mobile backdrop -->
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <button id="installBtn" class="fixed bottom-4 right-4 bg-primary text-white px-4 py-3 rounded-lg shadow-lg hidden">Install app</button>
    <script src="js/app.js?v=15" defer></script>
    <script>
      // Sidebar toggle functionality
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mobileSidebar = document.getElementById('mobileSidebar');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');

      sidebarToggle.addEventListener('click', function() {
        const isExpanded = sidebarToggle.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
          // Close sidebar
          mobileSidebar.classList.add('-translate-x-full');
          sidebarBackdrop.classList.add('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'false');
        } else {
          // Open sidebar
          mobileSidebar.classList.remove('-translate-x-full');
          sidebarBackdrop.classList.remove('hidden');
          sidebarToggle.setAttribute('aria-expanded', 'true');
        }
      });

      // Close sidebar when clicking backdrop
      sidebarBackdrop.addEventListener('click', function() {
        mobileSidebar.classList.add('-translate-x-full');
        sidebarBackdrop.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
      });

      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
          const isSidebarOpen = sidebarToggle.getAttribute('aria-expanded') === 'true';
          const isClickInsideSidebar = mobileSidebar.contains(event.target);
          const isClickOnToggle = sidebarToggle.contains(event.target);
          
          if (isSidebarOpen && !isClickInsideSidebar && !isClickOnToggle) {
            mobileSidebar.classList.add('-translate-x-full');
            sidebarBackdrop.classList.add('hidden');
            sidebarToggle.setAttribute('aria-expanded', 'false');
          }
        }
      });

      // Tabs: switch content
      function setActiveTab(tab) {
        const tabs = ['general','sms','smtp','notify','system','backup'];
        tabs.forEach(t => {
          const btn = document.querySelector(`.settings-tab[data-tab="${t}"]`);
          const panel = document.getElementById(`tab-${t}`);
          if (!btn || !panel) return;
          if (t === tab) {
            btn.classList.add('text-primary','bg-primary/10','border-b-2','border-primary');
            btn.classList.remove('text-text-light');
            panel.classList.remove('hidden');
          } else {
            btn.classList.remove('text-primary','bg-primary/10','border-b-2','border-primary');
            btn.classList.add('text-text-light');
            panel.classList.add('hidden');
          }
        });
      }
      document.querySelectorAll('.settings-tab').forEach(b => {
        b.addEventListener('click', () => setActiveTab(b.dataset.tab));
      });
      // Init default tab
      setActiveTab('general');

      // Load settings from database when page loads
      async function loadSettings() {
        try {
          // Load all settings
          const response = await fetch('api/get-settings.php?category=all');
          const result = await response.json();
          
          if (result.success && result.settings) {
            const settings = result.settings;
            
            // Load General settings
            if (settings.company_name) document.getElementById('companyName').value = settings.company_name;
            if (settings.company_email) document.getElementById('companyEmail').value = settings.company_email;
            if (settings.company_phone) document.getElementById('companyPhone').value = settings.company_phone;
            if (settings.company_address) document.getElementById('companyAddress').value = settings.company_address;
            
            // Load Notification settings
            if (settings.email_notifications !== undefined) document.getElementById('emailNotifications').checked = settings.email_notifications;
            if (settings.payment_reminders !== undefined) document.getElementById('paymentReminders').checked = settings.payment_reminders;
            if (settings.low_stock_alerts !== undefined) document.getElementById('lowStockAlerts').checked = settings.low_stock_alerts;
            
            // Load System settings
            if (settings.currency) document.getElementById('currency').value = settings.currency;
            if (settings.date_format) document.getElementById('dateFormat').value = settings.date_format;
            if (settings.timezone) document.getElementById('timezone').value = settings.timezone;
          }
        } catch (error) {
          console.error('Failed to load settings:', error);
        }
      }

      // Load settings when page loads
      loadSettings();

      // Notification function
      function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 ${
          type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
          <span class="material-icons">${type === 'success' ? 'check_circle' : 'error'}</span>
          <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
          notification.style.transition = 'opacity 0.3s';
          notification.style.opacity = '0';
          setTimeout(() => notification.remove(), 300);
        }, 3000);
      }

      // Section-specific save handlers
      document.getElementById('saveGeneralBtn').addEventListener('click', function() {
        const payload = {
          companyName: document.getElementById('companyName').value,
          companyEmail: document.getElementById('companyEmail').value,
          companyPhone: document.getElementById('companyPhone').value,
          companyAddress: document.getElementById('companyAddress').value
        };
        console.log('Save General:', payload);
        alert('General settings saved');
      });

      document.getElementById('saveNotifyBtn').addEventListener('click', async function() {
        const saveBtn = document.getElementById('saveNotifyBtn');
        const originalText = saveBtn.innerHTML;
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-icons animate-spin">hourglass_empty</span> Saving...';
        
        const payload = {
          category: 'notifications',
          emailNotifications: document.getElementById('emailNotifications').checked,
          paymentReminders: document.getElementById('paymentReminders').checked,
          lowStockAlerts: document.getElementById('lowStockAlerts').checked
        };
        
        try {
          const response = await fetch('api/save-settings.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('Notification settings saved successfully!', 'success');
          } else {
            showNotification(result.error || 'Failed to save notification settings', 'error');
          }
        } catch (error) {
          console.error('Save Notifications error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
        }
      });

      document.getElementById('saveSystemBtn').addEventListener('click', async function() {
        const saveBtn = document.getElementById('saveSystemBtn');
        const originalText = saveBtn.innerHTML;
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="material-icons animate-spin">hourglass_empty</span> Saving...';
        
        const payload = {
          category: 'system',
          currency: document.getElementById('currency').value,
          dateFormat: document.getElementById('dateFormat').value,
          timezone: document.getElementById('timezone').value
        };
        
        try {
          const response = await fetch('api/save-settings.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
          });
          
          const result = await response.json();
          
          if (result.success) {
            showNotification('System settings saved successfully!', 'success');
          } else {
            showNotification(result.error || 'Failed to save system settings', 'error');
          }
        } catch (error) {
          console.error('Save System error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalText;
        }
      });

      // Backup Now button
      document.getElementById('backupNowBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to create a backup now?')) {
          // Here you would typically trigger backup via AJAX
          alert('Backup started. You will be notified when complete.');
        }
      });

    </script>
  </body>
</html>

