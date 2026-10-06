const CACHE_NAME = 'tgm-health-v1';
const ASSETS = [
    './',
    './index.html',
    './manifest.json'
];

self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
    );
});

self.addEventListener('fetch', (e) => {
    // Only cache GET requests that are not health checks
    if (e.request.url.includes('/health')) {
        return fetch(e.request);
    }
    
    e.respondWith(
        caches.match(e.request).then((response) => response || fetch(e.request))
    );
});
