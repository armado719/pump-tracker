<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@pumptracker.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Datos reales del RIG158
        $this->call(RIG158Seeder::class);
    }
}
