<?php

namespace App\Http\Controllers;

use App\Models\Audit;
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

        $audits  = $query->paginate(50)->withQueryString();
        $modules = Audit::distinct()->orderBy('module')->pluck('module');

        return view('audit.index', compact('audits', 'modules'));
    }
}
