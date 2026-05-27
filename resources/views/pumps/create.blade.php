@extends('layouts.app')
@section('title', 'Nueva Bomba')
@section('header', 'Agregar Bomba')

@section('content')
<div class="max-w-2xl mx-auto pt-4">
    <form method="POST" action="{{ route('pumps.store') }}">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rig *</label>
                <select name="rig_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Seleccionar Rig —</option>
                    @foreach($rigs as $rig)
                        <option value="{{ $rig->id }}" {{ request('rig_id') == $rig->id ? 'selected' : '' }}>{{ $rig->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                    <input type="number" name="number" value="{{ old('number') }}" required min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <input type="text" name="brand" value="{{ old('brand') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="NATIONAL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                    <input type="text" name="model" value="{{ old('model') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="10-P-130">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Serial</label>
                    <input type="text" name="serial" value="{{ old('serial') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="1020">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diámetro Camisa</label>
                    <input type="text" name="liner_diameter" value="{{ old('liner_diameter') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="5-1/2 IN">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activo Fijo</label>
                    <input type="text" name="active_fixed_id" value="{{ old('active_fixed_id') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="08-01-009">
                </div>
                <div class="col-span-2 md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horas Acumuladas Base</label>
                    <input type="number" step="0.01" name="base_accumulated_hours" value="{{ old('base_accumulated_hours', 0) }}"
                           class="w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Horas registradas en el Excel anterior antes de iniciar este sistema</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('pumps.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    Crear Bomba (+ 24 componentes automáticos)
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
