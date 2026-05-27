@extends('layouts.app')
@section('title', 'Editar ' . $rig->name)
@section('header', 'Editar Rig: ' . $rig->name)

@section('content')
<div class="max-w-lg mx-auto pt-4">
    <form method="POST" action="{{ route('rigs.update', $rig) }}">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
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
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('rigs.show', $rig) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection
