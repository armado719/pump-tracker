<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pump extends Model {
    protected $fillable = [
        'rig_id','number','brand','model','serial',
        'liner_diameter','active_fixed_id','base_accumulated_hours'
    ];

    public function rig(): BelongsTo { return $this->belongsTo(Rig::class); }
    public function assemblies(): HasMany { return $this->hasMany(PumpAssembly::class); }
    public function dailyLogs(): HasMany { return $this->hasMany(DailyLog::class); }
    public function personnel(): HasMany { return $this->hasMany(PumpPersonnel::class); }

    public function currentPersonnel(): ?PumpPersonnel {
        return $this->personnel()->orderByDesc('period_start')->first();
    }

    public function latestLog(): ?DailyLog {
        return $this->dailyLogs()->orderByDesc('log_date')->first();
    }
}
