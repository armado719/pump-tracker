<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\Well;
use App\Models\Pump;
use App\Models\PumpAssembly;
use App\Models\AssemblyComponent;
use App\Services\AuditService;
use App\Services\ThresholdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RigController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rigs = $user->role === 'admin'
            ? Rig::withCount('pumps')->get()
            : Rig::where('id', $user->rig_id)->withCount('pumps')->get();

        return view('rigs.index', compact('rigs'));
    }

    public function create()
    {
        $this->authorize('admin');
        return view('rigs.create');
    }

    public function store(Request $request)
    {
        $this->authorize('admin');

        $data = $request->validate([
            'name'     => 'required|string|max:50',
            'location' => 'nullable|string|max:100',
            'manager'  => 'nullable|string|max:200',
            // Bombas opcionales en la creación
            'pumps'              => 'nullable|array',
            'pumps.*.number'     => 'required|integer',
            'pumps.*.brand'      => 'nullable|string|max:50',
            'pumps.*.model'      => 'nullable|string|max:50',
            'pumps.*.serial'     => 'nullable|string|max:50',
            'pumps.*.liner_diameter'    => 'nullable|string|max:20',
            'pumps.*.active_fixed_id'   => 'nullable|string|max:30',
            'pumps.*.base_accumulated_hours' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($data) {
            $rig = Rig::create([
                'name'     => $data['name'],
                'location' => $data['location'] ?? null,
                'manager'  => $data['manager'] ?? null,
            ]);

            foreach ($data['pumps'] ?? [] as $pumpData) {
                $this->createPumpWithComponents($rig, $pumpData);
            }
        });

        AuditService::log('creó', 'rig', "Creó rig {$data['name']}");
        return redirect()->route('rigs.index')->with('success', 'Rig creado exitosamente.');
    }

    public function show(Rig $rig)
    {
        $this->authorizeRig($rig);
        $rig->load(['pumps.assemblies.components.componentHours', 'wells']);
        return view('rigs.show', compact('rig'));
    }

    public function edit(Rig $rig)
    {
        $this->authorize('admin');
        return view('rigs.edit', compact('rig'));
    }

    public function update(Request $request, Rig $rig)
    {
        $this->authorize('admin');
        $data = $request->validate([
            'name'                   => 'required|string|max:50',
            'location'               => 'nullable|string|max:100',
            'manager'                => 'nullable|string|max:200',
            'altura_torre_ft'        => 'nullable|numeric|min:0',
            'diametro_tambor_in'     => 'nullable|numeric|min:0',
            'lineas_activas'         => 'nullable|integer|min:1|max:20',
            'tm_max_corte'           => 'nullable|numeric|min:1',
            'tipo_cable'             => 'nullable|string|max:10',
            'peso_bloque_default_lb' => 'nullable|numeric|min:0',
            'tm_alerta_pct'          => 'nullable|numeric|min:1|max:100',
        ]);
        $rig->update($data);
        AuditService::log('actualizó', 'rig', "Actualizó rig {$rig->name}");
        return redirect()->route('rigs.show', $rig)->with('success', 'Rig actualizado.');
    }

    public function destroy(Rig $rig)
    {
        $this->authorize('admin');
        AuditService::log('eliminó', 'rig', "Eliminó rig {$rig->name}");
        $rig->delete();
        return redirect()->route('rigs.index')->with('success', 'Rig eliminado.');
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function createPumpWithComponents(Rig $rig, array $pumpData): Pump
    {
        $pump = Pump::create([
            'rig_id'                  => $rig->id,
            'number'                  => $pumpData['number'],
            'brand'                   => $pumpData['brand'] ?? null,
            'model'                   => $pumpData['model'] ?? null,
            'serial'                  => $pumpData['serial'] ?? null,
            'liner_diameter'          => $pumpData['liner_diameter'] ?? null,
            'active_fixed_id'         => $pumpData['active_fixed_id'] ?? null,
            'base_accumulated_hours'  => $pumpData['base_accumulated_hours'] ?? 0,
        ]);

        foreach (['left', 'middle', 'right'] as $position) {
            $assembly = PumpAssembly::create([
                'pump_id'  => $pump->id,
                'position' => $position,
            ]);

            foreach (ThresholdService::COMPONENT_ORDER as $type) {
                $thresh = ThresholdService::getThresholds($type);
                AssemblyComponent::create([
                    'assembly_id'              => $assembly->id,
                    'type'                     => $type,
                    'serial'                   => null,
                    'installed_at_hours'       => 0,
                    'alert_threshold_warning'  => $thresh['warning'],
                    'alert_threshold_critical' => $thresh['critical'],
                ]);
            }
        }

        return $pump;
    }

    private function authorize(string $role): void
    {
        if (auth()->user()->role !== $role) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }
    }

    private function authorizeRig(Rig $rig): void
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->rig_id !== $rig->id) {
            abort(403);
        }
    }
}
