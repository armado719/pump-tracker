@extends('layouts.app')
@section('title', 'Bombas')
@section('header', 'Bombas de Lodo')

@section('content')
<div class="pt-4">
    <div class="flex justify-between items-center mb-5">
        <p class="text-gray-500 text-sm">{{ $pumps->count() }} bomba(s)</p>
        <a href="{{ route('pumps.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium">+ Nueva Bomba</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Rig</th>
                    <th class="px-4 py-3 text-center">Bomba #</th>
                    <th class="px-4 py-3 text-left">Marca / Modelo</th>
                    <th class="px-4 py-3 text-left">Serial</th>
                    <th class="px-4 py-3 text-right">Horas Base</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pumps as $pump)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $pump->rig->name }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-600">{{ $pump->number }}</td>
                    <td class="px-4 py-3">{{ $pump->brand }} {{ $pump->model }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $pump->serial ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-mono">{{ number_format($pump->base_accumulated_hours, 2) }}h</td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('pumps.show', $pump) }}" class="text-blue-600 hover:underline text-xs">Ver</a>
                        <a href="{{ route('pumps.edit', $pump) }}" class="text-yellow-600 hover:underline text-xs">Editar</a>
                        <a href="{{ route('pumps.logs.create', $pump) }}" class="text-green-600 hover:underline text-xs">📝 Reg.</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
