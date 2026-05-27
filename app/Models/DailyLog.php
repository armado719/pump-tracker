<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyLog extends Model {
    protected $fillable = [
        'pump_id','well_id','log_date','day_number',
        'hours_worked','accumulated_hours','dampener_pressure',
        'comments','synced'
    ];
    protected $casts = ['log_date' => 'date', 'synced' => 'boolean'];

    public function pump(): BelongsTo { return $this->belongsTo(Pump::class); }
    public function well(): BelongsTo { return $this->belongsTo(Well::class); }
    public function componentHours(): HasMany { return $this->hasMany(ComponentHour::class); }
    public function maintenanceEvents(): HasMany { return $this->hasMany(MaintenanceEvent::class); }
}
