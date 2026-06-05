<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rigs', function (Blueprint $table) {
            $table->float('altura_torre_ft')->default(105)->after('manager');
            $table->float('diametro_tambor_in')->default(18)->after('altura_torre_ft');
            $table->integer('lineas_activas')->default(8)->after('diametro_tambor_in');
            $table->float('tm_max_corte')->default(1200)->after('lineas_activas');
            $table->string('tipo_cable')->default('EIP')->after('tm_max_corte');
            $table->float('peso_bloque_default_lb')->default(25000)->after('tipo_cable');
            $table->float('tm_alerta_pct')->default(80)->after('peso_bloque_default_lb');
        });
    }

    public function down(): void
    {
        Schema::table('rigs', function (Blueprint $table) {
            $table->dropColumn([
                'altura_torre_ft','diametro_tambor_in','lineas_activas',
                'tm_max_corte','tipo_cable','peso_bloque_default_lb','tm_alerta_pct',
            ]);
        });
    }
};
