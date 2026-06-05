@extends('layouts.app')
@section('title', 'Historial de Cables — ' . $rig->name)

@section('content')
<div class="pt-4 space-y-4">

    <div class="flex justify-between items-center">
        <p class="text-xs" style="color:#4a8a9e;">{{ $cables->count() }} cable(s) registrados en {{ $rig->name }}</p>
        <a href="{{ route('cable.dashboard') }}" class="text-sm hover:underline" style="color:#06B6D4;">← Dashboard Cable</a>
    </div>

    <div class="rounded-xl overflow-hidden" style="background:#001a1f;border:1px solid #003344;">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead style="background:#00111a;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Serial</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Fabricante</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Grado</th>
                        <th class="px-4 py-3 text-left text-xs uppercase" style="color:#4a8a9e;">Instalación</th>
                        <th class="px-4 py-3 text-right text-xs uppercase" style="color:#4a8a9e;">TM Total</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Ops</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Cortes</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Estado</th>
                        <th class="px-4 py-3 text-center text-xs uppercase" style="color:#4a8a9e;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cables as $c)
                    <tr style="border-top:1px solid #003344;" class="hover:opacity-80 transition">
                        <td class="px-4 py-3 font-mono font-bold text-xs" style="color:#06B6D4;">{{ $c->serial }}</td>
                        <td class="px-4 py-3 text-xs" style="color:#F0EDE8;">{{ $c->fabricante ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs" style="color:#67a8bc;">{{ $c->grado }}</td>
                        <td class="px-4 py-3 font-mono text-xs" style="color:#67a8bc;">{{ $c->fecha_instalacion->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-xs" style="color:#06B6D4;">{{ number_format($c->tm_total, 2) }}</td>
                        <td class="px-4 py-3 text-center text-xs" style="color:#67a8bc;">{{ $c->operaciones_count }}</td>
                        <td class="px-4 py-3 text-center text-xs" style="color:#67a8bc;">{{ $c->cortes_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($c->activo)
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:#0d2a00;color:#4ade80;border:1px solid #166534;">Activo</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full" style="background:#001a1f;color:#4a8a9e;border:1px solid #003344;">Archivado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$c->activo)
                            <form method="POST" action="{{ route('cable.activar', $c) }}"
                                  onsubmit="return confirm('¿Activar el cable {{ $c->serial }}? Se desactivará el cable actual.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs hover:underline" style="color:#06B6D4;">Reactivar</button>
                            </form>
                            @else
                            <span class="text-xs" style="color:#4a8a9e;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm" style="color:#4a8a9e;">Sin cables registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
