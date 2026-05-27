<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssemblyComponent extends Model {
    protected $fillable = [
        'assembly_id','type','serial','installed_at_hours',
        'alert_threshold_warning','alert_threshold_critical'
    ];

    public function assembly(): BelongsTo { return $this->belongsTo(PumpAssembly::class, 'assembly_id'); }
    public function componentHours(): HasMany { return $this->hasMany(ComponentHour::class, 'component_id'); }
    public function maintenanceEvents(): HasMany { return $this->hasMany(MaintenanceEvent::class, 'component_id'); }

    public function getTypeLabelAttribute(): string {
        return match($this->type) {
            'camisa'           => 'Camisa',
            'piston'           => 'Pistón',
            'suction_module'   => 'Módulo Succión',
            'suction_valve'    => 'Válvula Succión',
            'suction_seat'     => 'Asiento Succión',
            'discharge_module' => 'Módulo Descarga',
            'discharge_valve'  => 'Válvula Descarga',
            'discharge_seat'   => 'Asiento Descarga',
            default            => $this->type,
        };
    }

    public function getSectionLabelAttribute(): string {
        return match($this->type) {
            'camisa', 'piston' => '—',
            'suction_module', 'suction_valve', 'suction_seat' => 'Módulo Succión',
            'discharge_module', 'discharge_valve', 'discharge_seat' => 'Módulo Descarga',
            default => '—',
        };
    }

    public function latestHours(int $logId = null): float {
        if ($logId) {
            $ch = $this->componentHours()->where('daily_log_id', $logId)->first();
            if ($ch) return $ch->hours_accumulated;
        }
        $ch = $this->componentHours()->orderByDesc('id')->first();
        return $ch ? $ch->hours_accumulated : $this->installed_at_hours;
    }

    public function getStatus(float $hours): string {
        if ($this->alert_threshold_critical && $hours >= $this->alert_threshold_critical) return 'critical';
        if ($this->alert_threshold_warning && $hours >= $this->alert_threshold_warning) return 'warning';
        return 'ok';
    }
}
