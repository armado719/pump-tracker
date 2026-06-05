@extends('layouts.app')
@section('title', 'Gestión de Pozos')

@section('content')
<div class="pt-4 space-y-5">

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $wells->count() }} pozo(s) registrado(s)</p>
        <a href="{{ route('wells.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
            + Nuevo Pozo
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Rig</th>
                    <th class="px-4 py-3 text-left">Nombre del Pozo</th>
                    <th class="px-4 py-3 text-center">Registros Diarios</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($wells as $well)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('rigs.show', $well->rig) }}"
                           class="text-blue-600 hover:underline font-medium">
                            {{ $well->rig?->name ?? '—' }}
                        </a>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $well->name }}</td>
                    <td class="px-4 py-3 text-center text-gray-500 font-mono">{{ $well->daily_logs_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('wells.edit', $well) }}"
                               class="text-yellow-600 hover:underline text-xs font-medium">✏️ Editar</a>
                            <form method="POST" action="{{ route('wells.destroy', $well) }}"
                                  onsubmit="return confirm('¿Eliminar el pozo {{ addslashes($well->name) }}? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs font-medium">
                                    🗑 Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                        No hay pozos registrados.
                        <a href="{{ route('wells.create') }}" class="text-blue-600 hover:underline ml-1">Crear el primero →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Quick-add form --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-700 text-sm mb-3">Agregar pozo rápido</h3>
        <form method="POST" action="{{ route('wells.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rig *</label>
                <select name="rig_id" required
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Selecciona —</option>
                    @foreach($rigs as $rig)
                        <option value="{{ $rig->id }}">{{ $rig->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre del Pozo *</label>
                <input type="text" name="name" required placeholder="ej. Pozo A-15"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-48">
            </div>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Agregar
            </button>
        </form>
    </div>

</div>
@endsection
