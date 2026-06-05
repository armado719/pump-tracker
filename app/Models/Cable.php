<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cable extends Model
{
    protected $fillable = [
        'rig_id','serial','referencia','fabricante','grado',
        'fecha_instalacion','longitud_inicial_ft','activo',
    ];

    protected function casts(): array
    {
        return ['fecha_instalacion' => 'date', 'activo' => 'boolean'];
    }

    public function rig(): BelongsTo { return $this->belongsTo(Rig::class); }
    public function operaciones(): HasMany { return $this->hasMany(OperacionTm::class); }
    public function cortes(): HasMany { return $this->hasMany(CorteCable::class); }

    public function tmAcumulado(): float
    {
        return (float) $this->operaciones()->sum('tm_operacion');
    }

    public function ultimaOperacion(): ?OperacionTm
    {
        return $this->operaciones()->latest('fecha')->latest()->first();
    }
}
