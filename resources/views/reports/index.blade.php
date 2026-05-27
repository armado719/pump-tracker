@extends('layouts.app')
@section('title', 'Reportes PDF')
@section('header', 'Generar Reporte PDF')

@section('content')
<div class="max-w-lg mx-auto pt-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <p class="text-sm text-gray-600 mb-5">
            Genera un PDF en formato FGOP-XXXX con el historial completo mensual de una bomba.
        </p>
        <form method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bomba</label>
                    <select name="pump_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">— Seleccionar —</option>
                        @foreach($pumps as $pump)
                        <option value="{{ $pump->id }}">{{ $pump->rig->name }} — Bomba #{{ $pump->number }} ({{ $pump->brand }} {{ $pump->model }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                    <input type="month" name="month" value="{{ now()->format('Y-m') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-bold text-sm transition">
                    📄 Descargar PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
