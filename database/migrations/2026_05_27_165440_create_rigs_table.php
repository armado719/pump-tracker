<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rigs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('location', 100)->nullable();
            $table->string('manager', 200)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('rigs'); }
};
