<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
public function run(): void
    {
        // 1. Seed Roles 
        $roles = [
            ['name' => 'superadmin', 'description' => 'Akses penuh seluruh sistem dan manajemen akun.'],
            ['name' => 'admin', 'description' => 'Akses manajemen data monitoring dan import berkas.'],
            ['name' => 'user', 'description' => 'Akses visualisasi dashboard dan ekspor laporan.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        // 2. Seed Initial Superadmin (Agar Bisa Login)
        $superAdminRole = Role::where('name', 'superadmin')->first();

        User::updateOrCreate(
            ['username' => 'gitaddpir'],
            [
                'role_id'  => $superAdminRole->id,
                'name'     => 'Maghfirah',
                'email'    => 'firahmagh485@gmail.com',
                'password' => Hash::make('password123'), // Ubah saat produksi
            ]
        );

        // 3. Seed Admin sesuai screenshot Bab 3
        $adminRole = Role::where('name', 'admin')->first();
        User::updateOrCreate(
            ['username' => 'admin_regional1'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Admin Regional 1',
                'email'    => 'admin.reg1@ptpn4.co.id',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
