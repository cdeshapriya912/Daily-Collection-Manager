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
    <title>Add New Customer - Daily Collection</title>
    <link rel="manifest" href="manifest.webmanifest?v=10">
    <link rel="apple-touch-icon" href="img/package.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" href="img/package.png" type="image/png">
    <meta name="description" content="Add New Customer - Daily Collection Manager">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#10b981",
              "background-light": "#f7fafc",
              "card-light": "#ffffff",
              "text-light": "#4a5568",
              "heading-light": "#1a202c",
              "border-light": "#e2e8f0",
            },
            fontFamily: { sans: ["Roboto", "sans-serif"] },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="assets/css/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/components.css?v=<?php echo time(); ?>">
    <style>
      /* Circular progress badge */
      .progress-ring {
        --progress: 0%;
        --ring-color: #ef4444;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: conic-gradient(var(--ring-color) var(--progress), #e5e7eb 0%);
        display: grid;
        place-items: center;
        position: relative;
        transition: --progress 300ms ease, --ring-color 300ms ease;
      }

      .progress-ring::before {
        content: '';
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #ffffff;
      }

      .progress-ring span {
        position: relative;
        font-weight: 700;
        font-size: 16px;
      }

      @property --progress {
        syntax: '<percentage>';
        inherits: false;
        initial-value: 0%;
      }
      @property --ring-color {
        syntax: '<color>';
        inherits: false;
        initial-value: #ef4444;
      }

      .dropzone {
        position: relative;
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        height: 200px;
        display: grid;
        place-items: center;
        color: #9ca3af;
        cursor: pointer;
        transition: border-color .15s ease, background-color .15s ease;
        overflow: hidden;
      }
      .dropzone.dragover { border-color: #10b981; background: #f0fdf4; }
      .dropzone .dz-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; border-radius: 10px; background: #ffffff; }
      .dropzone .dz-cta { position: absolute; bottom: 10px; left: 10px; right: 10px; display: flex; justify-content: space-between; align-items: center; z-index: 2; gap: 8px; }
      .dropzone .dz-title { font-weight: 600; color: #6b7280; font-size: 14px; }
      .dropzone .dz-btn { background:#10b981; color:#fff; padding:6px 10px; border-radius:8px; font-weight:600; font-size:12px; border:0; cursor:pointer; }
      .dropzone span:not(.dz-title) { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1; }

      .camera-modal { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: none; align-items: center; justify-content: center; z-index: 100; }
      .camera-card { background: #fff; width: min(960px, 92vw); border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.2); padding: 16px; }
      .video-wrap { position: relative; background: #000; border-radius: 12px; overflow: hidden; height: 420px; }
      .video-wrap video, .video-wrap canvas { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; }
      .camera-actions { display: flex; gap: 10px; justify-content: space-between; margin-top: 12px; }
      .btn { background:#10b981; color:#fff; padding:10px 14px; border-radius:10px; font-weight:600; border:0; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; min-width:48px; min-height:48px; }
      .btn.gray { background:#6b7280; }
      .btn svg { width:20px; height:20px; fill:currentColor; }

      input[type="file"] { display: none; }
    </style>
  </head>
  <body class="bg-background-light">
    <?php echo getDeveloperBanner(); ?>
    <div class="flex h-screen">
      <?php $activePage = 'add-customer'; $activeSubPage = 'add-customer'; include __DIR__ . '/partials/menu.php'; ?>
      <div class="flex-1 flex flex-col">
        <header class="flex items-center justify-between p-6 border-b border-border-light">
          <button id="sidebarToggle" class="md:hidden text-text-light" aria-label="Toggle menu">
            <span class="material-icons">menu</span>
          </button>
          <h2 class="text-2xl font-bold text-heading-light">Add New Customer</h2>
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
          <div class="bg-card-light p-6 rounded-lg border border-border-light max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-heading-light">Customer Registration Form</h3>
              <div class="flex items-center gap-3">
                <div class="progress-ring" id="progressRing" style="--progress: 0%; --ring-color: #ef4444;">
                  <span id="progressPercent">0%</span>
                </div>
                <span class="text-sm text-text-light font-medium">Completed</span>
              </div>
            </div>

            <form id="registrationForm" autocomplete="off">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                  <label for="first_name" class="block text-sm font-medium text-heading-light mb-2">First Name (මුල් නම) <span class="text-red-500">*</span></label>
                  <input type="text" id="first_name" name="first_name" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block text-sm font-medium text-heading-light mb-2">Last Name (අවසාන නම) <span class="text-red-500">*</span></label>
                  <input type="text" id="last_name" name="last_name" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Full Name with Surname -->
                <div class="md:col-span-2">
                  <label for="full_name" class="block text-sm font-medium text-heading-light mb-2">Full name with surname (වාසගම සහිත සම්පූර්ණ නම)</label>
                  <input type="text" id="full_name" name="full_name" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Phone Number -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-heading-light mb-2">Phone Number (0771230000) <span class="text-red-500">*</span></label>
                  <input type="tel" id="phone" name="phone" inputmode="numeric" maxlength="10" pattern="\d{10}" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Email Address -->
                <div>
                  <label for="email" class="block text-sm font-medium text-heading-light mb-2">Email Address <span class="text-red-500">*</span></label>
                  <input type="email" id="email" name="email" inputmode="email" pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Permanent Address -->
                <div class="md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-heading-light mb-2">Permanent Address <span class="text-red-500">*</span></label>
                  <textarea id="address" name="address" rows="3" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none"></textarea>
                </div>

                <!-- GND -->
                <div>
                  <label for="gnd" class="block text-sm font-medium text-heading-light mb-2">Grama Niladari Division (ග්‍රාම නිලධාරී වසම)</label>
                  <input type="text" id="gnd" name="gnd" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- LGI -->
                <div>
                  <label for="lgi" class="block text-sm font-medium text-heading-light mb-2">Local Government Institutions (පළාත් පාලන ආයතන)</label>
                  <input type="text" id="lgi" name="lgi" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Police Station -->
                <div>
                  <label for="police_station" class="block text-sm font-medium text-heading-light mb-2">Police station (පොලිස් ස්ථානය)</label>
                  <input type="text" id="police_station" name="police_station" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- NIC ID -->
                <div>
                  <label for="nic" class="block text-sm font-medium text-heading-light mb-2">NIC ID Number (ජාතික හැඳුනුම්පත් අංකය) <span class="text-red-500">*</span></label>
                  <input type="text" id="nic" name="nic" maxlength="12" pattern="(?:\d{12}|\d{9}[VvXx])" title="Enter 12 digits (new NIC) or 9 digits plus V/X (old NIC)" required class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                  <p class="text-xs text-text-light mt-1">12 digits (new) or 9 digits + V/X (old)</p>
                </div>

                <!-- Occupation -->
                <div>
                  <label for="occupation" class="block text-sm font-medium text-heading-light mb-2">Permanent Occupation (ස්ථිර රැකියාව)</label>
                  <input type="text" id="occupation" name="occupation" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>

                <!-- Residence Period -->
                <div>
                  <label for="residence_period" class="block text-sm font-medium text-heading-light mb-2">Period address (ඉහත ලිපිනෙහි පදිංචි කාලය)</label>
                  <input type="text" id="residence_period" name="residence_period" class="w-full px-4 py-3 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
              </div>

              <!-- Document Upload Section -->
              <div class="mt-6">
                <label class="block text-sm font-medium text-heading-light mb-2">Document Upload</label>
                <p class="text-xs text-text-light mb-4">Supported formats: JPG, PNG, GIF (Max 2MB). Drag & drop or browse.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- NIC Front -->
                  <div class="dropzone" id="dzNicFront">
                    <span>NIC Front preview</span>
                    <div class="dz-cta">
                      <span class="dz-title">NIC ID Front</span>
                      <div class="flex gap-2">
                        <button type="button" class="dz-btn dz-camera">Camera</button>
                        <button type="button" class="dz-btn">Browse</button>
                      </div>
                    </div>
                    <input id="nicFront" type="file" accept="image/jpeg,image/png,image/gif">
                  </div>
                  <!-- NIC Back -->
                  <div class="dropzone" id="dzNicBack">
                    <span>NIC Back preview</span>
                    <div class="dz-cta">
                      <span class="dz-title">NIC ID Back</span>
                      <div class="flex gap-2">
                        <button type="button" class="dz-btn dz-camera">Camera</button>
                        <button type="button" class="dz-btn">Browse</button>
                      </div>
                    </div>
                    <input id="nicBack" type="file" accept="image/jpeg,image/png,image/gif">
                  </div>
                  <!-- Customer Photo -->
                  <div class="dropzone" id="dzCustomerPhoto">
                    <span>Photo preview</span>
                    <div class="dz-cta">
                      <span class="dz-title">Customer Photo</span>
                      <div class="flex gap-2">
                        <button type="button" class="dz-btn dz-camera">Camera</button>
                        <button type="button" class="dz-btn">Browse</button>
                      </div>
                    </div>
                    <input id="customerPhoto" type="file" accept="image/jpeg,image/png,image/gif">
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="mt-6 flex items-center justify-end gap-4">
                <button type="button" onclick="window.location.href='customer.php'" class="px-6 py-3 border border-border-light text-text-light rounded-lg hover:bg-gray-50 transition-colors">
                  Cancel
                </button>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                  <span class="material-icons">save</span>
                  Save Customer
                </button>
              </div>
            </form>
          </div>
        </main>
      </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal" class="camera-modal" aria-hidden="true">
      <div class="camera-card">
        <div class="video-wrap">
          <video id="cameraVideo" autoplay playsinline></video>
          <canvas id="cameraCanvas" style="display:none;"></canvas>
        </div>
        <div class="camera-actions">
          <div style="display:flex; gap:10px;">
            <button id="cameraFlip" type="button" class="btn gray" title="Flip camera">
              <svg viewBox="0 0 24 24"><path d="M20 5h-3.17L15 3H9L7.17 5H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v-2.09c-2.83-.48-5-2.94-5-5.91 0-3.31 2.69-6 6-6s6 2.69 6 6c0 2.97-2.17 5.43-5 5.91V21h7c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm-8 13c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17l-1.5-1.5L9 17l3-3 3 3-1.5-1.5L12 17zm0-10l1.5 1.5L15 7l-3 3-3-3 1.5 1.5L12 7z"/></svg>
            </button>
            <button id="cameraCapture" type="button" class="btn" title="Capture">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.2"/><path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>
            </button>
          </div>
          <div style="display:flex; gap:10px;">
            <button id="cameraUse" type="button" class="btn" title="Use photo">
              <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
            </button>
            <button id="cameraClose" type="button" class="btn gray" title="Close">
              <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>
            </button>
          </div>
        </div>
      </div>
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-30 hidden md:hidden"></div>
    </div>
    <script src="js/app.js?v=15" defer></script>
    <script src="assets/js/notification-dialog.js"></script>
    <script src="assets/js/sidebar.js?v=<?php echo time(); ?>"></script>
    <script>
      const form = document.getElementById('registrationForm');
      const ring = document.getElementById('progressRing');
      const percentText = document.getElementById('progressPercent');

      // Required fields for progress calculation
      const first_nameInput = document.getElementById('first_name');
      const last_nameInput = document.getElementById('last_name');
      const phoneInput = document.getElementById('phone');
      const emailInput = document.getElementById('email');
      const addressInput = document.getElementById('address');
      const nicInput = document.getElementById('nic');

      const requiredInputs = [
        first_nameInput,
        last_nameInput,
        phoneInput,
        emailInput,
        addressInput,
        nicInput
      ].filter(Boolean);

      const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
      const nicRegex = /^(?:\d{12}|\d{9}[VX])$/;

      function percentToColor(p) {
        const hue = Math.round((120 * p) / 100);
        return `hsl(${hue}, 80%, 45%)`;
      }

      function isValidField(input) {
        const value = (input.value || '').trim();
        
        // Empty check
        if (value === '') return false;

        // Field-specific validation
        if (input === phoneInput) {
          // Phone: count as valid if it contains only digits (progress tracking)
          // The phone input handler automatically strips non-digits, so value should already be clean
          // Just check if it has at least one digit
          return /^\d+$/.test(value) && value.length > 0;
        }
        
        if (input === emailInput) {
          // Email: count as valid if it looks like an email (has @ and .)
          // More lenient for progress tracking
          return value.includes('@') && value.includes('.') && value.length > 5;
        }
        
        if (input === nicInput) {
          // NIC: count as valid if it contains only digits and/or V/X (case insensitive)
          // Progress tracking - more lenient
          const cleaned = value.toUpperCase().replace(/[^0-9VX]/g, '');
          const originalCleaned = value.replace(/[^0-9VXvx]/gi, '');
          return cleaned.length > 0 && originalCleaned.length === value.length;
        }

        // For other fields (first_name, last_name, address), just check if not empty
        return value !== '';
      }

      function updateProgress() {
        let filled = 0;
        requiredInputs.forEach(input => {
          if (isValidField(input)) {
            filled++;
          }
        });
        const total = requiredInputs.length;
        const p = total === 0 ? 0 : Math.round((filled / total) * 100);
        ring.style.setProperty('--progress', p + '%');
        ring.style.setProperty('--ring-color', percentToColor(p));
        percentText.textContent = p + '%';
      }

      // Set up input handlers with sanitization FIRST, then update progress
      phoneInput.addEventListener('input', () => {
        const digitsOnly = (phoneInput.value || '').replace(/\D/g, '').slice(0, 10);
        if (phoneInput.value !== digitsOnly) phoneInput.value = digitsOnly;
        phoneInput.setCustomValidity('');
        updateProgress(); // Update progress after sanitization
      });

      emailInput.addEventListener('input', () => {
        emailInput.setCustomValidity('');
        updateProgress(); // Update progress on input
      });

      nicInput.addEventListener('input', () => {
        let v = (nicInput.value || '').toUpperCase().replace(/[^0-9A-Z]/g, '');
        if (v.length > 12) v = v.slice(0, 12);
        if (nicInput.value !== v) nicInput.value = v;
        nicInput.setCustomValidity('');
        updateProgress(); // Update progress after sanitization
      });

      // Add general listeners for other fields (first_name, last_name, address)
      [first_nameInput, last_nameInput, addressInput].forEach(el => {
        if (el) {
          el.addEventListener('input', updateProgress);
          el.addEventListener('change', updateProgress);
        }
      });

      updateProgress();

      // File upload previews with validation
      const MAX_SIZE = 2 * 1024 * 1024;
      const allowedTypes = ['image/jpeg','image/png','image/gif'];

      function bindDropzone(dzId, inputId) {
        const dz = document.getElementById(dzId);
        const input = dz.querySelector(`#${inputId}`);
        const browseBtn = dz.querySelector('.dz-btn:not(.dz-camera)');

        function showPreview(file) {
          if (!allowedTypes.includes(file.type)) { alert('Only JPG, PNG, or GIF images are allowed.'); return; }
          if (file.size > MAX_SIZE) { alert('File must be 2MB or smaller.'); return; }
          const reader = new FileReader();
          reader.onload = e => {
            const existing = dz.querySelector('.dz-img');
            if (existing) existing.remove();
            const placeholder = dz.querySelector('span');
            if (placeholder) placeholder.style.display = 'none';
            const img = document.createElement('img');
            img.className = 'dz-img';
            img.alt = 'preview';
            img.src = e.target.result;
            dz.insertBefore(img, dz.firstChild);
          };
          reader.readAsDataURL(file);
        }

        function pickFile() { input.click(); }

        dz.addEventListener('click', pickFile);
        browseBtn.addEventListener('click', (e) => { e.stopPropagation(); pickFile(); });

        input.addEventListener('change', () => {
          const file = input.files && input.files[0];
          if (file) showPreview(file);
        });

        dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dragover'); });
        dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
        dz.addEventListener('drop', (e) => {
          e.preventDefault(); dz.classList.remove('dragover');
          const file = e.dataTransfer.files && e.dataTransfer.files[0];
          if (file) { input.files = e.dataTransfer.files; showPreview(file); }
        });
      }

      bindDropzone('dzNicFront', 'nicFront');
      bindDropzone('dzNicBack', 'nicBack');
      bindDropzone('dzCustomerPhoto', 'customerPhoto');

      // Camera capture integration
      const cameraModal = document.getElementById('cameraModal');
      const cameraVideo = document.getElementById('cameraVideo');
      const cameraCanvas = document.getElementById('cameraCanvas');
      const cameraCapture = document.getElementById('cameraCapture');
      const cameraUse = document.getElementById('cameraUse');
      const cameraClose = document.getElementById('cameraClose');
      const cameraFlip = document.getElementById('cameraFlip');

      let currentStream = null;
      let currentFacing = 'environment';
      let pendingShotBlob = null;
      let targetInput = null;

      async function startCamera() {
        try {
          stopCamera();
          const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: currentFacing } });
          currentStream = stream;
          cameraVideo.srcObject = stream;
          await cameraVideo.play();
        } catch (err) {
          alert('Unable to access camera: ' + err.message);
        }
      }

      function stopCamera() {
        if (!currentStream) return;
        currentStream.getTracks().forEach(t => t.stop());
        currentStream = null;
      }

      function openCameraFor(inputEl) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          alert('Camera not supported on this device/browser.');
          return;
        }
        targetInput = inputEl;
        pendingShotBlob = null;
        cameraModal.style.display = 'flex';
        startCamera();
      }

      cameraCapture.addEventListener('click', () => {
        if (!cameraVideo.videoWidth) return;
        cameraCanvas.width = cameraVideo.videoWidth;
        cameraCanvas.height = cameraVideo.videoHeight;
        const ctx = cameraCanvas.getContext('2d');
        ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);
        cameraCanvas.toBlob((blob) => { pendingShotBlob = blob; }, 'image/jpeg', 0.9);
      });

      cameraUse.addEventListener('click', () => {
        if (!pendingShotBlob || !targetInput) return;
        const file = new File([pendingShotBlob], 'capture.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        targetInput.files = dt.files;
        targetInput.dispatchEvent(new Event('change'));
        cameraModal.style.display = 'none';
        stopCamera();
      });

      cameraClose.addEventListener('click', () => {
        cameraModal.style.display = 'none';
        stopCamera();
      });

      cameraFlip.addEventListener('click', () => {
        currentFacing = currentFacing === 'environment' ? 'user' : 'environment';
        startCamera();
      });

      function addCameraHooks(dzId, inputId) {
        const dz = document.getElementById(dzId);
        const btn = dz.querySelector('.dz-camera');
        const input = document.getElementById(inputId);
        if (btn) btn.addEventListener('click', (e) => { e.stopPropagation(); openCameraFor(input); });
      }

      addCameraHooks('dzNicFront', 'nicFront');
      addCameraHooks('dzNicBack', 'nicBack');
      addCameraHooks('dzCustomerPhoto', 'customerPhoto');

      // Form submission with validation
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        let ok = true;

        if (!emailRegex.test((emailInput.value || '').trim())) {
          emailInput.setCustomValidity('Please enter a valid email (e.g., name@example.com).');
          ok = false;
        } else {
          emailInput.setCustomValidity('');
        }

        if (((phoneInput.value || '').replace(/\D/g, '')).length !== 10) {
          phoneInput.setCustomValidity('Phone must be exactly 10 digits.');
          ok = false;
        } else {
          phoneInput.setCustomValidity('');
        }

        const nicVal = (nicInput.value || '').toUpperCase().trim();
        if (!nicRegex.test(nicVal)) {
          nicInput.setCustomValidity('NIC must be 12 digits (new) or 9 digits + V/X (old).');
          ok = false;
        } else {
          nicInput.setCustomValidity('');
        }

        if (!form.checkValidity() || !ok) {
          form.reportValidity();
          return;
        }

        const formData = new FormData();
        formData.append('first_name', document.getElementById('first_name').value);
        formData.append('last_name', document.getElementById('last_name').value);
        formData.append('full_name', document.getElementById('full_name').value);
        formData.append('phone', phoneInput.value);
        formData.append('email', emailInput.value);
        formData.append('address', document.getElementById('address').value);
        formData.append('gnd', document.getElementById('gnd').value);
        formData.append('lgi', document.getElementById('lgi').value);
        formData.append('police_station', document.getElementById('police_station').value);
        formData.append('nic', nicInput.value.toUpperCase());
        formData.append('occupation', document.getElementById('occupation').value);
        formData.append('residence_period', document.getElementById('residence_period').value);

        const nicFrontFile = document.getElementById('nicFront').files[0];
        const nicBackFile = document.getElementById('nicBack').files[0];
        const customerPhotoFile = document.getElementById('customerPhoto').files[0];

        if (nicFrontFile) formData.append('nic_front', nicFrontFile);
        if (nicBackFile) formData.append('nic_back', nicBackFile);
        if (customerPhotoFile) formData.append('customer_photo', customerPhotoFile);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-icons animate-spin">refresh</span> Saving...';

        try {
          const response = await fetch('../api/submit-customer-registration.php', {
            method: 'POST',
            body: formData
          });

          const result = await response.json();

          if (result.success) {
            await showNotificationDialog({
              title: 'Success!',
              message: `Customer registered successfully!\n\nCustomer Code: ${result.customer_code}\nName: ${result.customer_name}`,
              type: 'success'
            });
            window.location.href = 'customer.php';
          } else {
            await showNotificationDialog({
              title: 'Error',
              message: result.error || 'Failed to register customer. Please try again.',
              type: 'error'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          }
        } catch (error) {
          console.error('Submission error:', error);
          await showNotificationDialog({
            title: 'Network Error',
            message: 'An error occurred while adding the customer. Please check your connection and try again.',
            type: 'error'
          });
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      });
    </script>
  </body>
</html>

