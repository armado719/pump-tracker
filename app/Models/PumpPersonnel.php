<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PumpPersonnel extends Model {
    protected $fillable = [
        'pump_id','period_start','rig_manager',
        'supervisor_day','supervisor_night',
        'encuellador_day','encuellador_night'
    ];
    protected $casts = ['period_start' => 'date'];

    public function pump(): BelongsTo { return $this->belongsTo(Pump::class); }
}
