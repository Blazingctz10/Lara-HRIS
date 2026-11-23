/* =============================================================================
   PT BPR BANDA RAYA — SECURITY-SAFE PWA SERVICE WORKER
   Caches only public static assets and an offline fallback page.
   NEVER caches authenticated HR/payroll/admin HTML or Livewire payloads.
   ============================================================================= */

const CACHE_NAME = 'bpr-hris-static-v1';
const STATIC_ASSETS = [
    '/offline.html',
    '/css/bento-theme.css',
    '/images/logo-tight.png',
    '/images/logo-horizontal-clean.png',
    '/images/logo-horizontal-dark.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    // Navigation requests: network-first, fallback ONLY to offline.html (never cache private pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Static public assets only (/css/, /images/, /manifest.webmanifest)
    const isPublicStatic =
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname === '/manifest.webmanifest';

    if (isPublicStatic) {
        event.respondWith(
            caches.match(request).then((cached) =>
                cached ||
                fetch(request).then((response) => {
                    if (response && response.ok) {
                        const copy = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
            )
        );
    }
});
