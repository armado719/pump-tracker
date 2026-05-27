@extends('layouts.app')
@section('title', 'Registro Día ' . $log->day_number)
@section('header', 'Registro Día ' . $log->day_number . ' — ' . $log->log_date->format('d/m/Y'))

@section('content')
<div class="pt-4 space-y-4">
    <a href="{{ route('pumps.logs.index', $pump) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
            <div><span class="text-xs text-gray-500 uppercase font-medium block">Día</span><span class="font-bold font-mono text-lg">{{ $log->day_number }}</span></div>
            <div><span class="text-xs text-gray-500 uppercase font-medium block">Fecha</span><span class="font-bold">{{ $log->log_date->format('d/m/Y') }}</span></div>
            <div><span class="text-xs text-gray-500 uppercase font-medium block">Horas Trabajadas</span><span class="font-bold font-mono text-blue-600 text-lg">{{ number_format($log->hours_worked,2) }}h</span></div>
            <div><span class="text-xs text-gray-500 uppercase font-medium block">Horas Acumuladas</span><span class="font-bold font-mono text-lg">{{ number_format($log->accumulated_hours,2) }}h</span></div>
            <div><span class="text-xs text-gray-500 uppercase font-medium block">Presión Dampener</span><span class="font-bold">{{ $log->dampener_pressure ? $log->dampener_pressure.' PSI' : '—' }}</span></div>
        </div>
        @if($log->comments)
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-900">
            💬 {{ $log->comments }}
        </div>
        @endif
    </div>

    @foreach(['left'=>'Izquierdo','middle'=>'Medio','right'=>'Derecho'] as $pos => $label)
    @if(isset($assembliesByPosition[$pos]))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-blue-50 border-b border-blue-100">
            <h3 class="font-semibold text-blue-900">Conjunto {{ $label }}</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Componente</th>
                    <th class="px-4 py-2 text-right">Horas Acumuladas</th>
                    <th class="px-4 py-2 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($assembliesByPosition[$pos] as $ch)
                @php
                    $status = \App\Services\ThresholdService::getStatus($ch->component->type, $ch->hours_accumulated);
                    $badge  = \App\Services\ThresholdService::getStatusBadgeClass($status);
                    $lbl    = \App\Services\ThresholdService::getStatusLabel($status);
                @endphp
                <tr>
                    <td class="px-4 py-2 font-medium">{{ $ch->component->type_label }}</td>
                    <td class="px-4 py-2 text-right font-mono font-bold">{{ number_format($ch->hours_accumulated,2) }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $badge }}">{{ $lbl }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach
</div>
@endsection
