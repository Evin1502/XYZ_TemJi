<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
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

        // 3. Akun Warga Terdaftar (beserta data warga agar halaman status tidak 404)
        $userWarga = User::updateOrCreate(
            ['email' => 'warga@temji.id'],
            [
                'name' => 'Warga Sukapura',
                'role' => User::ROLE_WARGA,
                'password' => Hash::make('password123'),
            ]
        );

        Warga::updateOrCreate(
            ['user_id' => $userWarga->id],
            [
                'nik' => '3273000000000001',
                'no_kk' => '3273000000000801',
                'nama_lengkap' => 'Warga Sukapura',
                'no_hp' => '081200000001',
                'alamat' => 'Jl. Contoh No. 1, Sukapura',
                'wilayah_id' => Wilayah::where('nama', 'Sukapura')->value('id'),
                'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
            ]
        );
    }
}