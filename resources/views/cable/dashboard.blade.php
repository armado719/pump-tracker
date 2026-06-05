@extends('layouts.app')
@section('title', 'Cable TM — ' . $rig->name)

@section('content')
<div class="pt-4 space-y-5">

    {{-- SELECTOR DE EQUIPO (solo admin / usuarios sin rig fijo) --}}
    @if($rigsDisponibles->count() > 1)
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl"
         style="background:#001a1f;border:1px solid #003344;">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="#4a8a9e" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <span class="text-xs font-bold uppercase tracking-widest" style="color:#4a8a9e;">Equipo</span>
        <form method="POST" action="{{ route('cable.seleccionar-rig') }}" class="flex items-center gap-2 flex-1">
            @csrf
            <select name="rig_id" onchange="this.form.submit()"
                    class="flex-1 rounded-lg px-3 py-1.5 text-sm font-medium"
                    style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                @foreach($rigsDisponibles as $r)
                    <option value="{{ $r->id }}" {{ $r->id === $rig->id ? 'selected' : '' }}>
                        {{ $r->name }}{{ $r->location ? ' — ' . $r->location : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    {{-- ALERTA TM CRÍTICA --}}
    @if($cable && $tmPct >= 95)
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-bold shadow"
         style="background:#00111a;border:1px solid #FF4D2E;color:#FF4D2E;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        CRÍTICO — Cable al {{ $tmPct }}% de vida útil. Programar corte inmediato.
    </div>
    @elseif($cable && $tmPct >= $alerta)
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-bold shadow"
         style="background:#001f2a;border:1px solid #EAB308;color:#EAB308;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        ALERTA — Cable al {{ $tmPct }}% de vida útil. Preparar plan de corte.
    </div>
    @endif

    {{-- PANEL PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- GAUGE TM --}}
        <div class="lg:col-span-1 rounded-2xl p-6 flex flex-col items-center"
             style="background:#001a1f;border:1px solid #003344;">
            <h3 class="text-xs font-bold tracking-widest mb-4 uppercase" style="color:#4a8a9e;">Tonelada-Milla</h3>

            @if($cable)
            <svg viewBox="0 0 200 120" class="w-56">
                {{-- Track --}}
                <path d="M 20 100 A 80 80 0 0 1 180 100"
                      fill="none" stroke="#003344" stroke-width="18" stroke-linecap="round"/>
                {{-- Fill --}}
                @if($tmPct > 0)
                <path d="{{ $gauge['path'] }}"
                      fill="none" stroke="{{ $gauge['color'] }}" stroke-width="18" stroke-linecap="round"/>
                @endif
                {{-- Valor % --}}
                <text x="100" y="82" text-anchor="middle" font-size="26" font-weight="900"
                      fill="{{ $gauge['color'] }}" font-family="monospace">{{ $tmPct }}%</text>
                <text x="100" y="98" text-anchor="middle" font-size="9" fill="#4a8a9e" font-family="monospace">
                    {{ number_format($tmAcum, 2) }} / {{ number_format($tmMax, 0) }} TM
                </text>
                {{-- Marcas 0% y 100% --}}
                <text x="16" y="115" text-anchor="middle" font-size="8" fill="#003344">0</text>
                <text x="184" y="115" text-anchor="middle" font-size="8" fill="#003344">MAX</text>
            </svg>

            <div class="mt-4 text-center">
                <div class="text-3xl font-black font-mono" style="color:#06B6D4;">
                    {{ number_format($tmAcum, 2) }} <span class="text-base font-normal" style="color:#4a8a9e;">TM</span>
                </div>
                <div class="text-xs mt-1" style="color:#4a8a9e;">
                    Restante: <span class="font-mono font-bold" style="color:#67a8bc;">{{ number_format(max(0, $tmMax - $tmAcum), 2) }} TM</span>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <div class="text-4xl mb-3">🔗</div>
                <p class="text-sm" style="color:#4a8a9e;">Sin cable activo</p>
            </div>
            @endif

            {{-- Botones acción --}}
            <div class="mt-5 w-full space-y-2">
                @if($cable)
                <a href="{{ route('cable.operaciones.create') }}"
                   class="block text-center text-sm font-bold py-2 rounded-lg transition"
                   style="background:#06B6D4;color:#00111a;">
                    + Registrar operación
                </a>
                @endif
                <a href="{{ route('cable.configuracion') }}"
                   class="block text-center text-sm py-2 rounded-lg transition"
                   style="border:1px solid #003344;color:#4a8a9e;"
                   onmouseover="this.style.borderColor='#06B6D4'"
                   onmouseout="this.style.borderColor='#003344'">
                    ⚙ Configuración
                </a>
            </div>
        </div>

        {{-- CABLE ACTIVO + ÚLTIMAS OPERACIONES --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info cable activo --}}
            @if($cable)
            <div class="rounded-2xl p-5" style="background:#001a1f;border:1px solid #003344;">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#4a8a9e;">Cable Activo</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#0d2a00;color:#4ade80;border:1px solid #166534;">● Activo</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Serial</span>
                        <span class="font-mono font-bold" style="color:#06B6D4;">{{ $cable->serial }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Fabricante</span>
                        <span style="color:#F0EDE8;">{{ $cable->fabricante ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Grado / Diámetro</span>
                        <span style="color:#F0EDE8;">{{ $cable->grado }} — {{ $cable->diametro_in ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Instalación</span>
                        <span style="color:#F0EDE8;">{{ $cable->fecha_instalacion->format('d/m/Y') }}</span>
                    </div>
                    @if($cable->resistencia_lb)
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Resistencia</span>
                        <span class="font-mono" style="color:#F0EDE8;">{{ number_format($cable->resistencia_lb, 0) }} lb</span>
                    </div>
                    @endif
                    @if($cable->longitud_inicial_ft)
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#4a8a9e;">Long. inicial</span>
                        <span class="font-mono" style="color:#F0EDE8;">{{ number_format($cable->longitud_inicial_ft, 0) }} ft</span>
                    </div>
                    @endif
                </div>
            </div>
            @else
            {{-- Registrar nuevo cable --}}
            <div class="rounded-2xl p-5" style="background:#001a1f;border:1px solid #06B6D4;">
                <h3 class="text-sm font-bold mb-4" style="color:#06B6D4;">Registrar Cable Activo</h3>
                <form method="POST" action="{{ route('cable.registrar') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Serial *</label>
                            <input type="text" name="serial" required
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                                   placeholder="ej. 4332421-24">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Fabricante</label>
                            <input type="text" name="fabricante"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                                   placeholder="ej. EMCOCABLES">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Referencia</label>
                            <input type="text" name="referencia"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                                   placeholder="ej. 6X19S BIPEX AA RL">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Grado</label>
                            <select name="grado" class="w-full rounded-lg px-3 py-2 text-sm"
                                    style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                                <option value="EIP">EIP</option>
                                <option value="EEIP">EEIP</option>
                                <option value="IPS">IPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Diámetro</label>
                            <input type="text" name="diametro_in" value='1 1/8"'
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                                   placeholder='ej. 1 1/8"'>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Resistencia (lb)</label>
                            <input type="number" name="resistencia_lb" step="0.01"
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;"
                                   placeholder="ej. 89500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Fecha instalación *</label>
                            <input type="date" name="fecha_instalacion" required value="{{ today()->format('Y-m-d') }}"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#4a8a9e;">Long. inicial (ft)</label>
                            <input type="number" name="longitud_inicial_ft" step="0.01"
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg font-bold text-sm transition"
                            style="background:#06B6D4;color:#00111a;">
                        Registrar cable y activar
                    </button>
                </form>
            </div>
            @endif

            {{-- Últimas operaciones --}}
            @if($cable && $ultimas->count())
            <div class="rounded-2xl overflow-hidden" style="background:#001a1f;border:1px solid #003344;">
                <div class="px-5 py-3 flex justify-between items-center" style="border-bottom:1px solid #003344;">
                    <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#4a8a9e;">Últimas Operaciones</h3>
                    <a href="{{ route('cable.operaciones.index') }}" class="text-xs hover:underline" style="color:#06B6D4;">Ver todas →</a>
                </div>
                <table class="w-full text-sm">
                    <thead style="background:#00111a;">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Operación</th>
                            <th class="px-4 py-2 text-right text-xs uppercase" style="color:#4a8a9e;">TM Op.</th>
                            <th class="px-4 py-2 text-right text-xs uppercase" style="color:#4a8a9e;">TM Acum.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimas as $op)
                        <tr style="border-top:1px solid #003344;">
                            <td class="px-4 py-2 font-mono text-xs" style="color:#67a8bc;">{{ $op->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-xs" style="color:#F0EDE8;">{{ $op->tipo_operacion }}</td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-xs" style="color:#06B6D4;">{{ number_format($op->tm_operacion, 4) }}</td>
                            <td class="px-4 py-2 text-right font-mono text-xs" style="color:#67a8bc;">{{ number_format($op->tm_acumulado, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- GRÁFICA TM ACUMULADO --}}
    @if($cable && count($chartLabels) > 1)
    <div class="rounded-2xl p-5" style="background:#001a1f;border:1px solid #003344;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#4a8a9e;">Tendencia TM Acumulado</h3>
            <span class="text-xs font-mono" style="color:#06B6D4;">{{ count($chartLabels) }} operaciones</span>
        </div>
        <canvas id="tmChart" height="70"></canvas>
    </div>
    @endif

    {{-- HISTORIAL CABLES --}}
    @if($cables->count() > 1 || ($cables->count() === 1 && !$cable))
    <div class="rounded-2xl overflow-hidden" style="background:#001a1f;border:1px solid #003344;">
        <div class="px-5 py-3 flex justify-between items-center" style="border-bottom:1px solid #003344;">
            <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#4a8a9e;">Cables del Rig</h3>
            <a href="{{ route('cable.historial') }}" class="text-xs hover:underline" style="color:#06B6D4;">Ver historial →</a>
        </div>
        <table class="w-full text-sm">
            <thead style="background:#00111a;">
                <tr>
                    <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Serial</th>
                    <th class="px-4 py-2 text-left text-xs uppercase" style="color:#4a8a9e;">Instalación</th>
                    <th class="px-4 py-2 text-right text-xs uppercase" style="color:#4a8a9e;">TM Total</th>
                    <th class="px-4 py-2 text-center text-xs uppercase" style="color:#4a8a9e;">Ops</th>
                    <th class="px-4 py-2 text-center text-xs uppercase" style="color:#4a8a9e;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cables as $c)
                <tr style="border-top:1px solid #003344;">
                    <td class="px-4 py-2 font-mono font-bold text-xs" style="color:#06B6D4;">{{ $c->serial }}</td>
                    <td class="px-4 py-2 text-xs" style="color:#67a8bc;">{{ $c->fecha_instalacion->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-right font-mono text-xs" style="color:#F0EDE8;">{{ number_format($c->tmAcumulado(), 2) }}</td>
                    <td class="px-4 py-2 text-center text-xs" style="color:#67a8bc;">{{ $c->operaciones_count }}</td>
                    <td class="px-4 py-2 text-center">
                        @if($c->activo)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#0d2a00;color:#4ade80;">Activo</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#001a1f;color:#4a8a9e;">Archivado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

@push('scripts')
@if($cable && count($chartLabels) > 1)
<script>
new Chart(document.getElementById('tmChart'), {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'TM Acumulado',
                data: @json($chartTmAcum),
                borderColor: '#06B6D4',
                backgroundColor: 'rgba(6,182,212,0.08)',
                borderWidth: 2,
                pointRadius: {{ count($chartLabels) > 40 ? 0 : 3 }},
                fill: true,
                tension: 0.3,
            },
            {
                label: 'Límite TM ({{ $tmMax }})',
                data: Array({{ count($chartLabels) }}).fill({{ $tmMax }}),
                borderColor: '#FF4D2E',
                borderWidth: 1,
                borderDash: [6, 4],
                pointRadius: 0,
                fill: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#4a8a9e', font: { size: 11 } } }
        },
        scales: {
            x: {
                ticks: { color: '#4a8a9e', maxTicksLimit: 14, font: { size: 10 } },
                grid: { color: '#003344' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#4a8a9e', font: { size: 10 } },
                grid: { color: '#003344' },
                title: { display: true, text: 'TM', color: '#4a8a9e', font: { size: 10 } }
            }
        }
    }
});
</script>
@endif
@endpush
