<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Pengelola / Admin
        User::updateOrCreate(
            ['email' => 'admin@temji.id'],
            [
                'name' => 'Admin TemJi',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Akun Petugas Lapangan Penjemputan Sampah
        User::updateOrCreate(
            ['email' => 'petugas@temji.id'],
            [
                'name' => 'Petugas Penjemputan',
                'role' => User::ROLE_PETUGAS,
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Akun Warga Terdaftar
        User::updateOrCreate(
            ['email' => 'warga@temji.id'],
            [
                'name' => 'Warga Sukapura',
                'role' => User::ROLE_WARGA,
                'password' => Hash::make('password123'),
            ]
        );
    }
}
