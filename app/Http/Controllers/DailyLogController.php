<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\DailyLog;
use App\Models\ComponentHour;
use App\Models\MaintenanceEvent;
use App\Models\AssemblyComponent;
use App\Services\ThresholdService;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyLogController extends Controller
{
    /**
     * Formulario de registro diario — pantalla principal de operación
     */
    public function create(Pump $pump)
    {
        $pump->load([
            'rig',
            'assemblies' => fn($q) => $q->orderByRaw("FIELD(position,'left','middle','right')"),
            'assemblies.components' => fn($q) => $q->orderByRaw(
                "FIELD(type,'camisa','piston','suction_module','suction_valve','suction_seat','discharge_module','discharge_valve','discharge_seat')"
            ),
        ]);

        // Último log para calcular horas acumuladas anteriores
        $lastLog = $pump->dailyLogs()->orderByDesc('log_date')->first();
        $previousAccum = $lastLog?->accumulated_hours ?? $pump->base_accumulated_hours;
        $dayNumber     = ($lastLog?->day_number ?? 0) + 1;

        // Horas anteriores de cada componente (clave: component_id)
        $previousComponentHours = [];
        if ($lastLog) {
            foreach ($lastLog->componentHours as $ch) {
                $previousComponentHours[$ch->component_id] = $ch->hours_accumulated;
            }
        } else {
            // Primera vez: usar installed_at_hours
            foreach ($pump->assemblies as $assembly) {
                foreach ($assembly->components as $component) {
                    $previousComponentHours[$component->id] = $component->installed_at_hours;
                }
            }
        }

        // Si hubo reemplazos standalone (sin log diario) desde el último log, reiniciar esas horas a 0
        $replacedIds = \App\Models\MaintenanceEvent::whereNull('daily_log_id')
            ->whereIn('component_id', array_keys($previousComponentHours))
            ->when($lastLog, fn($q) => $q->where('created_at', '>', $lastLog->created_at))
            ->pluck('component_id')->unique()->toArray();

        foreach ($replacedIds as $cid) {
            $previousComponentHours[$cid] = 0;
        }

        // Wells del rig para selector
        $wells = $pump->rig->wells;
        $currentPersonnel = $pump->currentPersonnel();

        return view('logs.create', compact(
            'pump', 'lastLog', 'previousAccum', 'dayNumber',
            'previousComponentHours', 'wells', 'currentPersonnel'
        ));
    }

    /**
     * Guardar registro diario con horas de componentes
     */
    public function store(Request $request, Pump $pump)
    {
        $data = $request->validate([
            'well_id'           => 'nullable|exists:wells,id',
            'log_date'          => 'required|date',
            'day_number'        => 'required|integer|min:1|max:31',
            'hours_worked'      => 'required|numeric|min:0|max:24',
            'accumulated_hours' => 'required|numeric|min:0',
            'dampener_pressure' => 'nullable|integer|min:0',
            'comments'          => 'nullable|string|max:500',
            // Horas de componentes: array indexado por component_id
            'component_hours'               => 'required|array',
            'component_hours.*'             => 'required|numeric|min:0',
            // Cambios (replacements): componentes reemplazados
            'replacements'                  => 'nullable|array',
            'replacements.*.component_id'   => 'required|exists:assembly_components,id',
            'replacements.*.new_serial'     => 'nullable|string|max:50',
            'replacements.*.hours_before'   => 'required|numeric|min:0',
            'replacements.*.notes'          => 'nullable|string|max:500',
        ]);

        DB::transaction(function() use ($data, $pump, $request) {
            // 1. Crear el log diario
            $log = DailyLog::create([
                'pump_id'           => $pump->id,
                'well_id'           => $data['well_id'] ?? null,
                'log_date'          => $data['log_date'],
                'day_number'        => $data['day_number'],
                'hours_worked'      => $data['hours_worked'],
                'accumulated_hours' => $data['accumulated_hours'],
                'dampener_pressure' => $data['dampener_pressure'] ?? null,
                'comments'          => $data['comments'] ?? null,
                'synced'            => true,
            ]);

            // 2. Guardar horas de cada componente (30 filas = 3 conjuntos × 8 componentes + 6 módulos)
            foreach ($data['component_hours'] as $componentId => $hours) {
                ComponentHour::create([
                    'daily_log_id'     => $log->id,
                    'component_id'     => $componentId,
                    'hours_accumulated'=> $hours,
                ]);
            }

            // 3. Procesar reemplazos
            foreach ($data['replacements'] ?? [] as $replacement) {
                // Crear evento de mantenimiento
                MaintenanceEvent::create([
                    'daily_log_id' => $log->id,
                    'component_id' => $replacement['component_id'],
                    'event_type'   => 'replacement',
                    'hours_before' => $replacement['hours_before'],
                    'new_serial'   => $replacement['new_serial'] ?? null,
                    'notes'        => $replacement['notes'] ?? null,
                ]);

                // Actualizar serial y horas de instalación del componente
                AssemblyComponent::where('id', $replacement['component_id'])->update([
                    'serial'             => $replacement['new_serial'] ?? null,
                    'installed_at_hours' => $data['hours_worked'],
                ]);
            }
        });

        AuditService::log('registró', 'log-diario',
            "Registró día {$data['day_number']} — Bomba #{$pump->number} ({$pump->rig->name}) — {$data['hours_worked']}h");
        return redirect()->route('pumps.show', $pump)
            ->with('success', "Registro del día {$data['day_number']} guardado correctamente.");
    }

    /**
     * Formulario de edición de un registro diario
     */
    public function edit(Pump $pump, DailyLog $log)
    {
        $pump->load('rig');
        $wells = $pump->rig->wells;
        $currentPersonnel = $pump->currentPersonnel();

        // Acumulado del log anterior (base para recalcular)
        $prevLog = $pump->dailyLogs()
            ->where('id', '!=', $log->id)
            ->where('log_date', '<=', $log->log_date)
            ->orderByDesc('log_date')
            ->first();
        $previousAccum = $prevLog?->accumulated_hours ?? $pump->base_accumulated_hours;

        return view('logs.edit', compact('pump', 'log', 'wells', 'previousAccum', 'currentPersonnel'));
    }

    /**
     * Actualizar un registro diario (corrige horas, comentarios, etc.)
     */
    public function update(Request $request, Pump $pump, DailyLog $log)
    {
        $data = $request->validate([
            'well_id'           => 'nullable|exists:wells,id',
            'log_date'          => 'required|date',
            'hours_worked'      => 'required|numeric|min:0|max:24',
            'dampener_pressure' => 'nullable|integer|min:0',
            'comments'          => 'nullable|string|max:500',
        ]);

        $diff = $data['hours_worked'] - $log->hours_worked;

        $prevLog = $pump->dailyLogs()
            ->where('id', '!=', $log->id)
            ->where('log_date', '<=', $log->log_date)
            ->orderByDesc('log_date')
            ->first();
        $previousAccum = $prevLog?->accumulated_hours ?? $pump->base_accumulated_hours;
        $newAccum = $previousAccum + $data['hours_worked'];

        DB::transaction(function() use ($data, $log, $diff, $newAccum) {
            $log->update([
                'well_id'           => $data['well_id'] ?? null,
                'log_date'          => $data['log_date'],
                'hours_worked'      => $data['hours_worked'],
                'accumulated_hours' => $newAccum,
                'dampener_pressure' => $data['dampener_pressure'] ?? null,
                'comments'          => $data['comments'] ?? null,
            ]);

            // Ajustar horas de cada componente con la diferencia
            if ($diff != 0) {
                foreach ($log->componentHours as $ch) {
                    $ch->update(['hours_accumulated' => max(0, $ch->hours_accumulated + $diff)]);
                }
            }
        });

        return redirect()->route('pumps.logs.show', [$pump, $log])
            ->with('success', "Registro del día {$log->day_number} actualizado correctamente.");
    }

    /**
     * Ver todos los registros de una bomba
     */
    public function index(Pump $pump)
    {
        $logs = $pump->dailyLogs()
            ->with('well')
            ->orderByDesc('log_date')
            ->paginate(31);

        return view('logs.index', compact('pump', 'logs'));
    }

    /**
     * Ver detalle de un registro
     */
    public function show(Pump $pump, DailyLog $log)
    {
        $log->load([
            'well',
            'componentHours.component.assembly',
            'maintenanceEvents.component',
        ]);

        // Organizar por conjunto
        $assembliesByPosition = [];
        foreach ($log->componentHours as $ch) {
            $pos = $ch->component->assembly->position;
            $assembliesByPosition[$pos][] = $ch;
        }

        return view('logs.show', compact('pump', 'log', 'assembliesByPosition'));
    }
}
