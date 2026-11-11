<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting (FK). Kelas yang belum ada (belum di-merge) dilewati.
        $urutan = [
            WilayahSeeder::class,
            UserSeeder::class,                   // admin, petugas, warga contoh (Aisya)
            PetugasSeeder::class,                // Rifka
            KategoriDaurUlangSeeder::class,      // Farhan (kategori + riwayat tarif)
            WargaSeeder::class,                  // Farhan
            WargaBelumBerwilayahSeeder::class,   // Aisya: data uji PBI 1
            JadwalPenjemputanSeeder::class,      // Rifka
            PengajuanDaurUlangSeeder::class,     // Daffa
            IuranWilayahSeeder::class,           // Shafina
        ];

        $this->call(array_values(array_filter($urutan, 'class_exists')));
    }
}
