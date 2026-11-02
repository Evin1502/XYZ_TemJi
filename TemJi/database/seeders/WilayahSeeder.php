<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti jumlah_kk dengan data asli
        foreach (['Sukapura', 'Sukabirus'] as $nama) {
            Wilayah::updateOrCreate(
                ['nama' => $nama],
                ['jumlah_kk' => 0, 'is_aktif' => true]
            );
        }
    }
}