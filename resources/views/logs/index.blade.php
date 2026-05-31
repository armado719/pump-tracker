@extends('layouts.app')
@section('title', 'Registros — Bomba #' . $pump->number)
@section('header', 'Registros Diarios — ' . $pump->rig->name . ' Bomba #' . $pump->number)

@section('content')
<div class="pt-4">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('pumps.show', $pump) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver a la bomba</a>
        <a href="{{ route('pumps.logs.create', $pump) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg font-medium">📝 Nuevo Registro</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-center">Día</th>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Pozo</th>
                    <th class="px-4 py-3 text-right">Horas</th>
                    <th class="px-4 py-3 text-right">Acumuladas</th>
                    <th class="px-4 py-3 text-center">Dampener</th>
                    <th class="px-4 py-3 text-left">Comentarios</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50 {{ $log->comments ? 'bg-yellow-50' : '' }}">
                    <td class="px-4 py-2 text-center font-mono font-bold">{{ $log->day_number }}</td>
                    <td class="px-4 py-2">{{ $log->log_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $log->well?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-right font-mono {{ $log->hours_worked > 0 ? 'text-green-600 font-bold' : 'text-gray-400' }}">
                        {{ number_format($log->hours_worked, 2) }}h
                    </td>
                    <td class="px-4 py-2 text-right font-mono">{{ number_format($log->accumulated_hours, 2) }}h</td>
                    <td class="px-4 py-2 text-center text-gray-500 font-mono text-xs">{{ $log->dampener_pressure ? $log->dampener_pressure.' PSI' : '—' }}</td>
                    <td class="px-4 py-2 text-gray-600 text-xs max-w-xs truncate">{{ $log->comments ?? '' }}</td>
                    <td class="px-4 py-2 text-center flex items-center justify-center gap-3">
                        <a href="{{ route('pumps.logs.show', [$pump, $log]) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                        <a href="{{ route('pumps.logs.edit', [$pump, $log]) }}" class="text-yellow-600 hover:underline text-xs">✏️ Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
