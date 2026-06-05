@extends('layouts.app')
@section('title', 'Historial de Auditoría')

@section('content')
<div class="pt-4 space-y-4">

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('audit.index') }}"
          class="rounded-xl p-4 flex flex-wrap gap-3 items-end"
          style="background:#0d1f2d;border:1px solid #1a3040;">

        <div class="flex-1 min-w-36">
            <label class="block text-xs uppercase font-medium mb-1" style="color:#6b9e82;">Módulo</label>
            <select name="module" class="w-full rounded-lg px-3 py-2 text-sm"
                    style="background:#0a1825;border:1px solid #1a3040;color:#F0EDE8;">
                <option value="">Todos</option>
                @foreach($modules as $m)
                <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>
                    {{ ucfirst($m) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 min-w-36">
            <label class="block text-xs uppercase font-medium mb-1" style="color:#6b9e82;">Usuario</label>
            <input type="text" name="user" value="{{ request('user') }}" placeholder="Nombre..."
                   class="w-full rounded-lg px-3 py-2 text-sm"
                   style="background:#0a1825;border:1px solid #1a3040;color:#F0EDE8;">
        </div>

        <div>
            <label class="block text-xs uppercase font-medium mb-1" style="color:#6b9e82;">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}"
                   class="rounded-lg px-3 py-2 text-sm"
                   style="background:#0a1825;border:1px solid #1a3040;color:#F0EDE8;">
        </div>

        <div>
            <label class="block text-xs uppercase font-medium mb-1" style="color:#6b9e82;">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                   class="rounded-lg px-3 py-2 text-sm"
                   style="background:#0a1825;border:1px solid #1a3040;color:#F0EDE8;">
        </div>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-bold transition"
                style="background:#166534;color:#4ade80;">
            Filtrar
        </button>
        <a href="{{ route('audit.index') }}" class="px-4 py-2 rounded-lg text-sm transition"
           style="background:#0a1825;color:#6b9e82;border:1px solid #1a3040;">
            Limpiar
        </a>
    </form>

    {{-- TABLA --}}
    <div class="rounded-xl overflow-hidden" style="background:#0d1f2d;border:1px solid #1a3040;">
        <div class="px-5 py-3 flex items-center justify-between" style="border-bottom:1px solid #1a3040;">
            <h3 class="text-xs font-bold uppercase tracking-widest" style="color:#6b9e82;">
                Registros — {{ $audits->total() }} entradas
            </h3>
        </div>

        @if($audits->isEmpty())
        <div class="p-10 text-center" style="color:#4a8a9e;">Sin registros de auditoría aún.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#0a1825;">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">Fecha / Hora</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">Usuario</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">Módulo</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">Acción</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">Descripción</th>
                        <th class="px-4 py-2 text-left text-xs uppercase" style="color:#6b9e82;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $a)
                    @php
                        $actionColor = match($a->action) {
                            'creó'      => '#4ade80',
                            'registró'  => '#06B6D4',
                            'actualizó' => '#EAB308',
                            'eliminó'   => '#FF4D2E',
                            'activó'    => '#4ade80',
                            'desactivó' => '#9ca3af',
                            'reemplazó' => '#fb923c',
                            'envió'     => '#c084fc',
                            default     => '#F0EDE8',
                        };
                        $moduleColor = match($a->module) {
                            'usuario'       => '#a78bfa',
                            'rig'           => '#f97316',
                            'bomba'         => '#4ade80',
                            'componente'    => '#86efac',
                            'log-diario'    => '#60a5fa',
                            'cable'         => '#06B6D4',
                            'operacion'     => '#22d3ee',
                            'pozo'          => '#fbbf24',
                            'configuración' => '#e879f9',
                            default         => '#9ca3af',
                        };
                    @endphp
                    <tr style="border-top:1px solid #1a3040;">
                        <td class="px-4 py-2 font-mono text-xs" style="color:#6b9e82;white-space:nowrap;">
                            {{ $a->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-xs font-medium" style="color:#F0EDE8;">{{ $a->user_name }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold"
                                  style="color:{{ $moduleColor }};background:{{ $moduleColor }}18;border:1px solid {{ $moduleColor }}44;">
                                {{ $a->module }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="text-xs font-bold" style="color:{{ $actionColor }};">{{ $a->action }}</span>
                        </td>
                        <td class="px-4 py-2 text-xs" style="color:#9ab8a8;">{{ $a->description }}</td>
                        <td class="px-4 py-2 font-mono text-xs" style="color:#4a8a9e;">{{ $a->ip_address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3" style="border-top:1px solid #1a3040;">
            {{ $audits->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
