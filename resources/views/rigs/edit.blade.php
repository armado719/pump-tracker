@extends('layouts.app')
@section('title', 'Editar ' . $rig->name)
@section('header', 'Editar Rig: ' . $rig->name)

@section('content')
<div class="max-w-xl mx-auto pt-4">
    <form method="POST" action="{{ route('rigs.update', $rig) }}">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">

            {{-- Datos generales --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $rig->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                <input type="text" name="location" value="{{ old('location', $rig->location) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rig Manager</label>
                <input type="text" name="manager" value="{{ old('manager', $rig->manager) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- ── Configuración Cable TM ─────────────────────────────────── --}}
            <div class="border-t border-gray-200 pt-4 mt-2">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Configuración Cable TM</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Altura Torre (ft)</label>
                        <input type="number" name="altura_torre_ft" step="0.1"
                               value="{{ old('altura_torre_ft', $rig->altura_torre_ft ?? 105) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Diámetro Tambor (in)</label>
                        <input type="number" name="diametro_tambor_in" step="0.1"
                               value="{{ old('diametro_tambor_in', $rig->diametro_tambor_in ?? 18) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Líneas Activas</label>
                        <input type="number" name="lineas_activas" min="1" max="20"
                               value="{{ old('lineas_activas', $rig->lineas_activas ?? 8) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Tipo de Cable</label>
                        <select name="tipo_cable"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach(['EIP','EEIP','IPS'] as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo_cable', $rig->tipo_cable ?? 'EIP') === $tipo ? 'selected' : '' }}>
                                {{ $tipo }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">TM Máx. para Corte</label>
                        <input type="number" name="tm_max_corte" step="1"
                               value="{{ old('tm_max_corte', $rig->tm_max_corte ?? 1200) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">% Umbral Alerta TM</label>
                        <input type="number" name="tm_alerta_pct" min="1" max="100"
                               value="{{ old('tm_alerta_pct', $rig->tm_alerta_pct ?? 80) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Recomendado: 80%</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Peso Bloque Default (lb)</label>
                        <input type="number" name="peso_bloque_default_lb" step="100"
                               value="{{ old('peso_bloque_default_lb', $rig->peso_bloque_default_lb ?? 25000) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('rigs.show', $rig) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection
