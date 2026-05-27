<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PumpAssembly extends Model {
    protected $fillable = ['pump_id', 'position'];

    public function pump(): BelongsTo { return $this->belongsTo(Pump::class); }
    public function components(): HasMany { return $this->hasMany(AssemblyComponent::class, 'assembly_id'); }

    public function getPositionLabelAttribute(): string {
        return match($this->position) {
            'left'   => 'Izquierdo',
            'middle' => 'Medio',
            'right'  => 'Derecho',
            default  => $this->position,
        };
    }
}
