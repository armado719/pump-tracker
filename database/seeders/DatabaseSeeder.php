<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $verified = now();

        // Admin GRS
        User::updateOrCreate(
            ['email' => 'admin@grs.com'],
            [
                'name'              => 'Administrador GRS',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'active'            => true,
                'email_verified_at' => $verified,
            ]
        );

        // Rig Manager
        User::updateOrCreate(
            ['email' => 'manager@grs.com'],
            [
                'name'              => 'Rig Manager GRS',
                'password'          => Hash::make('grs2026'),
                'role'              => 'rig_manager',
                'active'            => true,
                'email_verified_at' => $verified,
            ]
        );

        // Supervisor
        User::updateOrCreate(
            ['email' => 'supervisor@grs.com'],
            [
                'name'              => 'Supervisor GRS',
                'password'          => Hash::make('grs2026'),
                'role'              => 'supervisor',
                'active'            => true,
                'email_verified_at' => $verified,
            ]
        );

        // Datos reales del RIG158
        $this->call(RIG158Seeder::class);
    }
}
