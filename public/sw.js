const CACHE_NAME = 'optica-golden-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/catalogo',
    '/manifest.json',
    '/icons/icon-192.svg',
    '/icons/icon-512.svg',
];

// ── Install: pre-cache shell assets ──────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_ASSETS).catch(() => {
                // Partial failure is acceptable (some routes may not exist yet)
                return cache.add(OFFLINE_URL);
            });
        }).then(() => self.skipWaiting())
    );
});

// ── Activate: purge stale caches ─────────────────────────────────────────────
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch strategy ───────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const req = event.request;
    const url = new URL(req.url);

    // Only handle same-origin GET requests
    if (req.method !== 'GET' || url.origin !== self.location.origin) return;

    // Skip API calls and admin routes — always network
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/admin')) return;

    // Skip auth routes
    if (['/login', '/register', '/logout'].includes(url.pathname)) return;

    if (req.mode === 'navigate') {
        // Navigation: network-first → offline fallback
        event.respondWith(
            fetch(req)
                .then(response => {
                    // Cache successful navigation responses
                    if (response && response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(c => c.put(req, clone));
                    }
                    return response;
                })
                .catch(async () => {
                    const cached = await caches.match(req);
                    return cached || caches.match(OFFLINE_URL);
                })
        );
        return;
    }

    // Static assets (css, js, fonts, icons): cache-first
    if (
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/img/') ||
        /\.(woff2?|ttf|eot|svg|png|jpg|jpeg|gif|webp|ico)$/.test(url.pathname)
    ) {
        event.respondWith(
            caches.match(req).then(cached => {
                if (cached) return cached;
                return fetch(req).then(response => {
                    if (response && response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(c => c.put(req, clone));
                    }
                    return response;
                }).catch(() => caches.match(OFFLINE_URL));
            })
        );
        return;
    }

    // Everything else: stale-while-revalidate
    event.respondWith(
        caches.open(CACHE_NAME).then(cache => {
            return cache.match(req).then(cached => {
                const fetchPromise = fetch(req).then(response => {
                    if (response && response.status === 200) {
                        cache.put(req, response.clone());
                    }
                    return response;
                }).catch(() => cached || caches.match(OFFLINE_URL));
                return cached || fetchPromise;
            });
        })
    );
});

// ── Push notifications (future use) ──────────────────────────────────────────
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'Óptica Golden', {
            body: data.body || '',
            icon: '/icons/icon-192.svg',
            badge: '/icons/icon-192.svg',
            data: { url: data.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
