<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\DailyLog;
use App\Services\ThresholdService;

class GerenciaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $rigsQuery = ($user->role === 'admin' || !$user->rig_id)
            ? Rig::query()
            : Rig::where('id', $user->rig_id);

        $rigs = $rigsQuery->with([
            'pumps.assemblies.components.componentHours' => fn($q) => $q->orderByDesc('id')->limit(1),
            'cables' => fn($q) => $q->where('activo', true),
        ])->get();

        $rigCards = $rigs->map(function ($rig) {
            // ── BOMBAS ──────────────────────────────────────────────────────
            $pumpIds    = $rig->pumps->pluck('id');
            $hoursToday = round(
                DailyLog::whereIn('pump_id', $pumpIds)->whereDate('log_date', today())->sum('hours_worked'),
                1
            );

            $criticalComp = 0;
            $warningComp  = 0;
            foreach ($rig->pumps as $pump) {
                foreach ($pump->assemblies as $assembly) {
                    foreach ($assembly->components as $component) {
                        $h = $component->componentHours->first()?->hours_accumulated
                            ?? $component->installed_at_hours;
                        $status = ThresholdService::getStatus($component->type, $h);
                        if ($status === 'critical') $criticalComp++;
                        elseif ($status === 'warning') $warningComp++;
                    }
                }
            }

            $pumpStatus = $criticalComp > 0 ? 'critical' : ($warningComp > 0 ? 'warning' : 'ok');

            // ── CABLE TM ─────────────────────────────────────────────────────
            $cable    = $rig->cables->first();
            $tmMax    = $rig->tm_max_corte ?? 1200;
            $tmAlerta = $rig->tm_alerta_pct ?? 80;
            $tmAcum   = 0;
            $tmPct    = 0;

            if ($cable) {
                $tmAcum = round($cable->tmAcumulado(), 2);
                $tmPct  = $tmMax > 0 ? round($tmAcum / $tmMax * 100, 1) : 0;
            }

            $cableStatus = !$cable ? 'sin-cable'
                : ($tmPct >= 95 ? 'critical' : ($tmPct >= $tmAlerta ? 'warning' : 'ok'));

            return [
                'rig'          => $rig,
                'pumpCount'    => $rig->pumps->count(),
                'hoursToday'   => $hoursToday,
                'criticalComp' => $criticalComp,
                'warningComp'  => $warningComp,
                'pumpStatus'   => $pumpStatus,
                'cable'        => $cable,
                'tmAcum'       => $tmAcum,
                'tmMax'        => $tmMax,
                'tmPct'        => $tmPct,
                'cableStatus'  => $cableStatus,
            ];
        });

        // ── Conteos globales para tarjetas resumen ────────────────────────
        $totalRigs        = $rigCards->count();
        $rigsOpHoy        = $rigCards->where('hoursToday', '>', 0)->count();
        $rigsPumpCritical = $rigCards->where('pumpStatus', 'critical')->count();
        $rigsPumpWarning  = $rigCards->where('pumpStatus', 'warning')->count();
        $rigsCableCrit    = $rigCards->where('cableStatus', 'critical')->count();
        $rigsCableWarn    = $rigCards->where('cableStatus', 'warning')->count();

        return view('gerencia.dashboard', compact(
            'rigCards',
            'totalRigs', 'rigsOpHoy',
            'rigsPumpCritical', 'rigsPumpWarning',
            'rigsCableCrit', 'rigsCableWarn'
        ));
    }
}
