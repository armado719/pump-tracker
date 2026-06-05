@extends('layouts.app')
@section('title', 'Operaciones TM — ' . $cable->serial)

@section('content')
<div class="pt-4 space-y-4">

    <div class="flex justify-between items-center">
        <div>
            <p class="text-xs uppercase font-bold" style="color:#7a6040;">Cable: <span style="color:#E8A045;" class="font-mono">{{ $cable->serial }}</span></p>
            <p class="text-xs" style="color:#7a6040;">{{ $operaciones->total() }} operaciones registradas</p>
        </div>
        <a href="{{ route('cable.operaciones.create') }}"
           class="text-sm font-bold px-4 py-2 rounded-lg transition"
           style="background:#E8A045;color:#0d0800;">
            + Nueva operación
        </a>
    </div>

    <div class="rounded-xl overflow-hidden" style="background:#1a1000;border:1px solid #2d1f00;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#0d0800;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">COD</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Operación</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#7a6040;">Prof. Ini</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#7a6040;">Prof. Fin</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#7a6040;">TM Op.</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#7a6040;">TM Acum.</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Registrado por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operaciones as $op)
                    <tr style="border-top:1px solid #2d1f00;{{ $op->cod === '14' ? 'background:#1a0000;' : '' }}"
                        class="hover:opacity-80 transition">
                        <td class="px-4 py-2 font-mono text-xs" style="color:#9e7c4a;">{{ $op->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded"
                                  style="{{ $op->cod === '14' ? 'background:#3d0000;color:#FF4D2E;' : 'background:#2d1f00;color:#E8A045;' }}">
                                {{ $op->cod }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs" style="color:#F0EDE8;">{{ $op->tipo_operacion }}</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#9e7c4a;">{{ number_format($op->prof_inicial_ft, 0) }} ft</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#9e7c4a;">{{ number_format($op->prof_final_ft, 0) }} ft</td>
                        <td class="px-4 py-2 text-right font-mono font-bold text-xs" style="color:#E8A045;">{{ number_format($op->tm_operacion, 4) }}</td>
                        <td class="px-4 py-2 text-right font-mono text-xs" style="color:#F0EDE8;">{{ number_format($op->tm_acumulado, 2) }}</td>
                        <td class="px-4 py-2 text-xs" style="color:#7a6040;">{{ $op->created_by ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm" style="color:#7a6040;">Sin operaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $operaciones->links() }}

</div>
@endsection
