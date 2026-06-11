const CACHE_VERSION  = '__BUILD_TIME__';
const STATIC_CACHE   = 'pump-static-'  + CACHE_VERSION;
const RUNTIME_CACHE  = 'pump-runtime-' + CACHE_VERSION;

const PRECACHE_URLS = ['/offline'];

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
                keys.filter(k => k !== STATIC_CACHE && k !== RUNTIME_CACHE)
                    .map(k => caches.delete(k))
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
                    const clone = res.clone();
                    if (res.ok) caches.open(STATIC_CACHE).then(c => c.put(e.request, clone));
                    return res;
                });
            })
        );
        return;
    }

    // CDN externos (fuentes, Chart.js) → Cache First
    if (url.hostname !== self.location.hostname) {
        e.respondWith(
            caches.match(e.request).then(cached => {
                if (cached) return cached;
                return fetch(e.request).then(res => {
                    const clone = res.clone();
                    if (res.ok) caches.open(RUNTIME_CACHE).then(c => c.put(e.request, clone));
                    return res;
                }).catch(() => new Response('', { status: 408 }));
            })
        );
        return;
    }

    // Páginas HTML → Network First sin caché (evita guardar redirects de login)
    if (e.request.headers.get('accept')?.includes('text/html')) {
        e.respondWith(
            fetch(e.request)
                .catch(() =>
                    caches.match(e.request)
                        .then(cached => cached || caches.match('/offline'))
                        .then(res => res || new Response('Sin conexión', { status: 503 }))
                )
        );
        return;
    }

    // Resto → Network First con cache fallback
    e.respondWith(
        fetch(e.request)
            .then(res => {
                const clone = res.clone();
                if (res.ok) caches.open(RUNTIME_CACHE).then(c => c.put(e.request, clone));
                return res;
            })
            .catch(() => caches.match(e.request)
                .then(cached => cached || new Response('', { status: 503 }))
            )
    );
});
