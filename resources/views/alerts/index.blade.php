@extends('layouts.app')
@section('title', 'Centro de Alertas')
@section('header', 'Centro de Alertas')

@section('content')
<div class="pt-4 space-y-5">
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
            <span class="text-3xl">🔴</span>
            <div>
                <p class="text-2xl font-bold text-red-700">{{ $criticalCount }}</p>
                <p class="text-sm text-red-600">Componentes Críticos</p>
            </div>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
            <span class="text-3xl">⚠️</span>
            <div>
                <p class="text-2xl font-bold text-amber-700">{{ $warningCount }}</p>
                <p class="text-sm text-amber-600">Componentes en Alerta</p>
            </div>
        </div>
    </div>

    @if(count($alerts) === 0)
    <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center">
        <p class="text-4xl mb-3">✅</p>
        <p class="text-green-700 font-semibold">Todos los componentes están en estado OK</p>
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
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
                            <span class="px-2 py-1 rounded-full text-xs font-bold border {{ $alert['badge'] }}">{{ $alert['label'] }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $alert['rig'] }}</td>
                        <td class="px-4 py-3">{{ $alert['pump'] }}</td>
                        <td class="px-4 py-3">{{ $alert['assembly'] }}</td>
                        <td class="px-4 py-3 font-medium">{{ $alert['component'] }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold">{{ number_format($alert['hours'], 2) }}h</td>
                        <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs">{{ $alert['warning'] }}h</td>
                        <td class="px-4 py-3 text-center text-gray-400 font-mono text-xs">{{ $alert['critical'] }}h</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('pumps.show', $alert['pumpId']) }}" class="text-blue-600 hover:underline text-xs">Ver bomba →</a>
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
