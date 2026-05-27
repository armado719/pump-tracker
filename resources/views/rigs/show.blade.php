@extends('layouts.app')
@section('title', $rig->name)
@section('header', $rig->name)
@section('breadcrumb', 'Rigs / ' . $rig->name)

@section('content')
<div class="pt-4 space-y-5">
    {{-- Info rig --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500 block text-xs uppercase font-medium">Nombre</span>{{ $rig->name }}</div>
            <div><span class="text-gray-500 block text-xs uppercase font-medium">Ubicación</span>{{ $rig->location ?? '—' }}</div>
            <div class="col-span-2"><span class="text-gray-500 block text-xs uppercase font-medium">Manager</span>{{ $rig->manager ?? '—' }}</div>
        </div>
    </div>

    {{-- Bombas del rig --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Bombas ({{ $rig->pumps->count() }})</h3>
            <a href="{{ route('pumps.create') }}?rig_id={{ $rig->id }}"
               class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition">+ Bomba</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Bomba #</th>
                        <th class="px-4 py-3 text-left">Marca / Modelo</th>
                        <th class="px-4 py-3 text-left">Serial</th>
                        <th class="px-4 py-3 text-left">Camisa</th>
                        <th class="px-4 py-3 text-right">Horas Base</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rig->pumps as $pump)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">Bomba #{{ $pump->number }}</td>
                        <td class="px-4 py-3">{{ $pump->brand }} {{ $pump->model }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $pump->serial ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs">{{ $pump->liner_diameter ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($pump->base_accumulated_hours, 2) }}h</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('pumps.show', $pump) }}" class="text-blue-600 hover:underline text-xs mr-2">Ver</a>
                            <a href="{{ route('pumps.logs.create', $pump) }}" class="text-green-600 hover:underline text-xs">📝 Registro</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
