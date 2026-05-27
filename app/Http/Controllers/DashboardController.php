<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\DailyLog;
use App\Services\ThresholdService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $rigsQuery = $user->role === 'admin'
            ? Rig::query()
            : Rig::where('id', $user->rig_id);

        $rigs = $rigsQuery->with([
            'pumps.assemblies.components.componentHours',
            'pumps.dailyLogs' => fn($q) => $q->whereDate('log_date', today()),
        ])->get();

        $totalRigs  = $rigs->count();
        $pumpsToday = $rigs->flatMap->pumps->filter(fn($p) => $p->dailyLogs->isNotEmpty())->count();

        // Alertas: componentes con status warning/critical
        $criticalCount = 0;
        $criticalComponents = [];

        foreach ($rigs as $rig) {
            foreach ($rig->pumps as $pump) {
                foreach ($pump->assemblies as $assembly) {
                    foreach ($assembly->components as $component) {
                        $latestHours = $component->componentHours->sortByDesc('id')->first()?->hours_accumulated
                            ?? $component->installed_at_hours;
                        $status = ThresholdService::getStatus($component->type, $latestHours);
                        if (in_array($status, ['critical', 'warning'])) {
                            $criticalComponents[] = [
                                'rig'       => $rig->name,
                                'pump'      => "Bomba #{$pump->number}",
                                'assembly'  => $assembly->position_label,
                                'component' => $component->type_label,
                                'hours'     => $latestHours,
                                'status'    => $status,
                                'badge'     => ThresholdService::getStatusBadgeClass($status),
                                'label'     => ThresholdService::getStatusLabel($status),
                            ];
                            if ($status === 'critical') $criticalCount++;
                        }
                    }
                }
            }
        }

        usort($criticalComponents, fn($a, $b) => $b['hours'] <=> $a['hours']);
        $topAlerts = array_slice($criticalComponents, 0, 5);

        $pumpIds = $rigs->flatMap->pumps->pluck('id');
        $avgHours = round(
            DailyLog::whereIn('pump_id', $pumpIds)
                ->where('log_date', '>=', now()->subDays(7))
                ->avg('hours_worked') ?? 0,
            2
        );

        // Estado de rigs para la tabla
        $rigStatus = $rigs->map(function($rig) {
            $pIds = $rig->pumps->pluck('id');
            $lastLog = DailyLog::whereIn('pump_id', $pIds)->orderByDesc('log_date')->first();
            $hoursToday = DailyLog::whereIn('pump_id', $pIds)->whereDate('log_date', today())->sum('hours_worked');
            return [
                'rig'        => $rig,
                'well'       => $lastLog?->well?->name ?? '—',
                'pumps'      => $rig->pumps->count(),
                'hoursToday' => round($hoursToday, 2),
                'lastUpdate' => $lastLog?->log_date?->format('d/m/Y') ?? '—',
                'status'     => $hoursToday > 0 ? 'active' : 'idle',
            ];
        });

        // Datos para gráfica de horas acumuladas por rig (últimos 30 días)
        $labels = collect();
        for ($i = 29; $i >= 0; $i--) {
            $labels->push(now()->subDays($i)->format('d/m'));
        }
        $chartDatasets = $rigs->take(5)->map(function($rig) {
            $pIds = $rig->pumps->pluck('id');
            $logs = DailyLog::whereIn('pump_id', $pIds)
                ->where('log_date', '>=', now()->subDays(30))
                ->selectRaw('DATE(log_date) as date, SUM(hours_worked) as total')
                ->groupBy('date')->pluck('total', 'date');

            $data = [];
            for ($i = 29; $i >= 0; $i--) {
                $d = now()->subDays($i)->format('Y-m-d');
                $data[] = $logs[$d] ?? 0;
            }
            return ['label' => $rig->name, 'data' => $data];
        });

        return view('dashboard.index', compact(
            'totalRigs', 'pumpsToday', 'criticalCount', 'avgHours',
            'topAlerts', 'rigStatus', 'labels', 'chartDatasets'
        ));
    }
}
