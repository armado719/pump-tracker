@extends('layouts.app')
@section('title', 'Personal — Bomba #' . $pump->number)
@section('header', 'Personal de Bomba #' . $pump->number)

@section('content')
<div class="max-w-lg mx-auto pt-4">
    <form method="POST" action="{{ route('pumps.personnel.store', $pump) }}">
        @csrf
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inicio del Período *</label>
                <input type="date" name="period_start" value="{{ old('period_start', today()->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            @foreach([
                ['rig_manager','Rig Manager'],
                ['supervisor_day','Supervisor Día'],
                ['supervisor_night','Supervisor Noche'],
                ['encuellador_day','Encuellador Día'],
                ['encuellador_night','Encuellador Noche'],
            ] as [$field, $label])
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                <input type="text" name="{{ $field }}"
                       value="{{ old($field, $current?->{$field}) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ $label }}">
            </div>
            @endforeach
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('pumps.show', $pump) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Guardar Personal</button>
            </div>
        </div>
    </form>
</div>
@endsection
