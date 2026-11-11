<?php

namespace Database\Seeders;

use App\Models\JadwalPenjemputan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class JadwalPenjemputanSeeder extends Seeder
{
    public function run(): void
    {
        $wilayah = Wilayah::pluck('id', 'nama');
        $petugas = User::petugas()->pluck('id', 'name');

        // [jenis, wilayah, hari, mulai, selesai, kuota, petugas]
        $jadwal = [
            ['sampah_biasa', 'Sukapura', 1, '06:00', '08:00', null, 'Pak Asep'],
            ['sampah_biasa', 'Sukabirus', 2, '06:00', '08:00', null, 'Pak Ujang'],
            ['sampah_biasa', 'Sukapura', 3, '06:00', '08:00', null, 'Pak Asep'],
            ['sampah_biasa', 'Sukabirus', 4, '06:00', '08:00', null, 'Pak Ujang'],
            ['sampah_biasa', 'Sukapura', 5, '06:00', '08:00', null, 'Pak Asep'],
            ['sampah_biasa', 'Sukabirus', 6, '06:00', '08:00', null, 'Pak Ujang'],
            ['daur_ulang', 'Sukapura', 1, '09:00', '11:00', 20, 'Pak Dedi'],
            ['daur_ulang', 'Sukabirus', 3, '13:00', '15:00', 20, 'Pak Ujang'],
            ['daur_ulang', 'Sukabirus', 6, '08:00', '10:00', 20, 'Pak Ujang'],
        ];

        foreach ($jadwal as [$jenis, $namaWilayah, $hari, $mulai, $selesai, $kuota, $namaPetugas]) {
            JadwalPenjemputan::updateOrCreate(
                [
                    'wilayah_id' => $wilayah[$namaWilayah],
                    'jenis' => $jenis,
                    'hari' => $hari,
                    'jam_mulai' => JadwalPenjemputan::normalisasiJam($mulai),
                ],
                [
                    'jam_selesai' => $selesai,
                    'kuota' => $kuota,
                    'petugas_id' => $petugas[$namaPetugas],
                ]
            );
        }
    }
}
