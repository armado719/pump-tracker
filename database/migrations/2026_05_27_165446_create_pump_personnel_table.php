<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pump_personnel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pump_id')->constrained('pumps')->cascadeOnDelete();
            $table->date('period_start');
            $table->string('rig_manager', 300)->nullable();
            $table->string('supervisor_day', 300)->nullable();
            $table->string('supervisor_night', 300)->nullable();
            $table->string('encuellador_day', 200)->nullable();
            $table->string('encuellador_night', 200)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pump_personnel'); }
};
