@extends('layouts.app')
@section('title', 'Reemplazar Componente')

@section('content')
<div class="max-w-xl mx-auto pt-4 space-y-4">

    {{-- Info del componente actual --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-500 uppercase mb-3">Componente a reemplazar</h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-400 text-xs block">Bomba</span>
                <span class="font-medium">{{ $pump->rig->name }} — Bomba #{{ $pump->number }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Conjunto</span>
                <span class="font-medium">{{ $component->assembly->position_label }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Componente</span>
                <span class="font-bold text-blue-700">{{ $component->type_label }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Serial actual</span>
                <span class="font-mono">{{ $component->serial ?? '—' }}</span>
            </div>
            <div>
                <span class="text-gray-400 text-xs block">Horas acumuladas</span>
                <span class="font-mono font-bold text-red-600">{{ number_format($currentHours, 2) }}h</span>
            </div>
            @if($lastReplacement)
            <div>
                <span class="text-gray-400 text-xs block">Último cambio</span>
                <span class="text-xs">{{ $lastReplacement->created_at->format('d/m/Y') }}
                    ({{ number_format($lastReplacement->hours_before, 2) }}h)</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Formulario de reemplazo --}}
    <form method="POST" action="{{ route('components.replace', [$pump, $component]) }}">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 uppercase">Datos del componente nuevo</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Serial del componente nuevo
                    <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <input type="text" name="new_serial" value="{{ old('new_serial') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: 08-13-099">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Motivo / Observaciones
                </label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                          placeholder="Ej: Desgaste prematuro a las {{ number_format($currentHours, 0) }}h. Se reemplaza por componente nuevo.">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-800">
                ⚠ Las horas del componente <strong>se reiniciarán a 0</strong> al guardar.
                El contador nuevo comenzará en el próximo registro diario.
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <a href="{{ route('pumps.show', $pump) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                    Confirmar reemplazo
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
