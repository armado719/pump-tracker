@extends('layouts.app')
@section('title', 'Historial de Reemplazos — ' . $pump->rig->name . ' Bomba #' . $pump->number)

@section('content')
<div class="pt-4 space-y-5">

    {{-- Encabezado --}}
    <div class="flex justify-between items-center">
        <a href="{{ route('pumps.show', $pump) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver a la bomba</a>
        <div class="text-right">
            <p class="text-xs text-gray-400">{{ $pump->rig->name }} — Bomba #{{ $pump->number }}</p>
            <p class="text-xs text-gray-400">{{ $events->count() }} reemplazo(s) registrado(s)</p>
        </div>
    </div>

    @if($events->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="text-4xl mb-3">🔧</div>
        <p class="text-gray-500 text-sm">No hay reemplazos registrados para esta bomba.</p>
        <p class="text-gray-400 text-xs mt-1">Los cambios de componentes aparecerán aquí.</p>
    </div>
    @else

    {{-- Resumen por tipo de componente --}}
    @php
        $byType = $events->groupBy(fn($e) => $e->component->type_label);
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($byType as $typeName => $group)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $group->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $typeName }}</p>
        </div>
        @endforeach
    </div>

    {{-- Tabla principal --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-orange-50 border-b border-orange-100 flex items-center gap-2">
            <span class="text-orange-600 text-lg">🔧</span>
            <h3 class="font-semibold text-orange-900">Historial Completo de Reemplazos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Conjunto</th>
                        <th class="px-4 py-3 text-left">Componente</th>
                        <th class="px-4 py-3 text-right">Horas al cambio</th>
                        <th class="px-4 py-3 text-left">Serial anterior</th>
                        <th class="px-4 py-3 text-left">Serial nuevo</th>
                        <th class="px-4 py-3 text-left">Notas</th>
                        <th class="px-4 py-3 text-center">Origen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($events as $event)
                    @php
                        $assembly = $event->component->assembly;
                        $posLabel = match($assembly->position) {
                            'left'   => 'Izquierdo',
                            'middle' => 'Medio',
                            'right'  => 'Derecho',
                            default  => $assembly->position,
                        };
                    @endphp
                    <tr class="hover:bg-orange-50">
                        <td class="px-4 py-3 font-mono text-xs">
                            {{ $event->created_at->format('d/m/Y') }}
                            <span class="block text-gray-400">{{ $event->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $posLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $event->component->type_label }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-orange-600">
                            {{ number_format($event->hours_before, 2) }}h
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            {{-- El serial anterior era el del componente antes del cambio --}}
                            {{ $event->notes && str_contains($event->notes, 'serial:')
                                ? trim(explode('serial:', $event->notes)[1])
                                : '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700 font-medium">
                            {{ $event->new_serial ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 max-w-xs">
                            {{ $event->notes ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($event->dailyLog)
                                <a href="{{ route('pumps.logs.show', [$pump, $event->dailyLog]) }}"
                                   class="text-xs text-blue-600 hover:underline">
                                    Día {{ $event->dailyLog->day_number }}
                                </a>
                            @else
                                <span class="text-xs text-orange-600 font-medium">Independiente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
