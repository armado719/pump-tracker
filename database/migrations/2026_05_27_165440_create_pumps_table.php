<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pumps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rig_id')->constrained('rigs')->cascadeOnDelete();
            $table->integer('number');
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('serial', 50)->nullable();
            $table->string('liner_diameter', 20)->nullable();
            $table->string('active_fixed_id', 30)->nullable();
            $table->float('base_accumulated_hours')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pumps'); }
};
