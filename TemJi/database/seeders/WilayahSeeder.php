<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // Jumlah KK: GANTI dengan data asli. Nilai awal 0 dipertahankan karena VerifikasiWargaTest (Farhan) mengharapkan 0.
        foreach (['Sukapura' => 0, 'Sukabirus' => 0] as $nama => $kk) {
            Wilayah::updateOrCreate(
                ['nama' => $nama],
                ['jumlah_kk' => $kk, 'is_aktif' => true]
            );
        }
    }
}
