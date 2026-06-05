@extends('layouts.app')
@section('title', 'Nueva Operación TM')

@section('content')
<div class="pt-4 max-w-3xl mx-auto space-y-4"
     x-data="tmForm()"
     x-init="init()"
     x-cloak>

    <div class="flex items-center justify-between">
        <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#4a8a9e;">← Volver</a>
        <div class="text-xs font-mono px-3 py-1 rounded" style="background:#001a1f;color:#06B6D4;border:1px solid #003344;">
            Cable: {{ $cable->serial }}
        </div>
    </div>

    {{-- PREVIEW TM EN TIEMPO REAL --}}
    <div class="rounded-xl p-4 text-center" style="background:#001a1f;border:1px solid #06B6D4;">
        <div class="text-xs uppercase font-bold mb-1" style="color:#4a8a9e;">Esta operación</div>
        <div class="text-4xl font-black font-mono" style="color:#06B6D4;" x-text="tmOp.toFixed(4) + ' TM'">0.0000 TM</div>
        <div class="text-sm mt-1" style="color:#67a8bc;">
            Acumulado proyectado:
            <span class="font-mono font-bold" style="color:#F0EDE8;" x-text="({{ $tmAcum }} + tmOp).toFixed(2) + ' / {{ $rig->tm_max_corte ?? 1200 }} TM'"></span>
        </div>
        <template x-if="esCod14">
            <div class="mt-2 text-xs font-bold px-3 py-1 rounded inline-block" style="background:#00111a;color:#FF4D2E;border:1px solid #FF4D2E;">
                ⚠ COD 14: Se registrará el corte y se archivará el cable actual
            </div>
        </template>
        <template x-if="esCod13">
            <div class="mt-2 text-xs font-bold px-3 py-1 rounded inline-block" style="background:#001f2a;color:#EAB308;border:1px solid #EAB308;">
                Giro de cable — no suma TM
            </div>
        </template>
    </div>

    <form method="POST" action="{{ route('cable.operaciones.store') }}" class="space-y-4">
        @csrf

        {{-- DATOS BÁSICOS --}}
        <div class="rounded-xl p-5 space-y-4" style="background:#001a1f;border:1px solid #003344;">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#06B6D4;">Datos de la Operación</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ today()->format('Y-m-d') }}"
                           class="w-full rounded-lg px-3 py-2 text-sm"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-medium uppercase" style="color:#4a8a9e;">Tipo de Operación *</label>
                        <div class="flex rounded overflow-hidden text-xs font-bold" style="border:1px solid #003344;">
                            <button type="button"
                                    @click="setLang('es')"
                                    :style="lang==='es' ? 'background:#06B6D4;color:#00111a;' : 'background:#00111a;color:#4a8a9e;'"
                                    class="px-2 py-0.5 transition">ES</button>
                            <button type="button"
                                    @click="setLang('en')"
                                    :style="lang==='en' ? 'background:#06B6D4;color:#00111a;' : 'background:#00111a;color:#4a8a9e;'"
                                    class="px-2 py-0.5 transition">EN</button>
                        </div>
                    </div>
                    <select name="cod" required x-model="cod"
                            @change="recalcular()"
                            class="w-full rounded-lg px-3 py-2 text-sm"
                            style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        <template x-for="op in currentOps" :key="op.cod">
                            <option :value="op.cod" :selected="op.cod === cod" x-text="op.label"></option>
                        </template>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Descripción</label>
                    <input type="text" name="descripcion"
                           class="w-full rounded-lg px-3 py-2 text-sm"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           placeholder="ej. Saco BHA #3 — cambio de broca">
                </div>
            </div>
        </div>

        {{-- PROFUNDIDADES --}}
        <div class="rounded-xl p-5 space-y-4" style="background:#001a1f;border:1px solid #003344;" x-show="!esCod13 && !esCod14">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#06B6D4;">Profundidades</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Prof. Inicial (ft) *</label>
                    <input type="number" name="prof_inicial_ft" step="0.01" min="0"
                           x-model.number="profIni" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('prof_inicial_ft', $ultima?->prof_final_ft ?? 0) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Prof. Final (ft) *</label>
                    <input type="number" name="prof_final_ft" step="0.01" min="0"
                           x-model.number="profFin" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('prof_final_ft', $ultima?->prof_final_ft ?? 0) }}"
                           inputmode="decimal">
                </div>
            </div>
        </div>

        {{-- PESOS --}}
        <div class="rounded-xl p-5 space-y-4" style="background:#001a1f;border:1px solid #003344;" x-show="!esCod13 && !esCod14">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#06B6D4;">Pesos y Densidad</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Densidad lodo (ppg) *</label>
                    <input type="number" name="densidad_lodo_ppg" step="0.1" min="1"
                           x-model.number="densidad" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('densidad_lodo_ppg', $ultima?->densidad_lodo_ppg ?? 11.5) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Peso DP (lb/ft) *</label>
                    <input type="number" name="peso_dp_lb_ft" step="0.01" min="0"
                           x-model.number="pesoDp" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('peso_dp_lb_ft', $ultima?->peso_dp_lb_ft ?? 19.5) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Peso BHA (lb/ft) *</label>
                    <input type="number" name="peso_bha_lb_ft" step="0.01" min="0"
                           x-model.number="pesoBha" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('peso_bha_lb_ft', $ultima?->peso_bha_lb_ft ?? 50) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Long. BHA (ft) *</label>
                    <input type="number" name="long_bha_ft" step="0.01" min="0"
                           x-model.number="longBha" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('long_bha_ft', $ultima?->long_bha_ft ?? 150) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Peso Bloque (lb) *</label>
                    <input type="number" name="peso_bloque_lb" step="1" min="0"
                           x-model.number="pesoBloque" @input="recalcular()" required
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('peso_bloque_lb', $ultima?->peso_bloque_lb ?? $rig->peso_bloque_default_lb ?? 25000) }}"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Long. Parada (ft)</label>
                    <input type="number" name="long_parada_ft" step="0.01" min="0"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                           value="{{ old('long_parada_ft', $ultima?->long_parada_ft ?? 0) }}"
                           inputmode="decimal">
                </div>
            </div>
        </div>

        {{-- COD 14: campos de corte --}}
        <div class="rounded-xl p-5 space-y-4" style="background:#001a1f;border:1px solid #FF4D2E;" x-show="esCod14">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#FF4D2E;">Datos del Corte</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Pies cortados (ft) *</label>
                    <input type="number" name="ft_cortados" step="0.01" min="0"
                           :required="esCod14"
                           class="w-full rounded-lg px-3 py-2 text-sm font-mono text-center"
                           style="background:#00111a;border:1px solid #FF4D2E;color:#F0EDE8;"
                           inputmode="decimal">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Motivo del corte</label>
                    <input type="text" name="notas"
                           class="w-full rounded-lg px-3 py-2 text-sm"
                           style="background:#00111a;border:1px solid #FF4D2E;color:#F0EDE8;"
                           placeholder="ej. TM máximo alcanzado">
                </div>
            </div>
        </div>

        {{-- NOTAS (para ops normales) --}}
        <div class="rounded-xl p-5" style="background:#001a1f;border:1px solid #003344;" x-show="!esCod14">
            <label class="block text-xs font-medium mb-2 uppercase" style="color:#4a8a9e;">Notas adicionales</label>
            <textarea name="notas" rows="2"
                      class="w-full rounded-lg px-3 py-2 text-sm resize-none"
                      style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                      placeholder="Observaciones del perforador..."></textarea>
        </div>

        {{-- BOTÓN GUARDAR --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#4a8a9e;">Cancelar</a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl font-bold text-sm transition shadow-lg"
                    style="background:#06B6D4;color:#00111a;"
                    onmouseover="this.style.background='#d4863a'"
                    onmouseout="this.style.background='#06B6D4'">
                💾 Guardar Operación
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const _opsEs = @json(collect($operaciones)->map(fn($v,$k) => ['cod'=>$k,'label'=>$v])->values());
const _opsEn = @json(collect($operacionesEn)->map(fn($v,$k) => ['cod'=>$k,'label'=>$v])->values());

function tmForm() {
    return {
        cod:        '1',
        lang:       localStorage.getItem('tm_lang') || 'es',
        profIni:    {{ old('prof_inicial_ft', $ultima?->prof_final_ft ?? 0) }},
        profFin:    {{ old('prof_final_ft', $ultima?->prof_final_ft ?? 0) }},
        densidad:   {{ old('densidad_lodo_ppg', $ultima?->densidad_lodo_ppg ?? 11.5) }},
        pesoDp:     {{ old('peso_dp_lb_ft', $ultima?->peso_dp_lb_ft ?? 19.5) }},
        pesoBha:    {{ old('peso_bha_lb_ft', $ultima?->peso_bha_lb_ft ?? 50) }},
        longBha:    {{ old('long_bha_ft', $ultima?->long_bha_ft ?? 150) }},
        pesoBloque: {{ old('peso_bloque_lb', $ultima?->peso_bloque_lb ?? $rig->peso_bloque_default_lb ?? 25000) }},
        lineas:     {{ $rig->lineas_activas ?? 8 }},
        tmOp:       0,

        get esCod14() { return this.cod === '14'; },
        get esCod13() { return this.cod === '13'; },
        get currentOps() { return this.lang === 'en' ? _opsEn : _opsEs; },

        setLang(l) {
            this.lang = l;
            localStorage.setItem('tm_lang', l);
        },

        init() { this.recalcular(); },

        recalcular() {
            if (this.esCod13 || this.esCod14) { this.tmOp = 0; return; }

            const factores = {
                '1': 1.0, '1B': 0.5, '2': 1.0, '3': 1.0, '4': 1.0,
                '5': 1.0, '6': 1.0, '7': 1.0, '8': 1.0, '9': 1.0,
                '10': 1.0, '11': 1.0, '12': 1.0,
            };
            const fop    = factores[this.cod] ?? 1.0;
            const fa     = 1 / Math.max(1, this.lineas);
            const fb     = 1 - (this.densidad / 65.4);
            const longDp = Math.max(0, this.profFin - this.longBha);
            const pe     = fb * (this.pesoDp * longDp + this.pesoBha * this.longBha) + this.pesoBloque;
            const dMedia = (this.profIni + this.profFin) / 2;
            this.tmOp    = Math.max(0, fop * fa * pe * dMedia / 1_000_000);
        }
    };
}
</script>
@endpush
