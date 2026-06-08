<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('rig_id')->nullable()->after('id');
            $table->foreign('rig_id')->references('id')->on('rigs')->nullOnDelete();
            $table->enum('role', ['admin', 'rig_manager', 'supervisor', 'encuellador'])
                  ->default('encuellador')->after('email');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rig_id']);
            $table->dropColumn(['rig_id', 'role']);
        });
    }
};
