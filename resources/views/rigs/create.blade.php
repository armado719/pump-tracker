@extends('layouts.app')
@section('title', 'Nuevo Rig')
@section('header', 'Crear Nuevo Rig')

@section('content')
<div class="max-w-2xl mx-auto pt-4">
    <form method="POST" action="{{ route('rigs.store') }}" id="rigForm">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="font-semibold text-gray-800 text-lg border-b pb-3">Datos del Rig</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Rig *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="ej. RIG158" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación / Campo</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="ej. Campo Cupiagua">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rig Manager</label>
                <input type="text" name="manager" value="{{ old('manager') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="ej. Ricardo Sanchez / Wiston Ruiz">
            </div>
        </div>

        {{-- Bombas --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mt-4">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="font-semibold text-gray-800 text-lg">Bombas</h3>
                <button type="button" onclick="addPump()"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded-lg transition">
                    + Agregar Bomba
                </button>
            </div>
            <div id="pumpsContainer" class="space-y-4"></div>
        </div>

        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('rigs.index') }}" class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">Cancelar</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Crear Rig</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let pumpIndex = 0;
function addPump() {
    const i = pumpIndex++;
    document.getElementById('pumpsContainer').insertAdjacentHTML('beforeend', `
    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 relative" id="pump-${i}">
        <button type="button" onclick="document.getElementById('pump-${i}').remove()"
                class="absolute top-3 right-3 text-red-400 hover:text-red-600 text-xs">✕ Eliminar</button>
        <p class="font-medium text-sm text-gray-700 mb-3">Bomba #${i+1}</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Número *</label>
                <input type="number" name="pumps[${i}][number]" required min="1"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-blue-500" placeholder="3">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Marca</label>
                <input type="text" name="pumps[${i}][brand]"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="NATIONAL">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Modelo</label>
                <input type="text" name="pumps[${i}][model]"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="10-P-130">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Serial</label>
                <input type="text" name="pumps[${i}][serial]"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="1020">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Diámetro Camisa</label>
                <input type="text" name="pumps[${i}][liner_diameter]"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="5-1/2 IN">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Activo Fijo</label>
                <input type="text" name="pumps[${i}][active_fixed_id]"
                       class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="08-01-009">
            </div>
            <div class="col-span-2 md:col-span-3">
                <label class="block text-xs text-gray-600 mb-1">Horas Acumuladas Base (antes de este registro)</label>
                <input type="number" step="0.01" name="pumps[${i}][base_accumulated_hours]" value="0"
                       class="w-48 border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="0">
            </div>
        </div>
    </div>`);
}
</script>
@endpush
@endsection
