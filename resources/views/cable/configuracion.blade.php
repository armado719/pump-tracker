@extends('layouts.app')
@section('title', 'Configuración Cable TM')

@section('content')
<div class="pt-4 max-w-lg mx-auto space-y-5">

    <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#7a6040;">← Volver al Dashboard</a>

    <div class="rounded-xl p-6 space-y-4" style="background:#1a1000;border:1px solid #2d1f00;">
        <h2 class="text-sm font-bold uppercase tracking-widest" style="color:#E8A045;">Datos Técnicos del Equipo — {{ $rig->name }}</h2>

        <form method="POST" action="{{ route('cable.configuracion.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Altura Torre (ft)</label>
                    <input type="number" name="altura_torre_ft" step="0.1" required
                           value="{{ old('altura_torre_ft', $rig->altura_torre_ft ?? 105) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Diámetro Tambor (in)</label>
                    <input type="number" name="diametro_tambor_in" step="0.1" required
                           value="{{ old('diametro_tambor_in', $rig->diametro_tambor_in ?? 18) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">N° Líneas Activas</label>
                    <input type="number" name="lineas_activas" min="1" max="20" required
                           value="{{ old('lineas_activas', $rig->lineas_activas ?? 8) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">TM Máx. para Corte</label>
                    <input type="number" name="tm_max_corte" step="1" required
                           value="{{ old('tm_max_corte', $rig->tm_max_corte ?? 1200) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Tipo de Cable</label>
                    <select name="tipo_cable" required
                            class="w-full rounded-lg px-3 py-2 text-sm"
                            style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                        @foreach(['EIP','EEIP','IPS'] as $tipo)
                        <option value="{{ $tipo }}" {{ ($rig->tipo_cable ?? 'EIP') === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Peso Bloque Default (lb)</label>
                    <input type="number" name="peso_bloque_default_lb" step="100" required
                           value="{{ old('peso_bloque_default_lb', $rig->peso_bloque_default_lb ?? 25000) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Umbral de alerta (%)</label>
                    <input type="number" name="tm_alerta_pct" min="1" max="100" required
                           value="{{ old('tm_alerta_pct', $rig->tm_alerta_pct ?? 80) }}"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                    <p class="text-xs mt-1" style="color:#7a6040;">Porcentaje del TM máximo a partir del cual se muestra la alerta (recomendado: 80%)</p>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-lg font-bold text-sm transition"
                    style="background:#E8A045;color:#0d0800;"
                    onmouseover="this.style.background='#d4863a'"
                    onmouseout="this.style.background='#E8A045'">
                Guardar configuración
            </button>
        </form>
    </div>
</div>
@endsection
