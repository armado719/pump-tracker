<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rig extends Model {
    protected $fillable = [
        'name','location','manager',
        'altura_torre_ft','diametro_tambor_in','lineas_activas',
        'tm_max_corte','tipo_cable','peso_bloque_default_lb','tm_alerta_pct',
    ];

    public function wells(): HasMany   { return $this->hasMany(Well::class); }
    public function pumps(): HasMany   { return $this->hasMany(Pump::class); }
    public function users(): HasMany   { return $this->hasMany(User::class); }
    public function cables(): HasMany  { return $this->hasMany(Cable::class); }

    public function cableActivo(): ?Cable
    {
        return $this->cables()->where('activo', true)->latest()->first();
    }
}
