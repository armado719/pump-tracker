@extends('layouts.app')
@section('title', 'Centro de Alertas')
@section('header', 'Centro de Alertas')

@section('content')
<div class="pt-4 space-y-5">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex-1 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 min-w-40">
            <span class="text-3xl">🔴</span>
            <div>
                <p class="text-2xl font-bold text-red-700">{{ $criticalCount }}</p>
                <p class="text-sm text-red-600">Componentes Críticos</p>
            </div>
        </div>
        <div class="flex-1 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3 min-w-40">
            <span class="text-3xl">⚠️</span>
            <div>
                <p class="text-2xl font-bold text-amber-700">{{ $warningCount }}</p>
                <p class="text-sm text-amber-600">Componentes en Alerta</p>
            </div>
        </div>
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
