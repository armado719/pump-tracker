<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\Well;
use App\Services\AuditService;
use Illuminate\Http\Request;

class WellController extends Controller
{
    // ─── Gestión centralizada de pozos (admin) ────────────────────────────────

    public function index()
    {
        $wells = Well::with('rig')->withCount('dailyLogs')
            ->orderBy('rig_id')->orderBy('name')->get();
        $rigs  = Rig::orderBy('name')->get();
        return view('wells.index', compact('wells', 'rigs'));
    }

    public function create()
    {
        $rigs = Rig::orderBy('name')->get();
        return view('wells.create', compact('rigs'));
    }

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'rig_id' => 'required|exists:rigs,id',
            'name'   => 'required|string|max:50',
        ]);

        $rig  = Rig::findOrFail($data['rig_id']);
        $well = $rig->wells()->create(['name' => $data['name']]);

        AuditService::log('creó', 'pozo', "Creó pozo '{$well->name}' en {$rig->name}");
        return redirect()->route('wells.index')
            ->with('success', "Pozo '{$well->name}' creado en {$rig->name}.");
    }

    public function edit(Well $well)
    {
        $rigs = Rig::orderBy('name')->get();
        return view('wells.edit', compact('well', 'rigs'));
    }

    public function update(Request $request, Well $well)
    {
        $data = $request->validate([
            'rig_id' => 'required|exists:rigs,id',
            'name'   => 'required|string|max:50',
        ]);

        $well->update($data);
        AuditService::log('actualizó', 'pozo', "Actualizó pozo '{$data['name']}'");
        return redirect()->route('wells.index')->with('success', 'Pozo actualizado.');
    }

    public function destroyAdmin(Well $well)
    {
        $name = $well->name;
        $rig  = $well->rig?->name ?? '—';
        $well->delete();
        AuditService::log('eliminó', 'pozo', "Eliminó pozo '{$name}' de {$rig}");
        return redirect()->route('wells.index')->with('success', "Pozo '{$name}' eliminado.");
    }

    // ─── Inline desde la página del rig (rutas existentes) ───────────────────

    public function store(Request $request, Rig $rig)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $rig->wells()->create($data);

        return redirect()->route('rigs.show', $rig)
            ->with('success', "Pozo '{$data['name']}' agregado al {$rig->name}.");
    }

    public function destroy(Rig $rig, Well $well)
    {
        $well->delete();
        return redirect()->route('rigs.show', $rig)
            ->with('success', 'Pozo eliminado.');
    }
}
