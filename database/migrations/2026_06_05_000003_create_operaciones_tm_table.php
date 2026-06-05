<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operaciones_tm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cable_id')->constrained('cables')->cascadeOnDelete();
            $table->foreignId('rig_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->string('cod', 5);
            $table->string('tipo_operacion');
            $table->text('descripcion')->nullable();
            $table->float('prof_inicial_ft')->default(0);
            $table->float('prof_final_ft')->default(0);
            $table->float('densidad_lodo_ppg')->default(10);
            $table->float('peso_dp_lb_ft')->default(0);
            $table->float('peso_bha_lb_ft')->default(0);
            $table->float('long_bha_ft')->default(0);
            $table->float('peso_bloque_lb')->default(25000);
            $table->float('long_parada_ft')->default(0);
            $table->float('factor_operacion')->default(1);
            $table->float('tm_operacion')->default(0);
            $table->float('tm_acumulado')->default(0);
            $table->float('ft_cortados')->nullable();
            $table->text('notas')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('operaciones_tm'); }
};
