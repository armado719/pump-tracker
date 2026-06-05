<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rig_id')->constrained()->cascadeOnDelete();
            $table->string('serial');
            $table->string('referencia')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('grado')->default('EIP');
            $table->date('fecha_instalacion');
            $table->float('longitud_inicial_ft')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cables'); }
};
