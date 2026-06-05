<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperacionTm extends Model
{
    protected $table = 'operaciones_tm';

    protected $fillable = [
        'cable_id','rig_id','fecha','cod','tipo_operacion','descripcion',
        'prof_inicial_ft','prof_final_ft','densidad_lodo_ppg',
        'peso_dp_lb_ft','peso_bha_lb_ft','long_bha_ft','peso_bloque_lb','long_parada_ft',
        'factor_operacion','tm_operacion','tm_acumulado',
        'ft_cortados','notas','created_by',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function cable(): BelongsTo { return $this->belongsTo(Cable::class); }
    public function rig(): BelongsTo   { return $this->belongsTo(Rig::class); }
}
