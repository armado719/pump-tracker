<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rig_id')->constrained('rigs')->cascadeOnDelete();
            $table->string('name', 50);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('wells'); }
};
