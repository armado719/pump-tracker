@extends('layouts.app')
@section('title', 'Operaciones TM — ' . $cable->serial)

@section('content')
<div class="pt-4 space-y-4">

    <div class="flex justify-between items-center">
        <div>
            <p class="text-xs uppercase font-bold" style="color:#4a8a9e;">Cable: <span style="color:#06B6D4;" class="font-mono">{{ $cable->serial }}</span></p>
            <p class="text-xs" style="color:#4a8a9e;">{{ $operaciones->total() }} operaciones registradas</p>
        </div>
        <a href="{{ route('cable.operaciones.create') }}"
           class="text-sm font-bold px-4 py-2 rounded-lg transition"
           style="background:#06B6D4;color:#00111a;">
            + Nueva operación
        </a>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('cable.operaciones.index') }}"
          class="rounded-xl p-4 flex flex-wrap gap-3 items-end"
          style="background:#001a1f;border:1px solid #003344;">

        <div>
            <label class="block text-xs uppercase font-medium mb-1" style="color:#4a8a9e;">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}"
                   class="rounded-lg px-3 py-2 text-sm"
                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
        </div>

        <div>
            <label class="block text-xs uppercase font-medium mb-1" style="color:#4a8a9e;">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                   class="rounded-lg px-3 py-2 text-sm"
                   style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
        </div>

        <div class="flex-1 min-w-48">
            <label class="block text-xs uppercase font-medium mb-1" style="color:#4a8a9e;">Tipo de Operación</label>
            <select name="cod" class="w-full rounded-lg px-3 py-2 text-sm"
                    style="background:#00111a;border:1px solid #003344;color:#F0EDE8;">
                <option value="">Todos los tipos</option>
                @foreach($tiposCod as $cod => $nombre)
                    <option value="{{ $cod }}" {{ request('cod') == $cod ? 'selected' : '' }}>
                        COD {{ $cod }} — {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-bold transition"
                style="background:#06B6D4;color:#00111a;">
            Filtrar
        </button>
        <a href="{{ route('cable.operaciones.index') }}" class="px-4 py-2 rounded-lg text-sm transition"
           style="background:#00111a;color:#4a8a9e;border:1px solid #003344;">
            Limpiar
        </a>
    </form>

    <div class="rounded-xl overflow-hidden" style="background:#001a1f;border:1px solid #003344;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#00111a;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">COD</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Operación</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">Prof. Ini</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">Prof. Fin</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">TM Op.</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">TM Acum.</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operaciones as $op)
                    <tr style="border-top:1px solid #003344;{{ $op->cod === '14' ? 'background:#1a0000;' : '' }}"
                        class="hover:opacity-80 transition">
                        <td class="px-4 py-2 font-mono text-xs" style="color:#67a8bc;">{{ $op->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded"
                                  style="{{ $op->cod === '14' ? 'background:#00111a;color:#FF4D2E;' : 'background:#003344;color:#06B6D4;' }}">
                                {{ $op->cod }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs" style="color:#F0EDE8;">{{ $op->tipo_operacion }}</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#67a8bc;">{{ number_format($op->prof_inicial_ft, 0) }} ft</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#67a8bc;">{{ number_format($op->prof_final_ft, 0) }} ft</td>
                        <td class="px-4 py-2 text-right font-mono font-bold text-xs" style="color:#06B6D4;">{{ number_format($op->tm_operacion, 4) }}</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#F0EDE8;">{{ number_format($op->tm_acumulado, 2) }}</td>
                        <td class="px-4 py-2 text-xs" style="color:#4a8a9e;">{{ $op->created_by ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm" style="color:#4a8a9e;">Sin operaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $operaciones->links() }}

</div>
@endsection
