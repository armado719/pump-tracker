<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\User;
use App\Mail\AlertasCriticas;
use App\Services\ThresholdService;
use Illuminate\Support\Facades\Mail;

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

    public function notificar()
    {
        $rigs = Rig::with([
            'pumps.assemblies.components.componentHours' => fn($q) => $q->orderByDesc('id')->limit(1),
            'cables' => fn($q) => $q->where('activo', true),
        ])->get();

        $enviados = 0;

        foreach ($rigs as $rig) {
            // Alertas de bombas para este rig
            $alertasBombas = [];
            foreach ($rig->pumps as $pump) {
                foreach ($pump->assemblies as $assembly) {
                    foreach ($assembly->components as $component) {
                        $hours  = $component->componentHours->first()?->hours_accumulated ?? $component->installed_at_hours;
                        $status = ThresholdService::getStatus($component->type, $hours);
                        $thresh = ThresholdService::getThresholds($component->type);
                        if ($status !== 'ok') {
                            $alertasBombas[] = [
                                'pump'      => "Bomba #{$pump->number}",
                                'assembly'  => $assembly->position_label,
                                'component' => $component->type_label,
                                'hours'     => $hours,
                                'critical'  => $thresh['critical'],
                                'status'    => $status,
                            ];
                        }
                    }
                }
            }

            // Alertas de cable TM para este rig
            $alertasCables = [];
            $cable   = $rig->cables->first();
            $tmMax   = $rig->tm_max_corte ?? 1200;
            $alerta  = $rig->tm_alerta_pct ?? 80;
            if ($cable) {
                $tmAcum = $cable->tmAcumulado();
                $tmPct  = $tmMax > 0 ? round($tmAcum / $tmMax * 100, 1) : 0;
                if ($tmPct >= $alerta) {
                    $alertasCables[] = [
                        'serial' => $cable->serial,
                        'tmAcum' => $tmAcum,
                        'tmMax'  => $tmMax,
                        'pct'    => $tmPct,
                        'status' => $tmPct >= 95 ? 'critical' : 'warning',
                    ];
                }
            }

            if (empty($alertasBombas) && empty($alertasCables)) {
                continue;
            }

            // Destinatarios: usuarios del rig + todos los admins
            $destinatarios = User::where(fn($q) =>
                $q->where('rig_id', $rig->id)->orWhere('role', 'admin')
            )->whereNotNull('email')->pluck('email')->unique();

            foreach ($destinatarios as $email) {
                Mail::to($email)->send(new AlertasCriticas($alertasBombas, $alertasCables, $rig->name));
                $enviados++;
            }
        }

        $msg = $enviados > 0
            ? "Notificaciones enviadas a {$enviados} destinatario(s)."
            : 'Sin alertas activas — no se enviaron notificaciones.';

        return back()->with('success', $msg);
    }
}
