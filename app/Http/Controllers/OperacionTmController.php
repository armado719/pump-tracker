<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\Cable;
use App\Models\OperacionTm;
use App\Models\CorteCable;
use App\Services\TmCalculatorService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class OperacionTmController extends Controller
{
    private function cableActivo(): Cable
    {
        $user = auth()->user();
        $rig  = $user->rig_id ? Rig::findOrFail($user->rig_id) : Rig::first();
        $cable = $rig?->cableActivo();

        if (!$cable) {
            redirect()->route('cable.dashboard')
                ->with('warning', 'Primero registra un cable activo antes de ingresar operaciones.')
                ->send();
            exit;
        }
        return $cable;
    }

    public function index()
    {
        $cable = $this->cableActivo();
        $query = $cable->operaciones()->orderByDesc('fecha')->orderByDesc('id');

        if (request()->filled('desde')) {
            $query->whereDate('fecha', '>=', request('desde'));
        }
        if (request()->filled('hasta')) {
            $query->whereDate('fecha', '<=', request('hasta'));
        }
        if (request()->filled('cod')) {
            $query->where('cod', request('cod'));
        }

        $operaciones = $query->paginate(20)->withQueryString();
        $tiposCod    = TmCalculatorService::OPERACIONES;

        return view('cable.operaciones.index', compact('cable', 'operaciones', 'tiposCod'));
    }

    public function create()
    {
        $cable   = $this->cableActivo();
        $rig     = $cable->rig;
        $ultima  = $cable->ultimaOperacion();
        $tmAcum  = $cable->tmAcumulado();
        $operaciones   = TmCalculatorService::OPERACIONES;
        $operacionesEn = TmCalculatorService::OPERACIONES_EN;
        return view('cable.operaciones.create', compact('cable','rig','ultima','tmAcum','operaciones','operacionesEn'));
    }

    public function store(Request $request)
    {
        $cable = $this->cableActivo();
        $rig   = $cable->rig;

        $data = $request->validate([
            'fecha'              => 'required|date',
            'cod'                => 'required|string',
            'descripcion'        => 'nullable|string|max:500',
            'prof_inicial_ft'    => 'required|numeric|min:0',
            'prof_final_ft'      => 'required|numeric|min:0',
            'densidad_lodo_ppg'  => 'required|numeric|min:1',
            'peso_dp_lb_ft'      => 'required|numeric|min:0',
            'peso_bha_lb_ft'     => 'required|numeric|min:0',
            'long_bha_ft'        => 'required|numeric|min:0',
            'peso_bloque_lb'     => 'required|numeric|min:0',
            'long_parada_ft'     => 'nullable|numeric|min:0',
            'ft_cortados'        => 'nullable|numeric|min:0',
            'notas'              => 'nullable|string|max:500',
        ]);

        $result = TmCalculatorService::calcular(array_merge($data, [
            'lineas_activas' => $rig->lineas_activas ?? 8,
        ]));

        $tmAcumAnterior = $cable->tmAcumulado();
        $tmAcumNuevo    = $tmAcumAnterior + $result['tm'];

        $operacion = $cable->operaciones()->create([
            'rig_id'            => $rig->id,
            'fecha'             => $data['fecha'],
            'cod'               => $data['cod'],
            'tipo_operacion'    => TmCalculatorService::OPERACIONES[$data['cod']] ?? $data['cod'],
            'descripcion'       => $data['descripcion'] ?? null,
            'prof_inicial_ft'   => $data['prof_inicial_ft'],
            'prof_final_ft'     => $data['prof_final_ft'],
            'densidad_lodo_ppg' => $data['densidad_lodo_ppg'],
            'peso_dp_lb_ft'     => $data['peso_dp_lb_ft'],
            'peso_bha_lb_ft'    => $data['peso_bha_lb_ft'],
            'long_bha_ft'       => $data['long_bha_ft'],
            'peso_bloque_lb'    => $data['peso_bloque_lb'],
            'long_parada_ft'    => $data['long_parada_ft'] ?? 0,
            'factor_operacion'  => $result['factor_op'],
            'tm_operacion'      => $result['tm'],
            'tm_acumulado'      => round($tmAcumNuevo, 4),
            'ft_cortados'       => $data['ft_cortados'] ?? null,
            'notas'             => $data['notas'] ?? null,
            'created_by'        => auth()->user()->name,
        ]);

        // COD 14 → registrar corte y archivar cable
        if ($data['cod'] === '14') {
            CorteCable::create([
                'cable_id'    => $cable->id,
                'operacion_id'=> $operacion->id,
                'fecha'       => $data['fecha'],
                'ft_cortados' => $data['ft_cortados'] ?? 0,
                'tm_al_corte' => $tmAcumNuevo,
                'motivo'      => $data['notas'] ?? null,
            ]);

            $cable->update(['activo' => false]);

            AuditService::log('registró', 'operacion',
                "COD 14 — Corte cable {$cable->serial} ({$rig->name}) — TM al corte: " . round($tmAcumNuevo, 2));
            return redirect()->route('cable.dashboard')
                ->with('warning', "Corte registrado. Cable {$cable->serial} archivado. TM al corte: " . round($tmAcumNuevo, 2) . ' TM. Registra el nuevo cable.');
        }

        $tmMax    = $rig->tm_max_corte ?? 1200;
        $alertPct = $rig->tm_alerta_pct ?? 80;
        $pct      = $tmMax > 0 ? $tmAcumNuevo / $tmMax * 100 : 0;

        $msg = "Operación registrada. TM esta operación: {$result['tm']} | Acumulado: " . round($tmAcumNuevo, 2) . " / {$tmMax} TM";

        if ($pct >= 95) {
            return redirect()->route('cable.dashboard')->with('error', "⚠ CRÍTICO: {$msg}");
        } elseif ($pct >= $alertPct) {
            return redirect()->route('cable.dashboard')->with('warning', "⚠ ALERTA: {$msg}");
        }

        AuditService::log('registró', 'operacion',
            "COD {$data['cod']} — Cable {$cable->serial} ({$rig->name}) — TM: {$result['tm']} | Acum: " . round($tmAcumNuevo, 2));
        return redirect()->route('cable.dashboard')->with('success', $msg);
    }
}
