@extends('layouts.app')
@section('title', 'Panel Gerencial')

@section('content')
<div class="pt-4 space-y-5">

    {{-- ── TARJETAS RESUMEN ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

        {{-- Total rigs --}}
        <div class="rounded-xl p-4 text-center" style="background:#0d1f2d;border:1px solid #1a3040;">
            <div class="text-3xl font-black font-mono text-white">{{ $totalRigs }}</div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Total Rigs</div>
        </div>

        {{-- Operando hoy --}}
        <div class="rounded-xl p-4 text-center" style="background:#0d1f2d;border:1px solid #1a3040;">
            <div class="text-3xl font-black font-mono" style="color:#4ade80;">{{ $rigsOpHoy }}</div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Op. hoy</div>
        </div>

        {{-- Bombas críticas --}}
        <div class="rounded-xl p-4 text-center"
             style="background:#0d1f2d;border:1px solid {{ $rigsPumpCritical > 0 ? '#7f1d1d' : '#1a3040' }};">
            <div class="text-3xl font-black font-mono" style="color:{{ $rigsPumpCritical > 0 ? '#FF4D2E' : '#6b9e82' }};">
                {{ $rigsPumpCritical }}
            </div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Bombas críticas</div>
        </div>

        {{-- Bombas en alerta --}}
        <div class="rounded-xl p-4 text-center"
             style="background:#0d1f2d;border:1px solid {{ $rigsPumpWarning > 0 ? '#713f12' : '#1a3040' }};">
            <div class="text-3xl font-black font-mono" style="color:{{ $rigsPumpWarning > 0 ? '#EAB308' : '#6b9e82' }};">
                {{ $rigsPumpWarning }}
            </div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Bombas alerta</div>
        </div>

        {{-- Cable crítico --}}
        <div class="rounded-xl p-4 text-center"
             style="background:#0d1f2d;border:1px solid {{ $rigsCableCrit > 0 ? '#7f1d1d' : '#1a3040' }};">
            <div class="text-3xl font-black font-mono" style="color:{{ $rigsCableCrit > 0 ? '#FF4D2E' : '#6b9e82' }};">
                {{ $rigsCableCrit }}
            </div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Cable crítico</div>
        </div>

        {{-- Cable en alerta --}}
        <div class="rounded-xl p-4 text-center"
             style="background:#0d1f2d;border:1px solid {{ $rigsCableWarn > 0 ? '#713f12' : '#1a3040' }};">
            <div class="text-3xl font-black font-mono" style="color:{{ $rigsCableWarn > 0 ? '#EAB308' : '#6b9e82' }};">
                {{ $rigsCableWarn }}
            </div>
            <div class="text-xs mt-1 font-medium uppercase tracking-wide" style="color:#6b9e82;">Cable alerta</div>
        </div>
    </div>

    {{-- ── ENCABEZADOS DE COLUMNA ──────────────────────────────────────────── --}}
    <div class="hidden lg:grid grid-cols-3 gap-1 px-1">
        <div class="text-xs font-bold uppercase tracking-widest px-4" style="color:#6b9e82;">Equipo</div>
        <div class="text-xs font-bold uppercase tracking-widest px-4 text-center" style="color:#4ade80;">Bombas / Componentes</div>
        <div class="text-xs font-bold uppercase tracking-widest px-4 text-center" style="color:#06B6D4;">Cable TM</div>
    </div>

    {{-- ── CARDS POR RIG ────────────────────────────────────────────────────── --}}
    @forelse($rigCards as $card)
    @php
        $r = $card['rig'];

        $pBorder = match($card['pumpStatus']) {
            'critical' => '#7f1d1d', 'warning' => '#713f12', default => '#1a3040'
        };
        $cBorder = match($card['cableStatus']) {
            'critical' => '#7f1d1d', 'warning' => '#713f12', default => '#003344'
        };

        $pIconColor = match($card['pumpStatus']) {
            'critical' => '#FF4D2E', 'warning' => '#EAB308', default => '#4ade80'
        };
        $cColor = match($card['cableStatus']) {
            'critical' => '#FF4D2E', 'warning' => '#EAB308',
            'sin-cable' => '#4a8a9e', default => '#06B6D4'
        };

        $tmBarWidth = min(100, $card['tmPct']);
    @endphp

    <div class="rounded-2xl overflow-hidden" style="background:#0d1f2d;border:1px solid #1a3040;">

        {{-- Header del rig --}}
        <div class="px-5 py-3 flex items-center justify-between" style="background:#0a1825;border-bottom:1px solid #1a3040;">
            <div>
                <span class="font-bold text-white text-sm">{{ $r->name }}</span>
                @if($r->location)
                <span class="text-xs ml-2" style="color:#6b9e82;">{{ $r->location }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($card['hoursToday'] > 0)
                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#0d2a00;color:#4ade80;border:1px solid #166534;">
                    ● {{ $card['hoursToday'] }}h hoy
                </span>
                @else
                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#0a1825;color:#4a8a9e;border:1px solid #1a3040;">
                    Sin registro hoy
                </span>
                @endif
            </div>
        </div>

        {{-- Cuerpo: 2 secciones --}}
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- SECCIÓN BOMBAS --}}
            <div class="p-4 space-y-3" style="border-right:1px solid #1a3040;">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="{{ $pIconColor }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#4ade80;">Bombas</span>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-lg py-2" style="background:#0a1825;">
                        <div class="text-xl font-black font-mono text-white">{{ $card['pumpCount'] }}</div>
                        <div class="text-xs" style="color:#6b9e82;">Bombas</div>
                    </div>
                    <div class="rounded-lg py-2" style="background:#0a1825;border:1px solid {{ $card['criticalComp'] > 0 ? '#7f1d1d' : '#1a3040' }};">
                        <div class="text-xl font-black font-mono" style="color:{{ $card['criticalComp'] > 0 ? '#FF4D2E' : '#6b9e82' }};">
                            {{ $card['criticalComp'] }}
                        </div>
                        <div class="text-xs" style="color:#6b9e82;">Críticos</div>
                    </div>
                    <div class="rounded-lg py-2" style="background:#0a1825;border:1px solid {{ $card['warningComp'] > 0 ? '#713f12' : '#1a3040' }};">
                        <div class="text-xl font-black font-mono" style="color:{{ $card['warningComp'] > 0 ? '#EAB308' : '#6b9e82' }};">
                            {{ $card['warningComp'] }}
                        </div>
                        <div class="text-xs" style="color:#6b9e82;">Alertas</div>
                    </div>
                </div>

                @if($card['pumpStatus'] !== 'ok')
                <div class="text-xs px-2 py-1 rounded font-bold text-center"
                     style="background:{{ $card['pumpStatus'] === 'critical' ? '#1a0000' : '#1a1000' }};
                            color:{{ $card['pumpStatus'] === 'critical' ? '#FF4D2E' : '#EAB308' }};
                            border:1px solid {{ $card['pumpStatus'] === 'critical' ? '#7f1d1d' : '#713f12' }};">
                    {{ $card['pumpStatus'] === 'critical' ? '⚠ CRÍTICO — Requiere atención inmediata' : '⚠ ALERTA — Planificar mantenimiento' }}
                </div>
                @endif

                <a href="{{ route('alerts.index') }}"
                   class="block text-xs text-center py-1 rounded hover:underline" style="color:#4a8a9e;">
                    Ver alertas →
                </a>
            </div>

            {{-- SECCIÓN CABLE TM --}}
            <div class="p-4 space-y-3">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="{{ $cColor }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#06B6D4;">Cable TM</span>
                </div>

                @if($card['cable'])
                <div class="flex items-center justify-between text-xs mb-1">
                    <span style="color:#4a8a9e;">{{ $card['cable']->serial }}</span>
                    <span class="font-mono font-bold" style="color:{{ $cColor }};">{{ $card['tmPct'] }}%</span>
                </div>

                {{-- Barra de progreso --}}
                <div class="rounded-full overflow-hidden h-3" style="background:#001a1f;">
                    <div class="h-3 rounded-full transition-all"
                         style="width:{{ $tmBarWidth }}%;background:{{ $cColor }};"></div>
                </div>

                <div class="flex justify-between text-xs font-mono">
                    <span style="color:#4a8a9e;">{{ number_format($card['tmAcum'], 1) }} TM</span>
                    <span style="color:#4a8a9e;">Máx {{ number_format($card['tmMax'], 0) }} TM</span>
                </div>

                @if($card['cableStatus'] !== 'ok')
                <div class="text-xs px-2 py-1 rounded font-bold text-center"
                     style="background:{{ $card['cableStatus'] === 'critical' ? '#001400' : '#001400' }};
                            color:{{ $card['cableStatus'] === 'critical' ? '#FF4D2E' : '#EAB308' }};
                            border:1px solid {{ $card['cableStatus'] === 'critical' ? '#7f1d1d' : '#713f12' }};">
                    {{ $card['cableStatus'] === 'critical' ? '⚠ CRÍTICO — Programar corte' : '⚠ ALERTA — Preparar plan de corte' }}
                </div>
                @endif

                <a href="{{ route('cable.dashboard') }}"
                   class="block text-xs text-center py-1 rounded hover:underline" style="color:#4a8a9e;">
                    Ver detalle TM →
                </a>

                @else
                <div class="text-center py-3">
                    <div class="text-xs" style="color:#4a8a9e;">Sin cable activo registrado</div>
                    <a href="{{ route('cable.dashboard') }}"
                       class="text-xs mt-1 inline-block hover:underline" style="color:#06B6D4;">
                        Registrar cable →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="rounded-2xl p-10 text-center" style="background:#0d1f2d;border:1px solid #1a3040;">
        <p class="text-sm" style="color:#6b9e82;">No hay rigs configurados aún.</p>
    </div>
    @endforelse

</div>
@endsection
