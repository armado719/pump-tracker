@extends('layouts.app')
@section('title', 'Cable TM — Estado')

@section('content')
<div class="pt-4 space-y-5">

    {{-- ALERTA TM CRÍTICA --}}
    @if($cable && $tmPct >= 95)
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-bold shadow"
         style="background:#3d0000;border:1px solid #FF4D2E;color:#FF4D2E;">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        CRÍTICO — Cable al {{ $tmPct }}% de vida útil. Programar corte inmediato.
    </div>
    @elseif($cable && $tmPct >= $alerta)
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-bold shadow"
         style="background:#2a1f00;border:1px solid #EAB308;color:#EAB308;">
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
             style="background:#1a1000;border:1px solid #2d1f00;">
            <h3 class="text-xs font-bold tracking-widest mb-4 uppercase" style="color:#7a6040;">Tonelada-Milla</h3>

            @if($cable)
            <svg viewBox="0 0 200 120" class="w-56">
                {{-- Track --}}
                <path d="M 20 100 A 80 80 0 0 1 180 100"
                      fill="none" stroke="#2d1f00" stroke-width="18" stroke-linecap="round"/>
                {{-- Fill --}}
                @if($tmPct > 0)
                <path d="{{ $gauge['path'] }}"
                      fill="none" stroke="{{ $gauge['color'] }}" stroke-width="18" stroke-linecap="round"/>
                @endif
                {{-- Valor % --}}
                <text x="100" y="82" text-anchor="middle" font-size="26" font-weight="900"
                      fill="{{ $gauge['color'] }}" font-family="monospace">{{ $tmPct }}%</text>
                <text x="100" y="98" text-anchor="middle" font-size="9" fill="#7a6040" font-family="monospace">
                    {{ number_format($tmAcum, 2) }} / {{ number_format($tmMax, 0) }} TM
                </text>
                {{-- Marcas 0% y 100% --}}
                <text x="16" y="115" text-anchor="middle" font-size="8" fill="#4a3a20">0</text>
                <text x="184" y="115" text-anchor="middle" font-size="8" fill="#4a3a20">MAX</text>
            </svg>

            <div class="mt-4 text-center">
                <div class="text-3xl font-black font-mono" style="color:#E8A045;">
                    {{ number_format($tmAcum, 2) }} <span class="text-base font-normal" style="color:#7a6040;">TM</span>
                </div>
                <div class="text-xs mt-1" style="color:#7a6040;">
                    Restante: <span class="font-mono font-bold" style="color:#9e7c4a;">{{ number_format(max(0, $tmMax - $tmAcum), 2) }} TM</span>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <div class="text-4xl mb-3">🔗</div>
                <p class="text-sm" style="color:#7a6040;">Sin cable activo</p>
            </div>
            @endif

            {{-- Botones acción --}}
            <div class="mt-5 w-full space-y-2">
                @if($cable)
                <a href="{{ route('cable.operaciones.create') }}"
                   class="block text-center text-sm font-bold py-2 rounded-lg transition"
                   style="background:#E8A045;color:#0d0800;">
                    + Registrar operación
                </a>
                @endif
                <a href="{{ route('cable.configuracion') }}"
                   class="block text-center text-sm py-2 rounded-lg transition"
                   style="border:1px solid #2d1f00;color:#7a6040;"
                   onmouseover="this.style.borderColor='#E8A045'"
                   onmouseout="this.style.borderColor='#2d1f00'">
                    ⚙ Configuración
                </a>
            </div>
        </div>

        {{-- CABLE ACTIVO + ÚLTIMAS OPERACIONES --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Info cable activo --}}
            @if($cable)
            <div class="rounded-2xl p-5" style="background:#1a1000;border:1px solid #2d1f00;">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#7a6040;">Cable Activo</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#0d2a00;color:#4ade80;border:1px solid #166534;">● Activo</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#7a6040;">Serial</span>
                        <span class="font-mono font-bold" style="color:#E8A045;">{{ $cable->serial }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#7a6040;">Fabricante</span>
                        <span style="color:#F0EDE8;">{{ $cable->fabricante ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#7a6040;">Grado</span>
                        <span style="color:#F0EDE8;">{{ $cable->grado }}</span>
                    </div>
                    <div>
                        <span class="block text-xs uppercase font-medium" style="color:#7a6040;">Instalación</span>
                        <span style="color:#F0EDE8;">{{ $cable->fecha_instalacion->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            @else
            {{-- Registrar nuevo cable --}}
            <div class="rounded-2xl p-5" style="background:#1a1000;border:1px solid #E8A045;">
                <h3 class="text-sm font-bold mb-4" style="color:#E8A045;">Registrar Cable Activo</h3>
                <form method="POST" action="{{ route('cable.registrar') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Serial *</label>
                            <input type="text" name="serial" required
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;"
                                   placeholder="ej. 4332421-24">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Fabricante</label>
                            <input type="text" name="fabricante"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;"
                                   placeholder="ej. EMCOCABLES">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Referencia</label>
                            <input type="text" name="referencia"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;"
                                   placeholder="ej. 6X19S BIPEX AA RL">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Grado</label>
                            <select name="grado" class="w-full rounded-lg px-3 py-2 text-sm"
                                    style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                                <option value="EIP">EIP</option>
                                <option value="EEIP">EEIP</option>
                                <option value="IPS">IPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Fecha instalación *</label>
                            <input type="date" name="fecha_instalacion" required value="{{ today()->format('Y-m-d') }}"
                                   class="w-full rounded-lg px-3 py-2 text-sm"
                                   style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1 uppercase" style="color:#7a6040;">Long. inicial (ft)</label>
                            <input type="number" name="longitud_inicial_ft" step="0.01"
                                   class="w-full rounded-lg px-3 py-2 text-sm font-mono"
                                   style="background:#0d0800;border:1px solid #2d1f00;color:#F0EDE8;">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg font-bold text-sm transition"
                            style="background:#E8A045;color:#0d0800;">
                        Registrar cable y activar
                    </button>
                </form>
            </div>
            @endif

            {{-- Últimas operaciones --}}
            @if($cable && $ultimas->count())
            <div class="rounded-2xl overflow-hidden" style="background:#1a1000;border:1px solid #2d1f00;">
                <div class="px-5 py-3 flex justify-between items-center" style="border-bottom:1px solid #2d1f00;">
                    <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#7a6040;">Últimas Operaciones</h3>
                    <a href="{{ route('cable.operaciones.index') }}" class="text-xs hover:underline" style="color:#E8A045;">Ver todas →</a>
                </div>
                <table class="w-full text-sm">
                    <thead style="background:#0d0800;">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs uppercase" style="color:#7a6040;">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs uppercase" style="color:#7a6040;">Operación</th>
                            <th class="px-4 py-2 text-right text-xs uppercase" style="color:#7a6040;">TM Op.</th>
                            <th class="px-4 py-2 text-right text-xs uppercase" style="color:#7a6040;">TM Acum.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ultimas as $op)
                        <tr style="border-top:1px solid #2d1f00;">
                            <td class="px-4 py-2 font-mono text-xs" style="color:#9e7c4a;">{{ $op->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-xs" style="color:#F0EDE8;">{{ $op->tipo_operacion }}</td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-xs" style="color:#E8A045;">{{ number_format($op->tm_operacion, 4) }}</td>
                            <td class="px-4 py-2 text-right font-mono text-xs" style="color:#9e7c4a;">{{ number_format($op->tm_acumulado, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- HISTORIAL CABLES --}}
    @if($cables->count() > 1 || ($cables->count() === 1 && !$cable))
    <div class="rounded-2xl overflow-hidden" style="background:#1a1000;border:1px solid #2d1f00;">
        <div class="px-5 py-3 flex justify-between items-center" style="border-bottom:1px solid #2d1f00;">
            <h3 class="text-xs font-bold tracking-widest uppercase" style="color:#7a6040;">Cables del Rig</h3>
            <a href="{{ route('cable.historial') }}" class="text-xs hover:underline" style="color:#E8A045;">Ver historial →</a>
        </div>
        <table class="w-full text-sm">
            <thead style="background:#0d0800;">
                <tr>
                    <th class="px-4 py-2 text-left text-xs uppercase" style="color:#7a6040;">Serial</th>
                    <th class="px-4 py-2 text-left text-xs uppercase" style="color:#7a6040;">Instalación</th>
                    <th class="px-4 py-2 text-right text-xs uppercase" style="color:#7a6040;">TM Total</th>
                    <th class="px-4 py-2 text-center text-xs uppercase" style="color:#7a6040;">Ops</th>
                    <th class="px-4 py-2 text-center text-xs uppercase" style="color:#7a6040;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cables as $c)
                <tr style="border-top:1px solid #2d1f00;">
                    <td class="px-4 py-2 font-mono font-bold text-xs" style="color:#E8A045;">{{ $c->serial }}</td>
                    <td class="px-4 py-2 text-xs" style="color:#9e7c4a;">{{ $c->fecha_instalacion->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-right font-mono text-xs" style="color:#F0EDE8;">{{ number_format($c->tmAcumulado(), 2) }}</td>
                    <td class="px-4 py-2 text-center text-xs" style="color:#9e7c4a;">{{ $c->operaciones_count }}</td>
                    <td class="px-4 py-2 text-center">
                        @if($c->activo)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#0d2a00;color:#4ade80;">Activo</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background:#1a1000;color:#7a6040;">Archivado</span>
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
