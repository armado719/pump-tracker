<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()?->role !== 'admin') {
            return redirect()->back()->with('error', 'Solo administradores pueden realizar esta acción.');
        }
        return $next($request);
    }
}
