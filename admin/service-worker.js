// Basic cache-first service worker for static assets
const CACHE_NAME = 'dcm-pwa-v22';
const ASSETS = [
  './',
  './index.php',
  './product.php',
  './add-product.php',
  './category.php',
  './collection.php',
  './customer.php',
  './user.php',
  './ui-kit.php',
  './settings.php',
  './assets/css/styles.css',
  './js/app.js',
  './js/product.js',
  './manifest.webmanifest',
  './assets/icons/icon-192.svg',
  './assets/icons/icon-512.svg',
  './assets/icons/favicon.svg',
  './img/package.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((k) => (k !== CACHE_NAME ? caches.delete(k) : undefined)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const accept = request.headers.get('Accept') || '';
  const isHTML = request.mode === 'navigate' || accept.includes('text/html');

  if (isHTML) {
    // Network-first for HTML to avoid stale pages
    event.respondWith(
      fetch(request)
        .then((response) => {
          const copy = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          return response;
        })
        .catch(() =>
          caches.match(request).then((cached) => cached || caches.match('./index.php'))
        )
    );
    return;
  }

  // Cache-first for static assets
  event.respondWith(caches.match(request).then((cached) => cached || fetch(request)));
});


