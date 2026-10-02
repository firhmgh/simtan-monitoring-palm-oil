<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed application's database for demonstration.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            ['name' => 'superadmin', 'description' => 'System Owner - Akses penuh seluruh sistem dan manajemen akun.'],
            ['name' => 'admin', 'description' => 'Data Controller - Akses manajemen data monitoring dan import berkas.'],
            ['name' => 'user', 'description' => 'Decision Maker - Akses visualisasi dashboard dan ekspor laporan.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        // Ambil data Role
        $superadminRole = Role::where('name', 'superadmin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // 2. Seed Superadmin (System Owner)
        User::updateOrCreate(
            ['email' => 'demo.superadmin@simtan.test'],
            [
                'role_id'  => $superadminRole->id,
                'name'     => 'Demo Superadmin',
                'username' => 'demo.superadmin',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Seed Admin (Data Controller)
        User::updateOrCreate(
            ['email' => 'demo.admin@simtan.test'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Demo Admin Pemetaan',
                'username' => 'demo.admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 4. Seed User (Decision Maker)
        User::updateOrCreate(
            ['email' => 'demo.user@simtan.test'],
            [
                'role_id'  => $userRole->id,
                'name'     => 'Demo User Manajemen',
                'username' => 'demo.user',
                'password' => Hash::make('password123'),
            ]
        );

        // 5. Seed Default AI Engine Configuration (Failsafe & Database-Driven)
        \Illuminate\Support\Facades\DB::table('ai_configs')->updateOrInsert(
            ['id' => 1],
            [
                'provider_primary' => 'gemini',
                'key_primary'      => env('GEMINI_API_KEY', config('services.gemini.key')),
                'provider_backup'  => 'groq',
                'key_backup'       => env('GROQ_API_KEY', config('services.groq.key')),
                'threshold_yellow' => 85,
                'threshold_red'    => 75,
                'updated_at'       => now(),
            ]
        );
    }
}
