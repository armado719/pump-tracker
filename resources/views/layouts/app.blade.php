<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pump Tracker') }} @hasSection('title') – @yield('title') @endif</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b1622">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Desktop: sidebar visible como columna fija */
        @media (min-width: 768px) {
            #sidebar {
                display: flex !important;
                position: relative !important;
                transform: none !important;
                width: 256px !important;
                flex-shrink: 0 !important;
            }
            #hamburger     { display: none   !important; }
            #sidebar-overlay { display: none !important; }
        }
        /* Móvil: sidebar oculto, se muestra como overlay al abrir */
        @media (max-width: 767px) {
            #sidebar {
                display: none !important;
                position: fixed !important;
                top: 0; left: 0;
                height: 100vh !important;
                width: 256px !important;
                z-index: 50 !important;
            }
            #sidebar.open   { display: flex !important; }
            #hamburger      { display: flex !important; }
            #sidebar-overlay.visible { display: block !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden" style="display:flex;height:100vh;overflow:hidden;">

    {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
    {{-- Overlay oscuro al abrir sidebar en móvil --}}
    <div id="sidebar-overlay" class="hidden" onclick="closeSidebar()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:40;"></div>

    <aside id="sidebar" class="w-64 flex-shrink-0 text-white flex-col" style="display:none;width:256px;flex-shrink:0;flex-direction:column;background-color:#0b1622;">
        {{-- Logo GRS --}}
        <div class="py-5 flex flex-col items-center text-center" style="padding:1.25rem 0;display:flex;flex-direction:column;align-items:center;text-align:center;border-bottom:1px solid #1a3528;">
            <img src="/images/grs-logo.png" alt="GRS" style="width:80px;height:80px;border-radius:50%;margin-bottom:8px;flex-shrink:0;object-fit:cover;">
            <span class="text-sm font-bold tracking-widest text-white" style="font-size:0.875rem;font-weight:700;color:#ffffff;letter-spacing:0.1em;">GRS</span>
            <span style="font-size:0.7rem;color:#6b9e82;">General Rigs Services S.A.S.</span>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto" style="flex:1;padding:1rem;overflow-y:auto;">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('dashboard') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('dashboard') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('rigs.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('rigs.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('rigs.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Rigs
            </a>

            <a href="{{ route('pumps.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('pumps.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('pumps.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Bombas
            </a>

            <a href="{{ route('alerts.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('alerts.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('alerts.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Alertas
            </a>

            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('reports.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('reports.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reportes
            </a>

            {{-- ── GERENCIA ── --}}
            <div class="px-3 pt-4 pb-1">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-px" style="background:#2a1f3d;"></div>
                    <span class="text-xs font-black tracking-widest" style="color:#7c6fa0;">GERENCIA</span>
                    <div class="flex-1 h-px" style="background:#2a1f3d;"></div>
                </div>
            </div>

            <a href="{{ route('gerencia.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('gerencia.*') ? 'background-color:#1e1040;color:#a78bfa;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#150e2a'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('gerencia.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Panel Gerencial
            </a>

            {{-- ── CABLE TM ── --}}
            <div class="px-3 pt-4 pb-1">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-px" style="background:#003344;"></div>
                    <span class="text-xs font-black tracking-widest" style="color:#4a8a9e;">CABLE TM</span>
                    <div class="flex-1 h-px" style="background:#003344;"></div>
                </div>
            </div>

            <a href="{{ route('cable.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('cable.*') ? 'background-color:#003d4d;color:#06B6D4;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#001f2a'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('cable.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Estado Cable TM
            </a>

            <a href="{{ route('cable.operaciones.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('cable.operaciones.create') ? 'background-color:#003d4d;color:#06B6D4;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#001f2a'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Operación
            </a>

            <a href="{{ route('cable.historial') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('cable.historial') ? 'background-color:#003d4d;color:#06B6D4;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#001f2a'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Historial Cables
            </a>

            <a href="{{ route('reports.index') }}#cable-tm"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="color:#9ab8a8;"
               onmouseover="this.style.backgroundColor='#001f2a'"
               onmouseout="this.style.backgroundColor=''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reporte PDF TM
            </a>

            @if(auth()->user()->isAdmin())
            {{-- Separador admin --}}
            <div class="px-3 pt-4 pb-1">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-px" style="background:#1a2535;"></div>
                    <span class="text-xs font-black tracking-widest" style="color:#5a7a8e;">ADMIN</span>
                    <div class="flex-1 h-px" style="background:#1a2535;"></div>
                </div>
            </div>

            <a href="{{ route('users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('users.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('users.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Usuarios
            </a>

            <a href="{{ route('wells.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('wells.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('wells.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pozos
            </a>

            <a href="{{ route('audit.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('audit.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('audit.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Auditoría
            </a>

            <a href="{{ route('admin.settings.mail') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition"
               style="{{ request()->routeIs('admin.settings.*') ? 'background-color:#0d4a35;color:#4ade80;' : 'color:#9ab8a8;' }}"
               onmouseover="if(!this.dataset.active)this.style.backgroundColor='#152535'"
               onmouseout="if(!this.dataset.active)this.style.backgroundColor=''"
               {{ request()->routeIs('admin.settings.*') ? 'data-active=1' : '' }}>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Correo SMTP
            </a>
            @endif
        </nav>

        <div class="px-4 py-4" style="border-top:1px solid #1a3528;">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs mb-3 transition"
               style="{{ request()->routeIs('profile.*') ? 'background:#0d4a35;color:#4ade80;' : 'color:#6b9e82;' }}"
               onmouseover="this.style.backgroundColor='#152535'"
               onmouseout="this.style.backgroundColor='{{ request()->routeIs('profile.*') ? '#0d4a35' : '' }}'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Mi Perfil / Contraseña
            </a>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                     style="background-color:#0d4a35;color:#4ade80;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="text-sm">
                    <div class="font-medium text-white">{{ auth()->user()->name ?? '' }}</div>
                    <div class="text-xs" style="color:#6b9e82;">
                        @php $roleLabel = ['admin'=>'Administrador','rig_manager'=>'Rig Manager','supervisor'=>'Supervisor']; @endphp
                        {{ $roleLabel[auth()->user()->role] ?? auth()->user()->role }}
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left text-xs px-3 py-1.5 rounded transition"
                        style="color:#6b9e82;"
                        onmouseover="this.style.color='#fff';this.style.backgroundColor='#152535'"
                        onmouseout="this.style.color='#6b9e82';this.style.backgroundColor=''">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <script>if(window.innerWidth>=768){var s=document.getElementById('sidebar');s.style.display='flex';}</script>

    {{-- ── MAIN AREA ────────────────────────────────────────────────── --}}
    <div id="main-wrapper" class="flex-1 flex flex-col overflow-hidden" style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between flex-shrink-0" style="gap:0.75rem;">
            {{-- Botón hamburguesa (solo móvil) --}}
            <button id="hamburger" onclick="openSidebar()"
                    style="display:none;align-items:center;justify-content:center;width:36px;height:36px;border-radius:0.5rem;border:none;cursor:pointer;background:#0b1622;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-gray-800" style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">@yield('title', 'Dashboard')</h1>
            <span class="text-sm text-gray-500" style="flex-shrink:0;">{{ now()->format('d/m/Y') }}</span>
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
// ── Sidebar móvil ────────────────────────────────────────────────────────
window.openSidebar = function() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebar-overlay').classList.add('visible');
};
window.closeSidebar = function() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.remove('visible');
};
document.getElementById('sidebar').querySelectorAll('nav a').forEach(function(a) {
    a.addEventListener('click', function() { if (window.innerWidth < 768) closeSidebar(); });
});
</script>
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
    const failed = [];
    let synced = 0;

    for (const entry of pending) {
        try {
            const body = new URLSearchParams(entry.data);
            body.set('_token', csrfToken);
            const res = await fetch(entry.url, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (res.ok || res.redirected) {
                synced++;
            } else {
                failed.push(entry);
            }
        } catch (e) {
            failed.push(entry);
        }
    }

    // Solo conservar los que fallaron
    if (failed.length > 0) {
        localStorage.setItem('pending_logs', JSON.stringify(failed));
    } else {
        localStorage.removeItem('pending_logs');
    }

    if (synced > 0) {
        alert(`✅ ${synced} registro(s) sincronizados.${failed.length ? ` ${failed.length} no pudieron enviarse.` : ''}`);
        window.location.reload();
    } else {
        alert('No se pudieron sincronizar los registros. Verifica tu conexión.');
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
