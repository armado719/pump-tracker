<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cables', function (Blueprint $table) {
            $table->string('diametro_in')->default('1 1/8"')->after('grado');
            $table->float('resistencia_lb')->nullable()->after('diametro_in');
        });
    }

    public function down(): void
    {
        Schema::table('cables', function (Blueprint $table) {
            $table->dropColumn(['diametro_in', 'resistencia_lb']);
        });
    }
};
