@extends('layouts.app')
@section('title', 'Historial de Cables — ' . $rig->name)

@section('content')
<div class="pt-4 space-y-4">

    <div class="flex justify-between items-center">
        <p class="text-xs" style="color:#7a6040;">{{ $cables->count() }} cable(s) registrados en {{ $rig->name }}</p>
        <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#E8A045;">← Dashboard Cable</a>
    </div>

    <div class="rounded-xl overflow-hidden" style="background:#1a1000;border:1px solid #2d1f00;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#0d0800;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Serial</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Fabricante</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Grado</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#7a6040;">Instalación</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#7a6040;">TM Total</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#7a6040;">Ops</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#7a6040;">Cortes</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#7a6040;">Estado</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#7a6040;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cables as $c)
                    <tr style="border-top:1px solid #2d1f00;" class="hover:opacity-80 transition">
                        <td class="px-4 py-3 font-mono font-bold text-xs" style="color:#E8A045;">{{ $c->serial }}</td>
                        <td class="px-4 py-3 text-xs" style="color:#F0EDE8;">{{ $c->fabricante ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs" style="color:#9e7c4a;">{{ $c->grado }}</td>
                        <td class="px-4 py-3 font-mono text-xs" style="color:#9e7c4a;">{{ $c->fecha_instalacion->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-xs" style="color:#E8A045;">{{ number_format($c->tm_total, 2) }}</td>
                        <td class="px-4 py-3 text-center text-xs" style="color:#9e7c4a;">{{ $c->operaciones_count }}</td>
                        <td class="px-4 py-3 text-center text-xs" style="color:#9e7c4a;">{{ $c->cortes_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($c->activo)
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#0d2a00;color:#4ade80;border:1px solid #166534;">Activo</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#1a1000;color:#7a6040;border:1px solid #2d1f00;">Archivado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$c->activo)
                            <form method="POST" action="{{ route('cable.activar', $c) }}"
                                  onsubmit="return confirm('¿Activar el cable {{ $c->serial }}? Se desactivará el cable actual.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs hover:underline" style="color:#E8A045;">Reactivar</button>
                            </form>
                            @else
                            <span class="text-xs" style="color:#7a6040;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm" style="color:#7a6040;">Sin cables registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
