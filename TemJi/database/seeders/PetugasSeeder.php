<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $sukapura = Wilayah::where('nama', 'Sukapura')->value('id');
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->value('id');

        $akun = [
            ['name' => 'Admin TemJi', 'email' => 'admin@temji.test', 'role' => 'admin', 'wilayah_id' => null],
            ['name' => 'Pak Asep', 'email' => 'asep@temji.test', 'role' => 'petugas', 'wilayah_id' => $sukapura],
            ['name' => 'Pak Dedi', 'email' => 'dedi@temji.test', 'role' => 'petugas', 'wilayah_id' => $sukapura],
            ['name' => 'Pak Ujang', 'email' => 'ujang@temji.test', 'role' => 'petugas', 'wilayah_id' => $sukabirus],
        ];

        foreach ($akun as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data + ['password' => Hash::make('password')]
            );
        }
    }
}
