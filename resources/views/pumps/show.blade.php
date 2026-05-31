@extends('layouts.app')
@section('title', 'Bomba #' . $pump->number . ' — ' . $pump->rig->name)
@section('header', $pump->rig->name . ' — Bomba #' . $pump->number)
@section('breadcrumb', 'Rigs / ' . $pump->rig->name . ' / Bomba #' . $pump->number)

@section('content')
<div class="pt-4 space-y-5">

    {{-- Acción principal --}}
    <div class="flex justify-end gap-2">
        <a href="{{ route('pumps.personnel.create', $pump) }}"
           class="text-sm border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-gray-600 transition">
            👥 Personal
        </a>
        <a href="{{ route('pumps.logs.create', $pump) }}"
           class="text-sm bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
            📝 Registrar día
        </a>
    </div>

    {{-- Info bomba --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Marca</span>{{ $pump->brand ?? '—' }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Modelo</span>{{ $pump->model ?? '—' }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Serial</span><span class="font-mono">{{ $pump->serial ?? '—' }}</span></div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Activo Fijo</span>{{ $pump->active_fixed_id ?? '—' }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Diámetro Camisa</span>{{ $pump->liner_diameter ?? '—' }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Horas Base</span><span class="font-mono font-bold text-blue-600">{{ number_format($pump->base_accumulated_hours, 2) }}h</span></div>
            @if($currentPersonnel)
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Supervisor Día</span>{{ $currentPersonnel->supervisor_day ?? '—' }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium mb-1">Encuellador Día</span>{{ $currentPersonnel->encuellador_day ?? '—' }}</div>
            @endif
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Horas Diarias (último mes)</h3>
            <canvas id="dailyChart" height="120"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Estado de Componentes</h3>
            <canvas id="componentChart" height="120"></canvas>
        </div>
    </div>

    {{-- Estado componentes por conjunto --}}
    @foreach($pump->assemblies as $assembly)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-blue-50 border-b border-blue-100">
            <h3 class="font-semibold text-blue-900">Conjunto {{ $assembly->position_label }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Sección</th>
                        <th class="px-4 py-2 text-left">Componente</th>
                        <th class="px-4 py-2 text-left">Serial</th>
                        <th class="px-4 py-2 text-right">Horas Acum.</th>
                        <th class="px-4 py-2 text-center">Estado</th>
                        <th class="px-4 py-2 text-right">Umbral ⚠ / 🔴</th>
                        <th class="px-4 py-2 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($assembly->components as $comp)
                    @php
                        $hours = $comp->componentHours->first()?->hours_accumulated ?? $comp->installed_at_hours;
                        $status = \App\Services\ThresholdService::getStatus($comp->type, $hours);
                        $badge  = \App\Services\ThresholdService::getStatusBadgeClass($status);
                        $label  = \App\Services\ThresholdService::getStatusLabel($status);
                        $thresh = \App\Services\ThresholdService::getThresholds($comp->type);
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $status === 'critical' ? 'bg-red-50' : ($status === 'warning' ? 'bg-amber-50' : '') }}">
                        <td class="px-4 py-2 text-gray-500">{{ $comp->section_label }}</td>
                        <td class="px-4 py-2 font-medium">{{ $comp->type_label }}</td>
                        <td class="px-4 py-2 font-mono text-xs text-gray-500">{{ $comp->serial ?? '—' }}</td>
                        <td class="px-4 py-2 text-right font-mono font-bold">{{ number_format($hours, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $badge }}">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-2 text-right text-xs text-gray-400">
                            {{ $thresh['warning'] }}h / {{ $thresh['critical'] }}h
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('components.replace.form', [$pump, $comp]) }}"
                               class="text-xs text-orange-600 hover:text-orange-800 hover:underline font-medium"
                               title="Registrar reemplazo de este componente">
                                🔧 Cambiar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    {{-- Historial mantenimiento --}}
    @if($maintenanceHistory->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Historial de Mantenimiento</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Componente</th>
                    <th class="px-4 py-3 text-right">Horas antes</th>
                    <th class="px-4 py-3 text-left">Nuevo Serial</th>
                    <th class="px-4 py-3 text-left">Notas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($maintenanceHistory as $event)
                <tr>
                    <td class="px-4 py-3">{{ $event->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $event->component->type_label }}</td>
                    <td class="px-4 py-3 text-right font-mono">{{ number_format($event->hours_before, 2) }}h</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $event->new_serial ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $event->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Registros diarios --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Registros Diarios</h3>
            <a href="{{ route('pumps.logs.index', $pump) }}" class="text-xs text-blue-600 hover:underline">Ver todos →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Día</th>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-right">Horas</th>
                        <th class="px-4 py-3 text-right">Acumuladas</th>
                        <th class="px-4 py-3 text-center">Dampener</th>
                        <th class="px-4 py-3 text-left">Comentarios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pump->dailyLogs->take(15) as $log)
                    <tr class="hover:bg-gray-50 {{ $log->comments ? 'bg-yellow-50' : '' }}">
                        <td class="px-4 py-2 text-center font-mono">{{ $log->day_number }}</td>
                        <td class="px-4 py-2">{{ $log->log_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right font-mono {{ $log->hours_worked > 0 ? 'text-green-600 font-bold' : 'text-gray-400' }}">
                            {{ number_format($log->hours_worked, 2) }}h
                        </td>
                        <td class="px-4 py-2 text-right font-mono">{{ number_format($log->accumulated_hours, 2) }}h</td>
                        <td class="px-4 py-2 text-center text-gray-500">{{ $log->dampener_pressure ? $log->dampener_pressure.' PSI' : '—' }}</td>
                        <td class="px-4 py-2 text-gray-600 text-xs">{{ $log->comments ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Gráfica horas diarias
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Horas trabajadas',
            data: @json($chartHours),
            backgroundColor: @json($chartHours).map(h => h > 0 ? '#22c55e80' : '#e5e7eb'),
            borderColor: @json($chartHours).map(h => h > 0 ? '#16a34a' : '#9ca3af'),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Gráfica componentes horizontal
const compStats = @json($componentStats);
new Chart(document.getElementById('componentChart'), {
    type: 'bar',
    data: {
        labels: compStats.map(c => c.label.split(' — ')[1] ?? c.label),
        datasets: [
            {
                label: 'Horas Acumuladas',
                data: compStats.map(c => c.hours),
                backgroundColor: compStats.map(c => c.status === 'critical' ? '#fca5a5' : c.status === 'warning' ? '#fde68a' : '#86efac'),
                borderColor: compStats.map(c => c.status === 'critical' ? '#ef4444' : c.status === 'warning' ? '#f59e0b' : '#22c55e'),
                borderWidth: 1,
            }
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, title: { display: true, text: 'Horas' } } }
    }
});
</script>
@endpush
