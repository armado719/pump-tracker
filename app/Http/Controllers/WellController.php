<?php

namespace App\Http\Controllers;

use App\Models\Rig;
use App\Models\Well;
use Illuminate\Http\Request;

class WellController extends Controller
{
    public function store(Request $request, Rig $rig)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $rig->wells()->create($data);

        return redirect()->route('rigs.show', $rig)
            ->with('success', "Pozo '{$data['name']}' agregado al {$rig->name}.");
    }

    public function destroy(Rig $rig, Well $well)
    {
        $well->delete();
        return redirect()->route('rigs.show', $rig)
            ->with('success', 'Pozo eliminado.');
    }
}
