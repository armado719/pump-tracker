<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('role');
        });

        // Cambiar enum para quitar encuellador
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','rig_manager','supervisor') NOT NULL DEFAULT 'supervisor'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','rig_manager','supervisor','encuellador') NOT NULL DEFAULT 'encuellador'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
