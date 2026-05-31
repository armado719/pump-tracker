<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\Rig;
use App\Models\DailyLog;
use App\Models\AssemblyComponent;
use App\Models\MaintenanceEvent;
use App\Services\ThresholdService;
use Illuminate\Http\Request;

class PumpController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $pumps = $user->role === 'admin'
            ? Pump::with('rig')->get()
            : Pump::whereHas('rig', fn($q) => $q->where('id', $user->rig_id))->with('rig')->get();

        return view('pumps.index', compact('pumps'));
    }

    public function create()
    {
        $rigs = Rig::all();
        return view('pumps.create', compact('rigs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rig_id'                 => 'required|exists:rigs,id',
            'number'                 => 'required|integer',
            'brand'                  => 'nullable|string|max:50',
            'model'                  => 'nullable|string|max:50',
            'serial'                 => 'nullable|string|max:50',
            'liner_diameter'         => 'nullable|string|max:20',
            'active_fixed_id'        => 'nullable|string|max:30',
            'base_accumulated_hours' => 'nullable|numeric|min:0',
        ]);

        $pump = Pump::create($data);

        // Crear automáticamente los 3 conjuntos y 8 componentes cada uno
        foreach (['left', 'middle', 'right'] as $position) {
            $assembly = $pump->assemblies()->create(['position' => $position]);
            foreach (ThresholdService::COMPONENT_ORDER as $type) {
                $thresh = ThresholdService::getThresholds($type);
                $assembly->components()->create([
                    'type'                    => $type,
                    'alert_threshold_warning' => $thresh['warning'],
                    'alert_threshold_critical'=> $thresh['critical'],
                ]);
            }
        }

        return redirect()->route('pumps.show', $pump)->with('success', 'Bomba creada correctamente.');
    }

    public function show(Pump $pump)
    {
        $pump->load([
            'rig',
            'assemblies.components.componentHours' => fn($q) => $q->orderBy('id', 'desc'),
            'dailyLogs' => fn($q) => $q->with('well')->orderByDesc('log_date')->limit(31),
            'personnel' => fn($q) => $q->orderByDesc('period_start'),
        ]);

        // Horas diarias para gráfica (últimos 31 días)
        $chartLabels = [];
        $chartHours  = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $log = $pump->dailyLogs->firstWhere('log_date', $date);
            $chartHours[] = $log ? $log->hours_worked : 0;
        }

        // Componentes con status para gráfica horizontal
        $componentStats = [];
        foreach ($pump->assemblies as $assembly) {
            foreach ($assembly->components()->orderByRaw("FIELD(type, 'camisa','piston','suction_module','suction_valve','suction_seat','discharge_module','discharge_valve','discharge_seat')")->get() as $component) {
                $hours  = $component->componentHours->first()?->hours_accumulated ?? $component->installed_at_hours;
                $status = ThresholdService::getStatus($component->type, $hours);
                $thresh = ThresholdService::getThresholds($component->type);
                $componentStats[] = [
                    'label'    => "{$assembly->position_label} — {$component->type_label}",
                    'hours'    => $hours,
                    'warning'  => $thresh['warning'],
                    'critical' => $thresh['critical'],
                    'status'   => $status,
                ];
            }
        }

        // Historial de mantenimiento
        $maintenanceHistory = $pump->assemblies
            ->flatMap->components
            ->flatMap->maintenanceEvents
            ->sortByDesc('created_at')
            ->take(20);

        $currentPersonnel = $pump->currentPersonnel();

        return view('pumps.show', compact(
            'pump', 'chartLabels', 'chartHours',
            'componentStats', 'maintenanceHistory', 'currentPersonnel'
        ));
    }

    public function edit(Pump $pump)
    {
        $rigs = Rig::all();
        return view('pumps.edit', compact('pump', 'rigs'));
    }

    public function update(Request $request, Pump $pump)
    {
        $data = $request->validate([
            'rig_id'                 => 'required|exists:rigs,id',
            'number'                 => 'required|integer',
            'brand'                  => 'nullable|string|max:50',
            'model'                  => 'nullable|string|max:50',
            'serial'                 => 'nullable|string|max:50',
            'liner_diameter'         => 'nullable|string|max:20',
            'active_fixed_id'        => 'nullable|string|max:30',
            'base_accumulated_hours' => 'nullable|numeric|min:0',
        ]);
        $pump->update($data);
        return redirect()->route('pumps.show', $pump)->with('success', 'Bomba actualizada.');
    }

    public function replacements(Pump $pump)
    {
        $pump->load('rig');

        $events = MaintenanceEvent::whereHas('component.assembly', fn($q) => $q->where('pump_id', $pump->id))
            ->with(['component.assembly', 'dailyLog'])
            ->orderByDesc('created_at')
            ->get();

        return view('pumps.replacements', compact('pump', 'events'));
    }

    public function destroy(Pump $pump)
    {
        $pump->delete();
        return redirect()->route('rigs.show', $pump->rig_id)->with('success', 'Bomba eliminada.');
    }
}
