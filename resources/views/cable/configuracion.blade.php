@extends('layouts.app')
@section('title', 'Configuración Cable TM')

@section('content')
<div class="pt-4 max-w-lg mx-auto space-y-5">

    <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#4a8a9e;">← Volver al Dashboard</a>

    <div class="rounded-xl p-6 space-y-4" style="background:#001a1f;border:1px solid #003344;">
        <h2 class="text-sm font-bold uppercase tracking-widest" style="color:#06B6D4;">Datos Técnicos del Equipo — {{ $rig->name }}</h2>

        <form method="POST" action="{{ route('cable.configuracion.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Altura Torre (ft)</label>
                    <input type="number" name="altura_torre_ft" step="0.1" required
                           value="{{ old('altura_torre_ft', $rig->altura_torre_ft ?? 105) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Diámetro Tambor</label>
                    <div class="flex gap-1">
                        <input type="number" id="diametro_display" step="0.001" required
                               value="{{ old('diametro_tambor_in', $rig->diametro_tambor_in ?? 18) }}"
                               class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                               style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        <select id="diametro_unit" class="rounded-lg px-2 py-2 text-sm font-bold"
                                style="background:#00111a;border:1px solid #003344;color:#06B6D4;min-width:52px;">
                            <option value="in">in</option>
                            <option value="cm">cm</option>
                            <option value="ft">ft</option>
                        </select>
                    </div>
                    <input type="hidden" name="diametro_tambor_in" id="diametro_tambor_in"
                           value="{{ old('diametro_tambor_in', $rig->diametro_tambor_in ?? 18) }}">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">N° Líneas Activas</label>
                    <input type="number" name="lineas_activas" min="1" max="20" required
                           value="{{ old('lineas_activas', $rig->lineas_activas ?? 8) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">TM Máx. para Corte</label>
                    <input type="number" name="tm_max_corte" step="1" required
                           value="{{ old('tm_max_corte', $rig->tm_max_corte ?? 1200) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Tipo de Cable</label>
                    <select name="tipo_cable" required
                            class="w-full rounded-lg px-3 py-2 text-sm"
                            style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        @foreach(['EIP','EEIP','IPS'] as $tipo)
                        <option value="{{ $tipo }}" {{ ($rig->tipo_cable ?? 'EIP') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Peso Bloque Default (lb)</label>
                    <input type="number" name="peso_bloque_default_lb" step="100" required
                           value="{{ old('peso_bloque_default_lb', $rig->peso_bloque_default_lb ?? 25000) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Umbral de alerta (%)</label>
                    <input type="number" name="tm_alerta_pct" min="1" max="100" required
                           value="{{ old('tm_alerta_pct', $rig->tm_alerta_pct ?? 80) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                    <p class="text-xs mt-1" style="color:#4a8a9e;">Porcentaje del TM máximo a partir del cual se muestra la alerta (recomendado: 80%)</p>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-lg font-bold text-sm transition"
                    style="background:#06B6D4;color:#00111a;"
                    onmouseover="this.style.background='#d4863a'"
                    onmouseout="this.style.background='#06B6D4'">
                Guardar configuración
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const display = document.getElementById('diametro_display');
    const unitSel = document.getElementById('diametro_unit');
    const hidden  = document.getElementById('diametro_tambor_in');

    function r(v) { return Math.round(v * 1000) / 1000; }

    function toInches(val, unit) {
        if (unit === 'cm') return val / 2.54;
        if (unit === 'ft') return val * 12;
        return val;
    }

    function fromInches(inches, unit) {
        if (unit === 'cm') return r(inches * 2.54);
        if (unit === 'ft') return r(inches / 12);
        return r(inches);
    }

    unitSel.addEventListener('change', function () {
        const inches = parseFloat(hidden.value) || 0;
        display.value = fromInches(inches, this.value);
    });

    display.addEventListener('input', function () {
        const val = parseFloat(this.value) || 0;
        hidden.value = r(toInches(val, unitSel.value));
    });
})();
</script>
@endpush
