<?php

namespace App\Console\Commands;

use App\Mail\AlertasCriticas;
use App\Models\Rig;
use App\Models\User;
use App\Services\AuditService;
use App\Services\ThresholdService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarAlertasDiarias extends Command
{
    protected $signature   = 'alertas:enviar {--rig= : ID del rig específico (omitir = todos)}';
    protected $description = 'Envía correos de alerta de bombas y cable TM a los usuarios del rig';

    public function handle(): int
    {
        $rigQuery = Rig::with([
            'pumps.assemblies.components.componentHours' => fn($q) => $q->orderByDesc('id')->limit(1),
            'cables' => fn($q) => $q->where('activo', true),
        ]);

        if ($this->option('rig')) {
            $rigQuery->where('id', $this->option('rig'));
        }

        $rigs     = $rigQuery->get();
        $enviados = 0;
        $sinAlertas = 0;

        foreach ($rigs as $rig) {
            $alertasBombas = [];
            foreach ($rig->pumps as $pump) {
                foreach ($pump->assemblies as $assembly) {
                    foreach ($assembly->components as $component) {
                        $hours  = $component->componentHours->first()?->hours_accumulated
                                  ?? $component->installed_at_hours;
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

            $alertasCables = [];
            $cable  = $rig->cables->first();
            $tmMax  = $rig->tm_max_corte ?? 1200;
            $alerta = $rig->tm_alerta_pct ?? 80;
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
                $sinAlertas++;
                $this->line("  {$rig->name}: sin alertas");
                continue;
            }

            $destinatarios = User::where(
                fn($q) => $q->where('rig_id', $rig->id)->orWhere('role', 'admin')
            )->where('active', true)->whereNotNull('email')->pluck('email')->unique();

            foreach ($destinatarios as $email) {
                Mail::to($email)->send(new AlertasCriticas($alertasBombas, $alertasCables, $rig->name));
                $enviados++;
            }

            $this->info("  {$rig->name}: " . count($alertasBombas) . " bomba(s), "
                . count($alertasCables) . " cable(s) → {$destinatarios->count()} correo(s)");
        }

        AuditService::log('envió', 'alerta',
            "Alerta diaria automática — {$enviados} correo(s) enviado(s) a " . $rigs->count() . " rig(s)");

        $this->newLine();
        if ($enviados > 0) {
            $this->info("✓ {$enviados} notificación(es) enviada(s).");
        } else {
            $this->line("Sin alertas activas — no se enviaron correos.");
        }

        return self::SUCCESS;
    }
}
