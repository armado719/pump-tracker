@extends('layouts.app')
@section('title', 'Centro de Alertas')
@section('header', 'Centro de Alertas')

@section('content')
<div class="pt-4 space-y-6">

    {{-- ── Fila de resumen ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-4">

        {{-- Bombas --}}
        <div class="flex-1 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 min-w-40">
            <span class="text-3xl">🔴</span>
            <div>
                <p class="text-2xl font-bold text-red-700">{{ $criticalCount }}</p>
                <p class="text-xs text-red-600 uppercase tracking-wide font-medium">Bombas Críticas</p>
            </div>
        </div>
        <div class="flex-1 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3 min-w-40">
            <span class="text-3xl">⚠️</span>
            <div>
                <p class="text-2xl font-bold text-amber-700">{{ $warningCount }}</p>
                <p class="text-xs text-amber-600 uppercase tracking-wide font-medium">Bombas en Alerta</p>
            </div>
        </div>

        {{-- Separador visual --}}
        <div class="hidden md:block h-12 w-px" style="background:#003344;"></div>

        {{-- Cable TM --}}
        <div class="flex-1 rounded-xl p-4 flex items-center gap-3 min-w-40"
             style="background:#001a1f;border:1px solid #003344;">
            <span class="text-3xl">🔴</span>
            <div>
                <p class="text-2xl font-bold" style="color:#FF4D2E;">{{ $tmCriticalCount }}</p>
                <p class="text-xs uppercase tracking-wide font-medium" style="color:#4a8a9e;">Cables TM Críticos</p>
            </div>
        </div>
        <div class="flex-1 rounded-xl p-4 flex items-center gap-3 min-w-40"
             style="background:#001a1f;border:1px solid #003344;">
            <span class="text-3xl">⚡</span>
            <div>
                <p class="text-2xl font-bold" style="color:#EAB308;">{{ $tmWarningCount }}</p>
                <p class="text-xs uppercase tracking-wide font-medium" style="color:#4a8a9e;">Cables TM en Alerta</p>
            </div>
        </div>

        {{-- Botón notificar --}}
        <form method="POST" action="{{ route('alerts.notificar') }}" class="flex-shrink-0">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-sm transition shadow-sm"
                    style="background:#0b1622;border:1px solid #1a3040;color:#9ab8a8;"
                    onmouseover="this.style.borderColor='#4ade80';this.style.color='#4ade80'"
                    onmouseout="this.style.borderColor='#1a3040';this.style.color='#9ab8a8'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Notificar por email
            </button>
        </form>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- SECCIÓN 1 — BOMBAS                                                 --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header de sección --}}
        <div class="flex items-center gap-3 px-5 py-3 border-b border-gray-200"
             style="background:linear-gradient(90deg,#f0fdf4 0%,#ffffff 100%);">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 10h16M4 14h8m-8 4h4"/>
            </svg>
            <h2 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Alertas de Bombas — Componentes</h2>
            <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-bold
                {{ ($criticalCount + $warningCount) > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ $criticalCount + $warningCount }} alerta(s)
            </span>
        </div>

        @if(count($alerts) === 0)
        <div class="p-8 text-center">
            <p class="text-4xl mb-2">✅</p>
            <p class="text-green-700 font-semibold">Todos los componentes de bomba están en estado OK</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-left">Rig</th>
                        <th class="px-4 py-3 text-left">Bomba</th>
                        <th class="px-4 py-3 text-left">Conjunto</th>
                        <th class="px-4 py-3 text-left">Componente</th>
                        <th class="px-4 py-3 text-right">Horas Acum.</th>
                        <th class="px-4 py-3 text-center">⚠ Alerta</th>
                        <th class="px-4 py-3 text-center">🔴 Crítico</th>
                        <th class="px-4 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($alerts as $alert)
                    <tr class="{{ $alert['status'] === 'critical' ? 'bg-red-50' : 'bg-amber-50' }} hover:opacity-90">
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold border {{ $alert['badge'] }}">
                                {{ $alert['label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $alert['rig'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $alert['pump'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $alert['assembly'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $alert['component'] }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">{{ number_format($alert['hours'], 2) }}h</td>
                        <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs">{{ $alert['warning'] }}h</td>
                        <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs">{{ $alert['critical'] }}h</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('pumps.show', $alert['pumpId']) }}"
                               class="text-blue-600 hover:underline text-xs font-medium">Ver bomba →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- SECCIÓN 2 — CABLE TM                                               --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl overflow-hidden shadow-sm" style="background:#001a1f;border:1px solid #003344;">

        {{-- Header de sección --}}
        <div class="flex items-center gap-3 px-5 py-3" style="border-bottom:1px solid #003344;
             background:linear-gradient(90deg,#002030 0%,#001a1f 100%);">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="#06B6D4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h2 class="font-bold text-sm uppercase tracking-wider" style="color:#06B6D4;">
                Alertas de Cable TM — Ton-Millas Acumuladas
            </h2>
            <span class="ml-auto text-xs px-2 py-0.5 rounded-full font-bold"
                  style="{{ ($tmCriticalCount + $tmWarningCount) > 0
                    ? 'background:#1a0800;color:#EAB308;border:1px solid #713f12;'
                    : 'background:#001a2e;color:#06B6D4;border:1px solid #003344;' }}">
                {{ $tmCriticalCount + $tmWarningCount }} alerta(s)
            </span>
        </div>

        @if(count($alertasCableTm) === 0)
        <div class="p-8 text-center">
            <p class="text-4xl mb-2">✅</p>
            <p class="font-semibold" style="color:#06B6D4;">Todos los cables están dentro del límite operacional</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#00111a;">
                    <tr>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Estado</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Equipo</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Serial Cable</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Grado</th>
                        <th class="px-4 py-3 text-xs uppercase" style="color:#4a8a9e;">% Vida Útil</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">TM Acum.</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">TM Máx.</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alertasCableTm as $tm)
                    @php
                        $tmColor  = $tm['status'] === 'critical' ? '#FF4D2E' : '#EAB308';
                        $tmBarBg  = $tm['status'] === 'critical' ? '#1a0000' : '#1a1000';
                        $barWidth = min(100, $tm['tmPct']);
                    @endphp
                    <tr style="border-top:1px solid #003344;
                        background:{{ $tm['status'] === 'critical' ? '#0d0500' : '#0d0b00' }};">
                        <td class="px-4 py-3 text-center">
                            @if($tm['status'] === 'critical')
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                      style="background:#1a0000;color:#FF4D2E;border:1px solid #7f1d1d;">CRÍTICO</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                      style="background:#1a1000;color:#EAB308;border:1px solid #713f12;">ALERTA</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-xs" style="color:#F0EDE8;">{{ $tm['rig'] }}</td>
                        <td class="px-4 py-3 font-mono text-xs" style="color:#06B6D4;">{{ $tm['serial'] }}</td>
                        <td class="px-4 py-3 text-xs" style="color:#4a8a9e;">{{ $tm['grado'] }}</td>
                        <td class="px-4 py-3" style="min-width:160px;">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 rounded-full h-2" style="background:#003344;">
                                    <div class="h-2 rounded-full transition-all"
                                         style="width:{{ $barWidth }}%;background:{{ $tmColor }};"></div>
                                </div>
                                <span class="text-xs font-bold font-mono w-12 text-right"
                                      style="color:{{ $tmColor }};">{{ $tm['tmPct'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-xs font-bold"
                            style="color:{{ $tmColor }};">{{ number_format($tm['tmAcum'], 2) }} TM</td>
                        <td class="px-4 py-3 text-right font-mono text-xs" style="color:#4a8a9e;">
                            {{ number_format($tm['tmMax'], 0) }} TM
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('cable.seleccionar-rig') }}" class="inline">
                                @csrf
                                <input type="hidden" name="rig_id" value="{{ $tm['rigId'] }}">
                                <button type="submit" class="text-xs font-medium hover:underline cursor-pointer"
                                        style="color:#06B6D4;background:none;border:none;padding:0;">
                                    Ver cable →
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
