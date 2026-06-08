const CACHE_VERSION  = '__BUILD_TIME__';
const STATIC_CACHE   = 'pump-static-'  + CACHE_VERSION;
const RUNTIME_CACHE  = 'pump-runtime-' + CACHE_VERSION;

// Solo cachear la página offline — las rutas autenticadas NO se pre-cachean
// porque al instalarse el SW el usuario puede no estar logueado y se guardaría
// la página de login bajo la URL del dashboard/pumps/alerts (causa ERR_FAILED).
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
                    if (res.ok) {
                        caches.open(STATIC_CACHE).then(c => c.put(e.request, res.clone()));
                    }
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
                    if (res.ok) {
                        caches.open(RUNTIME_CACHE).then(c => c.put(e.request, res.clone()));
                    }
                    return res;
                }).catch(() => new Response('', { status: 408 }));
            })
        );
        return;
    }

    // Páginas HTML → Network First, sin guardar en caché para evitar
    // que respuestas de redirección (302 → login) se almacenen bajo rutas protegidas.
    // Solo usamos el caché como fallback offline cuando la red no responde.
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

    // Resto (API calls, imágenes, etc.) → Network First con cache fallback
    e.respondWith(
        fetch(e.request)
            .then(res => {
                if (res.ok) {
                    caches.open(RUNTIME_CACHE).then(c => c.put(e.request, res.clone()));
                }
                return res;
            })
            .catch(() => caches.match(e.request)
                .then(cached => cached || new Response('', { status: 503 }))
            )
    );
});
