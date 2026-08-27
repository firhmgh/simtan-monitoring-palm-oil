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
            ['email' => 'superadmin.regional1@ptpn4.co.id'],
            [
                'role_id'  => $superadminRole->id,
                'name'     => 'Maghfirah',
                'username' => 'superadmin.regional1',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Seed Admin (Data Controller)
        User::updateOrCreate(
            ['email' => 'admin.regional1@ptpn4.co.id'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Asisten Investasi Pemetaan',
                'username' => 'admin.regional1',
                'password' => Hash::make('password123'),
            ]
        );

        // 4. Seed User (Decision Maker)
        User::updateOrCreate(
            ['email' => 'user.regional1@ptpn4.co.id'],
            [
                'role_id'  => $userRole->id,
                'name'     => 'Pimpinan Manajemen',
                'username' => 'user.regional1',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
