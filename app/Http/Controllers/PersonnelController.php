<?php

namespace App\Http\Controllers;

use App\Models\Pump;
use App\Models\PumpPersonnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function create(Pump $pump)
    {
        $current = $pump->currentPersonnel();
        return view('pumps.personnel', compact('pump', 'current'));
    }

    public function store(Request $request, Pump $pump)
    {
        $data = $request->validate([
            'period_start'      => 'required|date',
            'rig_manager'       => 'nullable|string|max:300',
            'supervisor_day'    => 'nullable|string|max:300',
            'supervisor_night'  => 'nullable|string|max:300',
            'encuellador_day'   => 'nullable|string|max:200',
            'encuellador_night' => 'nullable|string|max:200',
        ]);

        $pump->personnel()->create($data);

        return redirect()->route('pumps.show', $pump)
            ->with('success', 'Personal actualizado correctamente.');
    }
}
