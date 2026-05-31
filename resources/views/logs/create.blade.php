@extends('layouts.app')
@section('title', 'Registro Diario — ' . $pump->rig->name . ' Bomba #' . $pump->number)

@section('content')
<div class="pt-4 space-y-5" x-data="{ activeTab: 'left', hoursWorked: 0 }">

    {{-- ENCABEZADO (solo lectura) --}}
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-sm">
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">RIG</span>
                <span class="font-bold text-gray-800">{{ $pump->rig->name }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">MARCA</span>
                <span class="font-bold">{{ $pump->brand }} {{ $pump->model }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">SERIAL</span>
                <span class="font-mono font-bold">{{ $pump->serial ?? '—' }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">DIÁMETRO CAMISA</span>
                <span class="font-bold">{{ $pump->liner_diameter ?? '—' }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">ACTIVO FIJO</span>
                <span>{{ $pump->active_fixed_id ?? '—' }}</span>
            </div>
            @if($currentPersonnel)
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">RIG MANAGER</span>
                <span>{{ $currentPersonnel->rig_manager ?? '—' }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">SUPERVISOR DÍA</span>
                <span>{{ $currentPersonnel->supervisor_day ?? '—' }}</span>
            </div>
            <div>
                <span class="text-xs text-gray-500 font-medium uppercase block">ENCUELLADOR DÍA</span>
                <span>{{ $currentPersonnel->encuellador_day ?? '—' }}</span>
            </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('pumps.logs.store', $pump) }}" id="logForm">
        @csrf

        {{-- CAMPOS PRINCIPALES DEL DÍA --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <h3 class="font-semibold text-gray-800 mb-4 text-base">📅 Datos del Día</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Día #</label>
                    <input type="number" name="day_number" value="{{ $dayNumber }}"
                           class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-center" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Fecha</label>
                    <input type="date" name="log_date" value="{{ today()->format('Y-m-d') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Horas Trabajo Hoy *</label>
                    <input type="number" step="0.01" min="0" max="24" name="hours_worked"
                           x-model.number="hoursWorked"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono text-center font-bold focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Horas Acumuladas</label>
                    <input type="number" step="0.01" name="accumulated_hours"
                           :value="({{ $previousAccum }} + hoursWorked).toFixed(2)"
                           class="w-full border border-gray-200 bg-blue-50 rounded-lg px-3 py-2 text-sm font-mono font-bold text-center text-blue-700" readonly>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Presión Dampener (PSI)</label>
                    <input type="number" name="dampener_pressure" value="{{ old('dampener_pressure', 900) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono text-center focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2 md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Pozo</label>
                    <select name="well_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">— Sin pozo asignado —</option>
                        @foreach($wells as $well)
                            <option value="{{ $well->id }}">{{ $well->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- TABLA DE CONJUNTOS (tabs: Izquierdo / Medio / Derecho) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-5">
            <div class="border-b border-gray-200">
                <div class="flex">
                    @foreach($pump->assemblies as $assembly)
                    <button type="button"
                            @click="activeTab = '{{ $assembly->position }}'"
                            :class="activeTab === '{{ $assembly->position }}'
                                ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-3 text-sm font-medium transition">
                        {{ $assembly->position_label }}
                    </button>
                    @endforeach
                </div>
            </div>

            @foreach($pump->assemblies as $assembly)
            <div x-show="activeTab === '{{ $assembly->position }}'" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Sección</th>
                            <th class="px-4 py-3 text-left">Componente</th>
                            <th class="px-4 py-3 text-left">Serial</th>
                            <th class="px-4 py-3 text-right w-32">Horas Acum.</th>
                            <th class="px-4 py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-center w-16">Cambio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($assembly->components as $comp)
                        @php
                            $prevHours = $previousComponentHours[$comp->id] ?? $comp->installed_at_hours;
                            $status = \App\Services\ThresholdService::getStatus($comp->type, $prevHours);
                            $badge  = \App\Services\ThresholdService::getStatusBadgeClass($status);
                            $label  = \App\Services\ThresholdService::getStatusLabel($status);
                        @endphp
                        <tr x-data="{ replaced: false }"
                            :class="replaced ? 'bg-orange-50' : '{{ $status === 'critical' ? 'bg-red-50' : ($status === 'warning' ? 'bg-amber-50' : '') }}'">
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $comp->section_label }}</td>
                            <td class="px-4 py-2 font-medium text-sm">{{ $comp->type_label }}</td>
                            <td class="px-4 py-2">
                                <span x-show="!replaced" class="font-mono text-xs text-gray-500">{{ $comp->serial ?? '—' }}</span>
                                {{-- Inputs solo se envían cuando replaced=true gracias a :disabled --}}
                                <input x-show="replaced"
                                       :disabled="!replaced"
                                       type="text"
                                       name="replacements[{{ $comp->id }}][new_serial]"
                                       class="w-28 border border-orange-300 rounded px-2 py-1 text-xs font-mono focus:ring-1 focus:ring-orange-400"
                                       placeholder="Nuevo serial">
                                <input :disabled="!replaced"
                                       type="hidden"
                                       name="replacements[{{ $comp->id }}][component_id]"
                                       value="{{ $comp->id }}">
                                <input :disabled="!replaced"
                                       type="hidden"
                                       name="replacements[{{ $comp->id }}][hours_before]"
                                       value="{{ $prevHours }}">
                                <input :disabled="!replaced"
                                       type="hidden"
                                       name="replacements[{{ $comp->id }}][notes]"
                                       :value="'Cambio registrado en día {{ $dayNumber }}'">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span x-show="!replaced"
                                      x-text="({{ $prevHours }} + hoursWorked).toFixed(2)"
                                      class="font-mono font-bold text-sm"></span>
                                <span x-show="replaced"
                                      x-text="hoursWorked.toFixed(2)"
                                      class="font-mono font-bold text-sm text-orange-600"></span>
                                <input type="hidden"
                                       name="component_hours[{{ $comp->id }}]"
                                       :value="replaced ? hoursWorked.toFixed(2) : ({{ $prevHours }} + hoursWorked).toFixed(2)">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="checkbox" x-model="replaced"
                                       class="w-4 h-4 rounded border-gray-300 text-orange-500 cursor-pointer"
                                       title="Marcar si se reemplazó este componente hoy">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-xs text-gray-400 px-4 py-2 border-t">
                    ☑ Marca "Cambio" si reemplazaste el componente hoy — las horas se resetean al valor del día.
                </p>
            </div>
            @endforeach
        </div>

        {{-- COMENTARIOS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
            <textarea name="comments" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 resize-none"
                      placeholder="ej. CAMBIO ASIENTO DESCARGA #2">{{ old('comments') }}</textarea>
        </div>

        {{-- BOTONES --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('pumps.show', $pump) }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold text-sm transition shadow-md">
                💾 Guardar Registro del Día {{ $dayNumber }}
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('logForm').addEventListener('submit', function(e) {
    if (navigator.onLine) return; // online: enviar normalmente
    e.preventDefault();

    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => { data[key] = value; });

    const pending = JSON.parse(localStorage.getItem('pending_logs') || '[]');
    pending.push({
        url:       '{{ route('pumps.logs.store', $pump) }}',
        pump_name: '{{ $pump->rig->name }} — Bomba #{{ $pump->number }}',
        day:       {{ $dayNumber }},
        data:      data,
        saved_at:  new Date().toLocaleString('es-CO'),
    });
    localStorage.setItem('pending_logs', JSON.stringify(pending));

    alert('📵 Sin conexión.\nEl registro del Día {{ $dayNumber }} fue guardado localmente.\nSe sincronizará automáticamente cuando haya señal.');
    window.location.href = '{{ route('pumps.show', $pump) }}';
});
</script>
@endpush
