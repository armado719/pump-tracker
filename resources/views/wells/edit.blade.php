@extends('layouts.app')
@section('title', 'Editar Pozo — ' . $well->name)

@section('content')
<div class="pt-4 max-w-md mx-auto space-y-5">

    <a href="{{ route('wells.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-5 text-base">Editar pozo: {{ $well->name }}</h2>

        <form method="POST" action="{{ route('wells.update', $well) }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rig *</label>
                <select name="rig_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($rigs as $rig)
                        <option value="{{ $rig->id }}" {{ old('rig_id', $well->rig_id) == $rig->id ? 'selected' : '' }}>
                            {{ $rig->name }}
                        </option>
                    @endforeach
                </select>
                @error('rig_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre del Pozo *</label>
                <input type="text" name="name" value="{{ old('name', $well->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2 flex justify-between items-center">
                <a href="{{ route('wells.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium text-sm transition">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
