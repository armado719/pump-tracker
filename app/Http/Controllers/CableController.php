<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\Cable;
use App\Services\TmCalculatorService;
use Illuminate\Http\Request;

class CableController extends Controller
{
    private function rig(): Rig
    {
        // En v1 usamos el primer rig disponible para el usuario
        $user = auth()->user();
        if ($user->rig_id) {
            return Rig::findOrFail($user->rig_id);
        }
        return Rig::first() ?? abort(404, 'No hay rigs configurados.');
    }

    public function dashboard()
    {
        $rig    = $this->rig();
        $cable  = $rig->cableActivo();
        $tmMax  = $rig->tm_max_corte ?? 1200;
        $alerta = $rig->tm_alerta_pct ?? 80;

        $tmAcum = 0;
        $tmPct  = 0;
        $gauge  = TmCalculatorService::gaugeArc(0);
        $ultimas = collect();

        if ($cable) {
            $tmAcum  = $cable->tmAcumulado();
            $tmPct   = $tmMax > 0 ? round($tmAcum / $tmMax * 100, 1) : 0;
            $gauge   = TmCalculatorService::gaugeArc($tmPct);
            $ultimas = $cable->operaciones()->latest('fecha')->latest()->take(5)->get();
        }

        $cables = $rig->cables()->withCount('operaciones')->orderByDesc('activo')->orderByDesc('fecha_instalacion')->get();

        return view('cable.dashboard', compact(
            'rig','cable','tmAcum','tmMax','tmPct','gauge','alerta','ultimas','cables'
        ));
    }

    public function historial()
    {
        $rig    = $this->rig();
        $cables = $rig->cables()
            ->withCount('operaciones')
            ->withCount('cortes')
            ->orderByDesc('activo')
            ->orderByDesc('fecha_instalacion')
            ->get()
            ->map(function($c) {
                $c->tm_total = $c->tmAcumulado();
                return $c;
            });

        return view('cable.historial', compact('rig', 'cables'));
    }

    public function activar(Cable $cable)
    {
        $rig = $this->rig();
        // Desactivar todos los cables del rig
        $rig->cables()->update(['activo' => false]);
        $cable->update(['activo' => true]);
        return back()->with('success', "Cable #{$cable->serial} activado.");
    }

    public function configuracion()
    {
        $rig = $this->rig();
        return view('cable.configuracion', compact('rig'));
    }

    public function updateConfiguracion(Request $request)
    {
        $rig  = $this->rig();
        $data = $request->validate([
            'altura_torre_ft'       => 'required|numeric|min:0',
            'diametro_tambor_in'    => 'required|numeric|min:0',
            'lineas_activas'        => 'required|integer|min:1|max:20',
            'tm_max_corte'          => 'required|numeric|min:1',
            'tipo_cable'            => 'required|string|max:10',
            'peso_bloque_default_lb'=> 'required|numeric|min:0',
            'tm_alerta_pct'         => 'required|numeric|min:1|max:100',
        ]);

        $rig->update($data);
        return back()->with('success', 'Configuración de Cable TM guardada.');
    }

    public function registrarCable(Request $request)
    {
        $rig  = $this->rig();
        $data = $request->validate([
            'serial'             => 'required|string|max:60',
            'referencia'         => 'nullable|string|max:100',
            'fabricante'         => 'nullable|string|max:80',
            'grado'              => 'required|in:EIP,EEIP,IPS',
            'diametro_in'        => 'nullable|string|max:20',
            'resistencia_lb'     => 'nullable|numeric|min:0',
            'fecha_instalacion'  => 'required|date',
            'longitud_inicial_ft'=> 'nullable|numeric|min:0',
        ]);

        // Desactivar cables anteriores
        $rig->cables()->update(['activo' => false]);

        $cable = $rig->cables()->create(array_merge($data, ['activo' => true]));

        return redirect()->route('cable.dashboard')
            ->with('success', "Cable {$cable->serial} registrado y activado.");
    }
}
