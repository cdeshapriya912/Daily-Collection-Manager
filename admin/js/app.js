/* UI interactions + PWA registration */
(function () {
  const toggleButton = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('mobileSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const userNameEl = document.getElementById('userName');
  const installBtn = document.getElementById('installBtn');
  let deferredPrompt = null;

  const prefersDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

  function setUserName() {
    // User name is now set by PHP in the HTML
    // Only update if localStorage has a value AND the element is empty/default
    const stored = localStorage.getItem('loggedUserName');
    if (userNameEl && stored && stored.trim() && userNameEl.textContent === 'User') {
      userNameEl.textContent = stored;
    }
  }

  function loadSidebarState() {
    const collapsed = localStorage.getItem('sidebarCollapsed');
    if (collapsed === 'true' && prefersDesktop()) {
      document.body.classList.add('sidebar-collapsed');
    }
  }

  function saveSidebarState() {
    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', String(isCollapsed));
  }

  function toggleSidebar() {
    if (prefersDesktop()) {
      document.body.classList.toggle('sidebar-collapsed');
      saveSidebarState();
    } else {
      if (!sidebar) return;
      const isHidden = sidebar.classList.contains('-translate-x-full');
      sidebar.classList.toggle('-translate-x-full', !isHidden);
      if (backdrop) backdrop.classList.toggle('hidden', !isHidden);
    }
  }

  function onResize() {
    // Clean up mobile open state when switching to desktop
    if (prefersDesktop()) document.body.classList.remove('sidebar-open');
  }

  // Event bindings
  if (toggleButton) {
    toggleButton.addEventListener('click', () => {
      toggleSidebar();
      const expanded = toggleButton.getAttribute('aria-expanded') === 'true';
      toggleButton.setAttribute('aria-expanded', String(!expanded));
    });
  }

  window.addEventListener('resize', onResize);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (!prefersDesktop() && sidebar) {
        sidebar.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
      }
      document.body.classList.remove('sidebar-open');
    }
  });

  if (backdrop) {
    backdrop.addEventListener('click', () => {
      if (sidebar) sidebar.classList.add('-translate-x-full');
      backdrop.classList.add('hidden');
    });
  }

  // Init
  setUserName();
  loadSidebarState();

  // PWA: register service worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('service-worker.js').catch(function () {
        // ignore errors in dev
      });
    });
  }

  // PWA: handle install prompt (Android Chrome)
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    if (installBtn) installBtn.classList.remove('hidden');
  });

  if (installBtn) {
    installBtn.addEventListener('click', async () => {
      if (!deferredPrompt) return;
      deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      if (outcome) {
        installBtn.classList.add('hidden');
      }
      deferredPrompt = null;
    });
  }

  // Disable page zoom (pinch, double-tap, ctrl+wheel)
  try {
    // Prevent pinch-zoom (Safari/WebKit)
    ['gesturestart','gesturechange','gestureend'].forEach((evt) => {
      window.addEventListener(evt, (e) => e.preventDefault(), { passive: false });
    });
    // Prevent ctrl + wheel zoom
    window.addEventListener('wheel', (e) => {
      if (e.ctrlKey) e.preventDefault();
    }, { passive: false });
    // Prevent double-tap zoom
    let lastTouchEnd = 0;
    document.addEventListener('touchend', (e) => {
      const now = Date.now();
      if (now - lastTouchEnd <= 300) e.preventDefault();
      lastTouchEnd = now;
    }, { passive: false });
    // Prevent pinch via scale on some browsers
    document.addEventListener('touchmove', (e) => {
      if ((e as any).scale && (e as any).scale !== 1) e.preventDefault();
    }, { passive: false });
  } catch (_) {
    // no-op if browser doesn't support these events
  }
})();


