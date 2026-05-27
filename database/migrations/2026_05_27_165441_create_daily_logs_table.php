<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pump_id')->constrained('pumps')->cascadeOnDelete();
            $table->foreignId('well_id')->nullable()->constrained('wells')->nullOnDelete();
            $table->date('log_date');
            $table->integer('day_number')->nullable();
            $table->float('hours_worked')->default(0);
            $table->float('accumulated_hours')->default(0);
            $table->integer('dampener_pressure')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('synced')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('daily_logs'); }
};
