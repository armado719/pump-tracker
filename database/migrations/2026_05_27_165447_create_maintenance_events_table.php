<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maintenance_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_log_id')->nullable()->constrained('daily_logs')->nullOnDelete();
            $table->foreignId('component_id')->constrained('assembly_components')->cascadeOnDelete();
            $table->string('event_type', 50)->default('replacement');
            $table->float('hours_before')->nullable();
            $table->string('new_serial', 50)->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('maintenance_events'); }
};
