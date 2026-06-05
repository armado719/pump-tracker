@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard — Estado de Rigs')

@section('content')
<div class="space-y-6 pt-4">

    {{-- Cards resumen --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Rigs Activos</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalRigs }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Bombas Operando Hoy</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $pumpsToday }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Alertas Críticas</p>
            <p class="text-3xl font-bold mt-1 {{ $criticalCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $criticalCount }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Horas Prom. (7d)</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $avgHours }}h</p>
        </div>
        <div class="rounded-xl p-5 shadow-sm" style="background:#001a1f;border:1px solid #003344;">
            <p class="text-xs uppercase tracking-wide font-medium" style="color:#4a8a9e;">Cables TM — Alertas</p>
            <p class="text-3xl font-bold mt-1" style="color:{{ $tmAlertCount > 0 ? '#EAB308' : '#06B6D4' }};">
                {{ $tmAlertCount }}
            </p>
        </div>
    </div>

    {{-- Tabla estado rigs + Gráfica --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Tabla estado --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Estado de Rigs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Rig</th>
                            <th class="px-4 py-3 text-left">Pozo</th>
                            <th class="px-4 py-3 text-center">Bombas</th>
                            <th class="px-4 py-3 text-right">Horas Hoy</th>
                            <th class="px-4 py-3 text-center">Última Act.</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($rigStatus as $rs)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('rigs.show', $rs['rig']) }}" class="text-blue-600 hover:underline">
                                    {{ $rs['rig']->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $rs['well'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $rs['pumps'] }}</td>
                            <td class="px-4 py-3 text-right font-mono">{{ $rs['hoursToday'] }}h</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $rs['lastUpdate'] }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($rs['status'] === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">● Activo</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">○ Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Panel alertas recientes --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Alertas Recientes</h3>
                <a href="{{ route('alerts.index') }}" class="text-xs text-blue-600 hover:underline">Ver todas →</a>
            </div>
            <div class="p-4 space-y-3">
                @forelse($topAlerts as $alert)
                <div class="p-3 rounded-lg border {{ $alert['badge'] }} text-xs">
                    <div class="font-semibold">{{ $alert['label'] }}</div>
                    <div class="text-gray-700 mt-0.5">{{ $alert['rig'] }} — {{ $alert['pump'] }}</div>
                    <div>{{ $alert['assembly'] }}: {{ $alert['component'] }}</div>
                    <div class="font-mono mt-1">{{ number_format($alert['hours'], 2) }}h acumuladas</div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">✅ Sin alertas activas</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Estado Cable TM por Rig --}}
    <div class="rounded-xl overflow-hidden shadow-sm" style="background:#001a1f;border:1px solid #003344;">
        <div class="px-5 py-3 flex items-center justify-between" style="border-bottom:1px solid #003344;">
            <h3 class="font-semibold text-sm" style="color:#4a8a9e;">
                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="#06B6D4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Estado Cable TM — por Equipo
            </h3>
            <a href="{{ route('cable.dashboard') }}" class="text-xs hover:underline" style="color:#06B6D4;">
                Ver detalle →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#00111a;">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Equipo</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Cable</th>
                        <th class="px-4 py-2 text-xs uppercase" style="color:#4a8a9e;">Progreso TM</th>
                        <th class="px-4 py-2 text-right text-xs uppercase" style="color:#4a8a9e;">Acumulado</th>
                        <th class="px-4 py-2 text-right text-xs uppercase" style="color:#4a8a9e;">Máximo</th>
                        <th class="px-4 py-2 text-center text-xs uppercase" style="color:#4a8a9e;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cableTmStatus as $row)
                    @php
                        $color = match($row['status']) {
                            'critical'  => '#FF4D2E',
                            'warning'   => '#EAB308',
                            'sin-cable' => '#4a8a9e',
                            default     => '#06B6D4',
                        };
                        $barW = min(100, $row['tmPct']);
                    @endphp
                    <tr style="border-top:1px solid #003344;">
                        <td class="px-4 py-2 font-medium text-xs" style="color:#F0EDE8;">{{ $row['rig']->name }}</td>
                        <td class="px-4 py-2 font-mono text-xs" style="color:#06B6D4;">
                            {{ $row['cable']?->serial ?? '—' }}
                        </td>
                        <td class="px-4 py-2" style="min-width:140px;">
                            @if($row['cable'])
                            <div class="flex items-center gap-2">
                                <div class="flex-1 rounded-full h-2" style="background:#003344;">
                                    <div class="h-2 rounded-full" style="width:{{ $barW }}%;background:{{ $color }};"></div>
                                </div>
                                <span class="text-xs font-bold font-mono w-10 text-right" style="color:{{ $color }};">
                                    {{ $row['tmPct'] }}%
                                </span>
                            </div>
                            @else
                            <span class="text-xs" style="color:#4a8a9e;">Sin cable activo</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#F0EDE8;">
                            {{ $row['cable'] ? number_format($row['tmAcum'], 2) . ' TM' : '—' }}
                        </td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#4a8a9e;">
                            {{ $row['cable'] ? number_format($row['tmMax'], 0) . ' TM' : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($row['status'] === 'critical')
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#1a0000;color:#FF4D2E;border:1px solid #7f1d1d;">CRÍTICO</span>
                            @elseif($row['status'] === 'warning')
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#1a1000;color:#EAB308;border:1px solid #713f12;">ALERTA</span>
                            @elseif($row['status'] === 'ok')
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#001a1f;color:#06B6D4;border:1px solid #003344;">OK</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full" style="color:#4a8a9e;">Sin cable</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Gráfica horas acumuladas --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Horas de Trabajo — Últimos 30 días</h3>
        <canvas id="hoursChart" height="80"></canvas>
    </div>

</div>
@endsection

@push('scripts')
<script>
const labels = @json($labels);
const datasets = @json($chartDatasets);
const colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'];

new Chart(document.getElementById('hoursChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: datasets.map((ds, i) => ({
            label: ds.label,
            data: ds.data,
            backgroundColor: colors[i % colors.length] + '80',
            borderColor: colors[i % colors.length],
            borderWidth: 1,
        }))
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Horas' } } }
    }
});
</script>
@endpush
