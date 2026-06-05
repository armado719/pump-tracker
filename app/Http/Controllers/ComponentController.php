<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\AssemblyComponent;
use App\Models\MaintenanceEvent;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComponentController extends Controller
{
    public function replaceForm(Pump $pump, AssemblyComponent $component)
    {
        $component->load('assembly');

        $currentHours = $component->componentHours()
            ->orderByDesc('id')->first()?->hours_accumulated
            ?? $component->installed_at_hours;

        $lastReplacement = $component->maintenanceEvents()
            ->orderByDesc('id')->first();

        return view('maintenance.replace', compact(
            'pump', 'component', 'currentHours', 'lastReplacement'
        ));
    }

    public function replace(Request $request, Pump $pump, AssemblyComponent $component)
    {
        $data = $request->validate([
            'new_serial' => 'nullable|string|max:50',
            'notes'      => 'nullable|string|max:500',
        ]);

        $component->load('assembly');
        $pump->load('rig');

        $hoursBefore = $component->componentHours()
            ->orderByDesc('id')->first()?->hours_accumulated
            ?? $component->installed_at_hours;

        DB::transaction(function () use ($data, $component, $hoursBefore) {
            MaintenanceEvent::create([
                'daily_log_id' => null,
                'component_id' => $component->id,
                'event_type'   => 'replacement',
                'hours_before' => $hoursBefore,
                'new_serial'   => $data['new_serial'] ?? null,
                'notes'        => $data['notes'] ?? null,
            ]);

            $component->update([
                'serial'             => $data['new_serial'] ?? null,
                'installed_at_hours' => 0,
            ]);
        });

        AuditService::log('reemplazó', 'componente',
            "Reemplazó {$component->type_label} en Bomba #{$pump->number} ({$pump->rig->name}) — Conjunto {$component->assembly->position_label}");

        return redirect()->route('pumps.show', $pump)
            ->with('success', "'{$component->type_label}' reemplazado correctamente. El contador de horas reinicia en el próximo registro diario.");
    }
}
