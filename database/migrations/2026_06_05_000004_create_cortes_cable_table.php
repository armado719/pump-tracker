<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cortes_cable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cable_id')->constrained('cables')->cascadeOnDelete();
            $table->foreignId('operacion_id')->nullable()->constrained('operaciones_tm')->nullOnDelete();
            $table->date('fecha');
            $table->float('ft_cortados');
            $table->float('tm_al_corte');
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cortes_cable'); }
};
