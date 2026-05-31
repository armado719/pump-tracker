const STATIC_CACHE  = 'pump-static-v2';
const RUNTIME_CACHE = 'pump-runtime-v2';

const PRECACHE_URLS = ['/offline', '/dashboard', '/pumps', '/alerts'];

self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(STATIC_CACHE).then(c => c.addAll(PRECACHE_URLS).catch(() => {}))
    );
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== STATIC_CACHE && k !== RUNTIME_CACHE).map(k => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    const url = new URL(e.request.url);

    // Assets Vite (CSS/JS con hash) → Cache First
    if (url.pathname.startsWith('/build/')) {
        e.respondWith(
            caches.match(e.request).then(cached => {
                if (cached) return cached;
                return fetch(e.request).then(res => {
                    caches.open(STATIC_CACHE).then(c => c.put(e.request, res.clone()));
                    return res;
                });
            })
        );
        return;
    }

    // CDN externos (Chart.js, fonts) → Cache First
    if (!url.hostname.includes('localhost') && !url.hostname.includes('127.0.0.1')) {
        e.respondWith(
            caches.match(e.request).then(cached => {
                if (cached) return cached;
                return fetch(e.request).then(res => {
                    caches.open(RUNTIME_CACHE).then(c => c.put(e.request, res.clone()));
                    return res;
                }).catch(() => new Response('', {status: 408}));
            })
        );
        return;
    }

    // Páginas HTML → Network First, cache como fallback
    if (e.request.headers.get('accept')?.includes('text/html')) {
        e.respondWith(
            fetch(e.request)
                .then(res => {
                    caches.open(RUNTIME_CACHE).then(c => c.put(e.request, res.clone()));
                    return res;
                })
                .catch(() =>
                    caches.match(e.request).then(cached => cached || caches.match('/offline'))
                )
        );
        return;
    }

    // Resto → Network First con cache fallback
    e.respondWith(
        fetch(e.request)
            .then(res => {
                caches.open(RUNTIME_CACHE).then(c => c.put(e.request, res.clone()));
                return res;
            })
            .catch(() => caches.match(e.request))
    );
});
