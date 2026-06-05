<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorteCable extends Model
{
    protected $table = 'cortes_cable';

    protected $fillable = ['cable_id','operacion_id','fecha','ft_cortados','tm_al_corte','motivo'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function cable(): BelongsTo    { return $this->belongsTo(Cable::class); }
    public function operacion(): BelongsTo { return $this->belongsTo(OperacionTm::class); }
}
