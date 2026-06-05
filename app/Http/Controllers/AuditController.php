<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::orderByDesc('created_at');

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $audits = $query->paginate(50)->withQueryString();

        // Lista fija de módulos conocidos + los que ya existan en BD
        $knownModules = ['rig', 'bomba', 'componente', 'log-diario', 'cable', 'operacion', 'pozo', 'usuario', 'configuración'];
        $dbModules    = Audit::distinct()->orderBy('module')->pluck('module')->toArray();
        $modules      = collect(array_unique(array_merge($knownModules, $dbModules)))->sort()->values();

        // Lista de usuarios para el filtro (dropdown)
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('audit.index', compact('audits', 'modules', 'users'));
    }
}
