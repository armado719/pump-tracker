<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\Cable;
use App\Models\DailyLog;
use App\Services\ThresholdService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $pumps = ($user->role === 'admin' || !$user->rig_id)
            ? Pump::with('rig')->get()
            : Pump::whereHas('rig', fn($q) => $q->where('id', $user->rig_id))->with('rig')->get();

        $cables = ($user->role === 'admin' || !$user->rig_id)
            ? Cable::with('rig')->orderByDesc('activo')->orderBy('rig_id')->get()
            : Cable::whereHas('rig', fn($q) => $q->where('id', $user->rig_id))->with('rig')->orderByDesc('activo')->get();

        return view('reports.index', compact('pumps', 'cables'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'pump_id' => 'required|exists:pumps,id',
            'month'   => 'required|date_format:Y-m',
        ]);

        $pump = Pump::with([
            'rig',
            'assemblies' => fn($q) => $q->orderByRaw("FIELD(position,'left','middle','right')"),
            'assemblies.components' => fn($q) => $q->orderByRaw(
                "FIELD(type,'camisa','piston','suction_module','suction_valve','suction_seat','discharge_module','discharge_valve','discharge_seat')"
            ),
        ])->findOrFail($data['pump_id']);

        [$year, $month] = explode('-', $data['month']);

        $logs = DailyLog::where('pump_id', $pump->id)
            ->whereYear('log_date', $year)
            ->whereMonth('log_date', $month)
            ->with('componentHours.component', 'well', 'maintenanceEvents.component')
            ->orderBy('day_number')
            ->get();

        $personnel = $pump->personnel()
            ->where('period_start', '<=', "{$year}-{$month}-01")
            ->orderByDesc('period_start')
            ->first();

        $tableRows = $logs->map(function($log) use ($pump) {
            $row = [
                'day'        => $log->day_number,
                'date'       => $log->log_date->format('d/m/Y'),
                'hours'      => $log->hours_worked,
                'accum'      => $log->accumulated_hours,
                'dampener'   => $log->dampener_pressure,
                'comments'   => $log->comments,
                'components' => [],
            ];

            $chByComp = $log->componentHours->keyBy('component_id');

            foreach ($pump->assemblies as $assembly) {
                foreach ($assembly->components as $comp) {
                    $hours  = $chByComp[$comp->id]?->hours_accumulated ?? '—';
                    $status = is_numeric($hours) ? ThresholdService::getStatus($comp->type, $hours) : 'ok';
                    $row['components'][$comp->id] = ['hours' => $hours, 'status' => $status];
                }
            }
            return $row;
        });

        $pdf = Pdf::loadView('reports.pdf', compact('pump', 'logs', 'tableRows', 'personnel', 'year', 'month'))
            ->setPaper('A3', 'landscape');

        $filename = "FGOP_{$pump->rig->name}_Bomba{$pump->number}_{$year}-{$month}.pdf";

        return $pdf->download($filename);
    }

    public function cableGenerate(Request $request)
    {
        $data = $request->validate(['cable_id' => 'required|exists:cables,id']);

        $cable = Cable::with([
            'rig',
            'operaciones' => fn($q) => $q->orderBy('fecha')->orderBy('id'),
            'cortes',
        ])->findOrFail($data['cable_id']);

        $tmAcum = round($cable->tmAcumulado(), 4);
        $tmMax  = $cable->rig->tm_max_corte ?? 1200;
        $tmPct  = $tmMax > 0 ? round($tmAcum / $tmMax * 100, 1) : 0;

        $pdf = Pdf::loadView('cable.reporte-pdf', compact('cable', 'tmAcum', 'tmMax', 'tmPct'))
            ->setPaper('A3', 'landscape');

        $filename = "TM_{$cable->rig->name}_{$cable->serial}_" . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
