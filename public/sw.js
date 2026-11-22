const CACHE_NAME = 'veyra-pwa-v3';
const PRECACHE_ASSETS = [
    './offline.html',
    './favicon.png',
    './pwa-icon-192.png',
    './pwa-icon-512.png',
    './manifest.json'
];

// Install: Simpan aset penting ke cache
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate: Bersihkan cache versi lama
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Network First, fallback to offline.html for HTML navigation
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Hanya tangani GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Navigasi halaman HTML: Network first, jika gagal tampilkan offline.html
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(async () => {
                const cached = await caches.match('./offline.html');
                return cached || new Response('<h1>Offline</h1>', { status: 503, headers: { 'Content-Type': 'text/html' } });
            })
        );
        return;
    }

    // Aset statis & gambar: Network first, fallback ke cache jika offline
    event.respondWith(
        fetch(request)
            .then((response) => {
                // Simpan salinan response yang valid ke cache jika itu gambar/ikon
                if (response && response.status === 200 && response.type === 'basic') {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return response;
            })
            .catch(async () => {
                const cached = await caches.match(request);
                return cached || new Response('', { status: 404, statusText: 'Not Found' });
            })
    );
});
