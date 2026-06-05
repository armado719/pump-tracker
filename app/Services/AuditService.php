<?php

namespace App\Services;

use App\Models\Audit;

class AuditService
{
    public static function log(string $action, string $module, string $description): void
    {
        try {
            Audit::create([
                'user_id'     => auth()->id(),
                'user_name'   => auth()->user()?->name ?? 'Sistema',
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'created_at'  => now(),
            ]);
        } catch (\Throwable) {
            // No interrumpir el flujo si falla el registro de auditoría
        }
    }
}
