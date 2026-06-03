<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Services\ThresholdService;

class AlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rigsQuery = ($user->role === 'admin' || !$user->rig_id)
            ? Rig::query()
            : Rig::where('id', $user->rig_id);

        $rigs = $rigsQuery->with([
            'pumps.assemblies.components.componentHours' => fn($q) => $q->orderByDesc('id')->limit(1),
        ])->get();

        $alerts = [];
        foreach ($rigs as $rig) {
            foreach ($rig->pumps as $pump) {
                foreach ($pump->assemblies as $assembly) {
                    foreach ($assembly->components as $component) {
                        $hours  = $component->componentHours->first()?->hours_accumulated ?? $component->installed_at_hours;
                        $status = ThresholdService::getStatus($component->type, $hours);
                        $thresh = ThresholdService::getThresholds($component->type);
                        if ($status !== 'ok') {
                            $alerts[] = [
                                'rig'       => $rig->name,
                                'pump'      => "Bomba #{$pump->number}",
                                'pumpId'    => $pump->id,
                                'assembly'  => $assembly->position_label,
                                'component' => $component->type_label,
                                'hours'     => $hours,
                                'warning'   => $thresh['warning'],
                                'critical'  => $thresh['critical'],
                                'status'    => $status,
                                'badge'     => ThresholdService::getStatusBadgeClass($status),
                                'label'     => ThresholdService::getStatusLabel($status),
                            ];
                        }
                    }
                }
            }
        }

        // Ordenar: critical primero, luego warning; dentro de cada grupo por horas desc
        usort($alerts, function($a, $b) {
            $order = ['critical' => 0, 'warning' => 1];
            $cmp = ($order[$a['status']] ?? 2) <=> ($order[$b['status']] ?? 2);
            return $cmp !== 0 ? $cmp : $b['hours'] <=> $a['hours'];
        });

        $criticalCount = collect($alerts)->where('status', 'critical')->count();
        $warningCount  = collect($alerts)->where('status', 'warning')->count();

        return view('alerts.index', compact('alerts', 'criticalCount', 'warningCount'));
    }
}
