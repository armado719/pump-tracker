@extends('layouts.app')
@section('title', 'Reportes PDF')

@section('content')
<div class="max-w-2xl mx-auto pt-4 space-y-6">

    {{-- BOMBAS --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-800">Reporte de Bombas</h2>
                <p class="text-xs text-gray-500">Historial mensual FGOP — horas y componentes</p>
            </div>
        </div>
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
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit" name="format" value="pdf"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-sm transition">
                        📄 PDF
                    </button>
                    <button type="submit" form="pump-csv-form"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-bold text-sm transition">
                        📊 Excel / CSV
                    </button>
                </div>
            </div>
        </form>
        {{-- Form oculto para CSV que reutiliza los mismos campos --}}
        <form id="pump-csv-form" method="POST" action="{{ route('export.pump-hours') }}" class="hidden">
            @csrf
            <input type="hidden" name="pump_id" id="csv_pump_id">
            <input type="hidden" name="month" id="csv_month">
        </form>
        <script>
        document.getElementById('pump-csv-form').addEventListener('submit', function() {
            document.getElementById('csv_pump_id').value = document.querySelector('[name=pump_id]').value;
            document.getElementById('csv_month').value = document.querySelector('[name=month]').value;
        });
        </script>
    </div>

    {{-- CABLE TM --}}
    <div class="rounded-xl p-6 shadow-sm" style="background:#001a1f;border:1px solid #003344;">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background:#003d4d;">
                <svg class="w-4 h-4" fill="none" stroke="#06B6D4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold" style="color:#F0EDE8;">Reporte de Cable TM</h2>
                <p class="text-xs" style="color:#4a8a9e;">Historial completo de operaciones y TM acumulado</p>
            </div>
        </div>
        <form method="POST" action="{{ route('reports.cable.generate') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1 uppercase tracking-wide text-xs" style="color:#4a8a9e;">Cable</label>
                    <select name="cable_id" required
                            class="w-full rounded-lg px-3 py-2 text-sm"
                            style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        <option value="">— Seleccionar cable —</option>
                        @foreach($cables->groupBy(fn($c) => $c->rig->name) as $rigName => $rigCables)
                            <optgroup label="{{ $rigName }}">
                                @foreach($rigCables as $cable)
                                <option value="{{ $cable->id }}">
                                    {{ $cable->serial }} — {{ $cable->grado }}
                                    {{ $cable->activo ? '(Activo)' : '(Archivado)' }}
                                </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="submit"
                            class="w-full py-3 rounded-xl font-bold text-sm transition"
                            style="background:#06B6D4;color:#00111a;">
                        📄 PDF
                    </button>
                    <button type="submit" form="cable-csv-form"
                            class="w-full py-3 rounded-xl font-bold text-sm transition"
                            style="background:#166534;color:#4ade80;">
                        📊 Excel / CSV
                    </button>
                </div>
            </div>
        </form>
        <form id="cable-csv-form" method="POST" action="{{ route('export.cable-operaciones') }}" class="hidden">
            @csrf
            <input type="hidden" name="cable_id" id="csv_cable_id">
        </form>
        <script>
        document.getElementById('cable-csv-form').addEventListener('submit', function() {
            document.getElementById('csv_cable_id').value = document.querySelector('[name=cable_id]').value;
        });
            </div>
        </form>
    </div>

</div>
@endsection

