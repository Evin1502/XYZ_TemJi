<?php

namespace Database\Seeders;

use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WargaSeeder extends Seeder
{
    public function run(): void
    {
        $sukapura  = Wilayah::where('nama', 'Sukapura')->value('id');
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->value('id');

        // Contoh data; hapus/ganti dengan data asli. Null = belum berwilayah (perlu dilengkapi admin).
        $data = [
            ['3204010000000001', 'Contoh Warga 1', $sukapura],
            ['3204010000000002', 'Contoh Warga 2', $sukabirus],
            ['3204010000000003', 'Contoh Warga 3', null],
        ];

        foreach ($data as [$kk, $nama, $wilayahId]) {
            Warga::updateOrCreate(['no_kk' => $kk], ['nama_kepala_keluarga' => $nama, 'wilayah_id' => $wilayahId]);
        }
    }
}
