@extends('layouts.app')
@section('title', 'Editar Registro Día ' . $log->day_number . ' — ' . $pump->rig->name . ' Bomba #' . $pump->number)

@section('content')
<div class="pt-4 space-y-5" x-data="{ hoursWorked: {{ $log->hours_worked }} }">

    {{-- ENCABEZADO --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-sm">
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">RIG</span>
                <span class="font-bold text-gray-800">{{ $pump->rig->name }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">BOMBA</span>
                <span class="font-bold">{{ $pump->brand }} {{ $pump->model }} #{{ $pump->number }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">DÍA</span>
                <span class="font-bold font-mono text-blue-700">#{{ $log->day_number }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">REGISTRADO</span>
                <span class="font-mono text-xs text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('pumps.logs.update', [$pump, $log]) }}">
        @csrf
        @method('PUT')

        {{-- CAMPOS EDITABLES --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 text-base">✏️ Corregir Datos del Día</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Fecha</label>
                    <input type="date" name="log_date" value="{{ $log->log_date->format('Y-m-d') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Horas Trabajo *</label>
                    <input type="number" step="0.01" min="0" max="24" name="hours_worked"
                           x-model.number="hoursWorked"
                           value="{{ $log->hours_worked }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono text-center font-bold focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Horas Acumuladas</label>
                    <input type="number" step="0.01"
                           :value="({{ $previousAccum }} + hoursWorked).toFixed(2)"
                           class="w-full border border-gray-200 bg-blue-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-center text-blue-700" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Presión Dampener (PSI)</label>
                    <input type="number" name="dampener_pressure" value="{{ old('dampener_pressure', $log->dampener_pressure) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono text-center focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2 md:col-span-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Pozo</label>
                    <select name="well_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">— Sin pozo asignado —</option>
                        @foreach($wells as $well)
                            <option value="{{ $well->id }}" {{ $log->well_id == $well->id ? 'selected' : '' }}>
                                {{ $well->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- COMENTARIOS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
            <textarea name="comments" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="ej. CAMBIO ASIENTO DESCARGA #2">{{ old('comments', $log->comments) }}</textarea>
        </div>

        {{-- AVISO sobre componentes --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-5 text-sm text-amber-800">
            <strong>ℹ️ Nota:</strong> Al corregir las horas trabajadas, las horas acumuladas de todos los componentes de este día se ajustan automáticamente con la diferencia.
            Los reemplazos de componentes no se modifican aquí.
        </div>

        {{-- BOTONES --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('pumps.logs.show', [$pump, $log]) }}" class="text-sm text-gray-500 hover:text-gray-700">← Cancelar</a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold text-sm transition shadow-md">
                💾 Guardar Corrección — Día {{ $log->day_number }}
            </button>
        </div>
    </form>
</div>
@endsection
