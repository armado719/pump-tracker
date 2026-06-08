<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assembly_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assembly_id')->constrained('pump_assemblies')->cascadeOnDelete();
            $table->enum('type', ['camisa','piston','suction_module','suction_valve','suction_seat','discharge_module','discharge_valve','discharge_seat']);
            $table->string('serial', 50)->nullable();
            $table->float('installed_at_hours')->default(0);
            $table->float('alert_threshold_warning')->nullable();
            $table->float('alert_threshold_critical')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('assembly_components'); }
};
