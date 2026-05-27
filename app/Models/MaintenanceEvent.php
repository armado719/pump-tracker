<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceEvent extends Model
{
    protected $fillable = [
        'daily_log_id',
        'component_id',
        'event_type',
        'hours_before',
        'new_serial',
        'notes',
    ];

    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(AssemblyComponent::class, 'component_id');
    }
}
