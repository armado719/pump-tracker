<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pump_assemblies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pump_id')->constrained('pumps')->cascadeOnDelete();
            $table->enum('position', ['left', 'middle', 'right']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pump_assemblies'); }
};
