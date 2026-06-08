<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('component_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_log_id')->constrained('daily_logs')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('assembly_components')->cascadeOnDelete();
            $table->float('hours_accumulated')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('component_hours'); }
};
