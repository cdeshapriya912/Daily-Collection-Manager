<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Registration form</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  body {
    font-family: 'Roboto', sans-serif;
    background: #f4f6fa;
    display: flex;
    justify-content: center;
    padding: 40px;
  }
  
  @media (max-width: 900px) {
    body {
      padding: 0;
    }
  }

  .container {
    background: #ffffff;
    width: 100%;
    max-width: 1000px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    padding: 36px;
  }

  .header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
  }

  .header h2 { text-align: center; }

  h2 {
    margin: 0;
    font-size: 28px;
  }

  /* Circular progress badge */
  .progress-badge {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .progress-ring {
    --progress: 0%;
    --ring-color: #ef4444; /* default red */
    width: 96px;
    height: 96px;
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
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #ffffff;
  }

  .progress-ring span {
    position: relative;
    font-weight: 700;
    font-size: 18px;
  }

  /* Enable smooth interpolation of custom properties in supported browsers */
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

  .progress-text {
    font-size: 14px;
    color: #111827;
    font-weight: 600;
    text-align: center;
  }

  .section {
    margin-top: 20px;
  }

  .grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    color: #1f2937;
  }
  /* Make field labels equal height so inputs align even if label wraps */
  .field-label { min-height: 40px; display: flex; align-items: flex-end; }

  .required {
    color: #ef4444;
  }

  input, textarea, select {
    width: 100%;
    padding: 14px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 16px;
    outline: none;
    background: #ffffff;
  }

  /* Larger comfortable height for text boxes */
  input:not([type="file"]), select { height: 52px; }

  textarea { resize: vertical; min-height: 110px; }

  .doc-note {
    font-size: 13px;
    color: #6b7280;
    margin-top: 6px;
  }

  /* Modern dropzone layout */
  .dz-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
  .dropzone {
    position: relative;
    background: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    height: 260px;
    display: grid;
    place-items: center;
    color: #9ca3af;
    cursor: pointer;
    transition: border-color .15s ease, background-color .15s ease;
    overflow: hidden;
  }
  .dropzone.dragover { border-color: #10b981; background: #f0fdf4; }
  .dropzone .dz-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; border-radius: 10px; background: #ffffff; }
  .dropzone .dz-cta { position: absolute; bottom: 14px; left: 14px; right: 14px; display: flex; justify-content: space-between; align-items: center; z-index: 2; gap: 10px; }
  .dropzone .dz-title { font-weight: 600; color: #6b7280; }
  .dropzone .dz-btn { background:#10b981; color:#fff; padding:8px 12px; border-radius:8px; font-weight:600; font-size:14px; }

  /* Camera modal */
  .camera-modal { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: none; align-items: center; justify-content: center; z-index: 100; }
  .camera-card { background: #fff; width: min(960px, 92vw); border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,.2); padding: 16px; }
  .video-wrap { position: relative; background: #000; border-radius: 12px; overflow: hidden; height: 420px; }
  .video-wrap video, .video-wrap canvas { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; }
  .camera-actions { display: flex; gap: 10px; justify-content: space-between; margin-top: 12px; }
  .btn { background:#10b981; color:#fff; padding:10px 14px; border-radius:10px; font-weight:600; border:0; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; min-width:48px; min-height:48px; }
  .btn.gray { background:#6b7280; }
  .btn svg { width:20px; height:20px; fill:currentColor; }

  .actions { display: flex; justify-content: center; }
  .submit-btn {
    background: #10b981;
    color: #ffffff;
    padding: 12px 22px;
    font-size: 16px;
    font-weight: 600;
    border: 0;
    border-radius: 10px;
    cursor: pointer;
  }
  .submit-btn:hover { background: #0ea371; }

  /* Hide the raw file inputs */
  input[type="file"] { display: none; }

  @media (max-width: 900px) {
    .grid-2, .row { grid-template-columns: 1fr; }
    .upload-row { grid-template-columns: 1fr; }
    .preview-small { width: 100%; height: 220px; }
    /* Stack header and shrink progress on small screens to avoid overflow */
    .header h2 { font-size: 24px !important; line-height: 1.2; }
    .progress-text { display: none; }
    .progress-ring { width: 72px; height: 72px; }
    .progress-ring::before { width: 52px; height: 52px; }
    .progress-ring span { font-size: 14px; }
  }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h2 style="font-size: 36px; font-weight: 800; margin: 0;">Customer Registration Form</h2>
    <div class="progress-badge">
      <div class="progress-ring" id="progressRing" style="--progress: 0%; --ring-color: #ef4444;">
        <span id="progressPercent">0%</span>
      </div>
      <div class="progress-text">Completed</div>
    </div>
  </div>

  <form id="registrationForm" class="section" autocomplete="off">
      <div class="grid-2">
      <div>
        <label class="field-label">First Name (මුල් නම) <span class="required">*</span></label>
        <input type="text" name="first_name" required>
      </div>
      <div>
        <label class="field-label">Last Name (අවසාන නම) <span class="required">*</span></label>
        <input type="text" name="last_name" required>
      </div>
    </div>

    <div class="section">
      <label class="field-label">Full name with surname (වාසගම සහිත සම්පූර්ණ නම)</label>
      <input type="text" name="full_name">
    </div>

    <div class="grid-2 section">
      <div>
        <label class="field-label">Phone Number (0771230000) <span class="required">*</span></label>
        <input type="tel" name="phone" inputmode="numeric" maxlength="10" pattern="\d{10}" required>
      </div>
      <div>
        <label class="field-label">Email Address <span class="required">*</span></label>
        <input type="email" name="email" inputmode="email" pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" required>
      </div>
    </div>

    <div class="section">
      <label class="field-label">Permanent Address <span class="required">*</span></label>
      <textarea name="address" rows="3" required></textarea>
    </div>

    <div class="grid-2 section">
      <div>
        <label class="field-label">Grama Niladari Division (ග්‍රාම නිලධාරී වසම)</label>
        <input type="text" name="gnd">
      </div>
      <div>
        <label class="field-label">Local Government Institutions (පළාත් පාලන ආයතන)</label>
        <input type="text" name="lgi">
      </div>
    </div>

    <div class="grid-2 section">
      <div>
        <label class="field-label">Police station (පොලිස් ස්ථානය)</label>
        <input type="text" name="police_station">
      </div>
      <div>
        <label class="field-label">NIC ID Number (ජාතික හැඳුනුම්පත් අංකය) <span class="required">*</span></label>
        <input type="text" name="nic" maxlength="12" pattern="(?:\d{12}|\d{9}[VvXx])" title="Enter 12 digits (new NIC) or 9 digits plus V/X (old NIC)" required>
      </div>
    </div>

    <div class="grid-2 section">
      <div>
        <label class="field-label">Permanent Occupation (ස්ථිර රැකියාව)</label>
        <input type="text" name="occupation">
      </div>
      <div>
        <label class="field-label">Period of residence at the above address (ඉහත ලිපිනෙහි පදිංචි කාලය)</label>
        <input type="text" name="residence_period">
      </div>
    </div>

    <div class="section">
      <h3 style="margin:0 0 6px 0;">Document Upload</h3>
      <div class="doc-note">Supported formats: JPG, PNG, GIF (Max 2MB). Drag & drop or browse.</div>
      <div class="dz-grid section">
        <!-- NIC Front -->
        <div class="dropzone" id="dzNicFront">
          <span>NIC Front preview</span>
          <div class="dz-cta">
            <span class="dz-title">NIC ID Front</span>
            <div style="display:flex; gap:8px;">
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
            <div style="display:flex; gap:8px;">
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
            <div style="display:flex; gap:8px;">
              <button type="button" class="dz-btn dz-camera">Camera</button>
              <button type="button" class="dz-btn">Browse</button>
            </div>
          </div>
          <input id="customerPhoto" type="file" accept="image/jpeg,image/png,image/gif">
        </div>
      </div>
    </div>

    <div class="actions section">
      <button type="submit" class="submit-btn">Submit</button>
    </div>
  </form>
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
</div>

<script>
  const form = document.getElementById('registrationForm');
  const ring = document.getElementById('progressRing');
  const percentText = document.getElementById('progressPercent');

  // Required fields for progress calculation
  const requiredSelectors = [
    'input[name="first_name"]',
    'input[name="last_name"]',
    'input[name="phone"]',
    'input[name="email"]',
    'textarea[name="address"]',
    'input[name="nic"]'
  ];

  const requiredInputs = requiredSelectors
    .map(sel => form.querySelector(sel))
    .filter(Boolean);

  function clamp(n, min, max) { return Math.min(Math.max(n, min), max); }

  function percentToColor(p) {
    // 0 -> red (0deg), 50 -> yellow (60deg), 100 -> green (120deg)
    const hue = Math.round((120 * p) / 100);
    return `hsl(${hue}, 80%, 45%)`;
  }

  function updateProgress() {
    let filled = 0;
    requiredInputs.forEach(input => {
      if (input.type === 'file') {
        if (input.files && input.files.length > 0) filled++;
      } else if ((input.value || '').trim() !== '') {
        filled++;
      }
    });
    const total = requiredInputs.length;
    const p = total === 0 ? 0 : Math.round((filled / total) * 100);
    ring.style.setProperty('--progress', p + '%');
    ring.style.setProperty('--ring-color', percentToColor(p));
    percentText.textContent = p + '%';
  }

  requiredInputs.forEach(el => {
    el.addEventListener('input', updateProgress);
    el.addEventListener('change', updateProgress);
  });
  updateProgress();

  // Field validation and sanitization
  const emailInput = form.querySelector('input[name="email"]');
  const phoneInput = form.querySelector('input[name="phone"]');
  const nicInput = form.querySelector('input[name="nic"]');

  const emailRegex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
  const nicRegex = /^(?:\d{12}|\d{9}[VX])$/; // Sri Lanka NIC: 12 digits (new) or 9 digits + V/X (old)

  // Phone: only digits, max 10
  phoneInput.addEventListener('input', () => {
    const digitsOnly = (phoneInput.value || '').replace(/\D/g, '').slice(0, 10);
    if (phoneInput.value !== digitsOnly) phoneInput.value = digitsOnly;
    phoneInput.setCustomValidity('');
  });

  // Email: clear custom validity on input; strict check on submit
  emailInput.addEventListener('input', () => {
    emailInput.setCustomValidity('');
  });

  // NIC: allow only alphanumeric, uppercase, max length 12
  nicInput.addEventListener('input', () => {
    let v = (nicInput.value || '').toUpperCase().replace(/[^0-9A-Z]/g, '');
    if (v.length > 12) v = v.slice(0, 12);
    if (nicInput.value !== v) nicInput.value = v;
    nicInput.setCustomValidity('');
  });

  // File upload previews with validation
  const MAX_SIZE = 2 * 1024 * 1024; // 2MB
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
        // Remove previous preview image if any
        const existing = dz.querySelector('.dz-img');
        if (existing) existing.remove();
        // Hide placeholder text if present
        const placeholder = dz.querySelector('span');
        if (placeholder) placeholder.style.display = 'none';
        // Append preview image but keep CTA and input
        const img = document.createElement('img');
        img.className = 'dz-img';
        img.alt = 'preview';
        img.src = e.target.result;
        dz.appendChild(img);
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

    // Email strict check
    if (!emailRegex.test((emailInput.value || '').trim())) {
      emailInput.setCustomValidity('Please enter a valid email (e.g., name@example.com).');
      ok = false;
    } else {
      emailInput.setCustomValidity('');
    }

    // Phone must be exactly 10 digits
    if (((phoneInput.value || '').replace(/\D/g, '')).length !== 10) {
      phoneInput.setCustomValidity('Phone must be exactly 10 digits.');
      ok = false;
    } else {
      phoneInput.setCustomValidity('');
    }

    // NIC: 12 digits (new) or 9 digits + V/X (old)
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

    // Prepare form data with files
    const formData = new FormData();
    formData.append('first_name', form.querySelector('[name="first_name"]').value);
    formData.append('last_name', form.querySelector('[name="last_name"]').value);
    formData.append('full_name', form.querySelector('[name="full_name"]').value);
    formData.append('phone', phoneInput.value);
    formData.append('email', emailInput.value);
    formData.append('address', form.querySelector('[name="address"]').value);
    formData.append('gnd', form.querySelector('[name="gnd"]').value);
    formData.append('lgi', form.querySelector('[name="lgi"]').value);
    formData.append('police_station', form.querySelector('[name="police_station"]').value);
    formData.append('nic', nicInput.value.toUpperCase());
    formData.append('occupation', form.querySelector('[name="occupation"]').value);
    formData.append('residence_period', form.querySelector('[name="residence_period"]').value);

    // Append files
    const nicFrontFile = document.getElementById('nicFront').files[0];
    const nicBackFile = document.getElementById('nicBack').files[0];
    const customerPhotoFile = document.getElementById('customerPhoto').files[0];

    if (nicFrontFile) formData.append('nic_front', nicFrontFile);
    if (nicBackFile) formData.append('nic_back', nicBackFile);
    if (customerPhotoFile) formData.append('customer_photo', customerPhotoFile);

    // Disable submit button and show loading
    const submitBtn = form.querySelector('.submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    submitBtn.style.opacity = '0.6';

    try {
      const response = await fetch('api/submit-customer-registration.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (result.success) {
        alert(`Success! Customer registered successfully.\n\nCustomer Code: ${result.customer_code}\nName: ${result.customer_name}`);
        // Reset form
        form.reset();
        // Clear image previews
        document.querySelectorAll('.dz-img').forEach(img => img.remove());
        document.querySelectorAll('.dropzone span').forEach(span => span.style.display = '');
        updateProgress();
      } else {
        alert('Error: ' + (result.error || 'Failed to register customer'));
      }
    } catch (error) {
      console.error('Submission error:', error);
      alert('Failed to submit form. Please check your connection and try again.');
    } finally {
      // Re-enable submit button
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      submitBtn.style.opacity = '1';
    }
  });
</script>

</body>
</html>
