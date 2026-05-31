<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pump Tracker') }} @hasSection('title') – @yield('title') @endif</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a5f">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
    <aside class="w-64 flex-shrink-0 bg-blue-900 text-white flex flex-col">
        <div class="px-6 py-5 border-b border-blue-800">
            <span class="text-xl font-bold tracking-wide">⛽ Pump Tracker</span>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('rigs.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('rigs.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Rigs
            </a>

            <a href="{{ route('pumps.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('pumps.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Bombas
            </a>

            <a href="{{ route('alerts.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('alerts.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Alertas
            </a>

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('reports.*') ? 'bg-blue-700 text-white' : 'text-blue-200 hover:bg-blue-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reportes
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-blue-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="text-sm">
                    <div class="font-medium text-white">{{ auth()->user()->name ?? '' }}</div>
                    <div class="text-blue-300 text-xs">{{ auth()->user()->role ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left text-xs text-blue-300 hover:text-white px-3 py-1.5 rounded hover:bg-blue-800 transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN AREA ────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            <span class="text-sm text-gray-500">{{ now()->format('d/m/Y') }}</span>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 flex items-stretch rounded-xl overflow-hidden shadow-md text-sm auto-dismiss">
            <div class="bg-green-500 flex items-center justify-center px-4 py-3">
                <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="bg-green-50 border border-green-200 flex-1 px-4 py-3">
                <p class="font-bold text-green-800 mb-0.5">Éxito</p>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-4 flex items-stretch rounded-xl overflow-hidden shadow-md text-sm auto-dismiss">
            <div class="bg-red-500 flex items-center justify-center px-4 py-3">
                <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="bg-red-50 border border-red-200 flex-1 px-4 py-3">
                <p class="font-bold text-red-800 mb-0.5">Error</p>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('warning'))
        <div class="mx-6 mt-4 flex items-stretch rounded-xl overflow-hidden shadow-md text-sm auto-dismiss">
            <div class="bg-orange-400 flex items-center justify-center px-4 py-3">
                <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="bg-orange-50 border border-orange-200 flex-1 px-4 py-3">
                <p class="font-bold text-orange-800 mb-0.5">Atención</p>
                <p class="text-orange-700">{{ session('warning') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-6 mt-4 flex items-stretch rounded-xl overflow-hidden shadow-md text-sm auto-dismiss">
            <div class="bg-red-500 flex items-center justify-center px-4 py-3">
                <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="bg-red-50 border border-red-200 flex-1 px-4 py-3">
                <p class="font-bold text-red-800 mb-0.5">Error de validación</p>
                <ul class="text-red-700 list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- Banner offline / sync pendiente --}}
        <div id="offlineBanner" class="hidden mx-6 mt-3 px-4 py-2 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg text-sm flex items-center justify-between">
            <span>📡 Sin conexión — trabajando en modo offline</span>
        </div>
        <div id="syncBanner" class="hidden mx-6 mt-3 px-4 py-2 bg-blue-100 border border-blue-300 text-blue-800 rounded-lg text-sm flex items-center justify-between">
            <span id="syncMsg"></span>
            <button onclick="syncPendingLogs()" class="ml-4 bg-blue-600 text-white text-xs px-3 py-1 rounded-lg font-medium hover:bg-blue-700">
                Sincronizar ahora
            </button>
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
<script>
// ── Offline / Sync ───────────────────────────────────────────────────────
function updateOnlineStatus() {
    document.getElementById('offlineBanner').classList.toggle('hidden', navigator.onLine);
    if (navigator.onLine) checkPendingLogs();
}

function checkPendingLogs() {
    const pending = JSON.parse(localStorage.getItem('pending_logs') || '[]');
    const banner  = document.getElementById('syncBanner');
    const msg     = document.getElementById('syncMsg');
    if (pending.length > 0) {
        msg.textContent = `Hay ${pending.length} registro(s) pendiente(s) de sincronizar`;
        banner.classList.remove('hidden');
    } else {
        banner.classList.add('hidden');
    }
}

async function syncPendingLogs() {
    const pending = JSON.parse(localStorage.getItem('pending_logs') || '[]');
    if (!pending.length) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let synced = 0;

    for (const entry of pending) {
        try {
            const body = new URLSearchParams(entry.data);
            body.set('_token', csrfToken);
            const res = await fetch(entry.url, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (res.ok || res.redirected) synced++;
        } catch (e) { break; }
    }

    if (synced > 0) {
        localStorage.removeItem('pending_logs');
        alert(`✅ ${synced} registro(s) sincronizados correctamente.`);
        window.location.reload();
    } else {
        alert('No se pudieron sincronizar los registros. Intenta de nuevo.');
    }
}

window.addEventListener('online',  updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
// Page loaded from server = we are online; just check for pending logs
checkPendingLogs();

// Auto-dismiss flash notifications after 4 seconds
document.querySelectorAll('.auto-dismiss').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    }, 4000);
});
</script>
</body>
</html>
