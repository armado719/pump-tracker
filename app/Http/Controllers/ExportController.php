<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\Cable;
use App\Models\DailyLog;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function pumpHours(Request $request)
    {
        $data = $request->validate([
            'pump_id' => 'required|exists:pumps,id',
            'month'   => 'required|date_format:Y-m',
        ]);

        $pump = Pump::with('rig')->findOrFail($data['pump_id']);
        [$year, $month] = explode('-', $data['month']);

        $logs = DailyLog::where('pump_id', $pump->id)
            ->whereYear('log_date', $year)
            ->whereMonth('log_date', $month)
            ->with('well')
            ->orderBy('day_number')
            ->get();

        $filename = "HorasBomba_{$pump->rig->name}_Bomba{$pump->number}_{$year}-{$month}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($pump, $logs, $year, $month) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 para que Excel lo abra correctamente
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['RIG', 'Bomba', 'Marca', 'Modelo', 'Mes', 'Día#', 'Fecha',
                           'Horas día', 'Horas acum.', 'Pozo', 'Presión amort. (PSI)', 'Comentarios'], ';');

            foreach ($logs as $log) {
                fputcsv($out, [
                    $pump->rig->name,
                    "Bomba #{$pump->number}",
                    $pump->brand,
                    $pump->model,
                    "{$year}-{$month}",
                    $log->day_number,
                    $log->log_date->format('d/m/Y'),
                    number_format($log->hours_worked, 2),
                    number_format($log->accumulated_hours, 2),
                    $log->well?->name ?? '—',
                    $log->dampener_pressure ?? '',
                    $log->comments ?? '',
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cableOperaciones(Request $request)
    {
        $data = $request->validate(['cable_id' => 'required|exists:cables,id']);

        $cable = Cable::with(['rig', 'operaciones' => fn($q) => $q->orderBy('fecha')->orderBy('id')])->findOrFail($data['cable_id']);

        $filename = "TM_{$cable->rig->name}_{$cable->serial}_" . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cable) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'RIG', 'Serial Cable', 'Grado', 'Diámetro', 'Fecha Instalación',
                'Fecha', 'COD', 'Tipo de Operación',
                'Prof. Ini (ft)', 'Prof. Fin (ft)', 'Densidad (ppg)',
                'Peso DP (lb/ft)', 'Peso BHA (lb/ft)', 'Long. BHA (ft)',
                'Peso Bloque (lb)', 'Factor Op.', 'TM Operación', 'TM Acumulado',
                'Pies cortados', 'Notas',
            ], ';');

            foreach ($cable->operaciones as $op) {
                fputcsv($out, [
                    $cable->rig->name,
                    $cable->serial,
                    $cable->grado,
                    $cable->diametro_in ?? '—',
                    $cable->fecha_instalacion->format('d/m/Y'),
                    $op->fecha->format('d/m/Y'),
                    $op->cod,
                    $op->tipo_operacion,
                    $op->prof_inicial_ft,
                    $op->prof_final_ft,
                    $op->densidad_lodo_ppg,
                    $op->peso_dp_lb_ft,
                    $op->peso_bha_lb_ft,
                    $op->long_bha_ft,
                    $op->peso_bloque_lb,
                    $op->factor_operacion,
                    $op->tm_operacion,
                    $op->tm_acumulado,
                    $op->ft_cortados ?? '',
                    $op->notas ?? '',
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
