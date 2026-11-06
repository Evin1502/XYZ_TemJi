<?php

namespace Database\Seeders;

use App\Models\KategoriDaurUlang;
use App\Models\PengajuanDaurUlang;
use App\Models\PengajuanDaurUlangItem;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PengajuanDaurUlangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sukapura = Wilayah::where('nama', 'Sukapura')->first();

        // 1. Akun Warga Contoh (Ibu Ratna)
        $warga = User::updateOrCreate(
            ['email' => 'warga@temji.test'],
            [
                'name' => 'Ratna Warga',
                'password' => Hash::make('password'),
                'role' => 'warga',
                'wilayah_id' => $sukapura?->id,
            ]
        );

        // 2. Kategori Daur Ulang
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $petBening = KategoriDaurUlang::where('nama', 'Botol PET bening')->first();

        if (!$minyak || !$petBening) {
            return;
        }

        // 3. Pengajuan Daur Ulang Contoh Sesuai Mockup UI (Kode DU-2610-0214)
        $pengajuan = PengajuanDaurUlang::updateOrCreate(
            ['kode_pengajuan' => 'DU-2610-0214'],
            [
                'user_id' => $warga->id,
                'wilayah_id' => $sukapura?->id,
                'total_estimasi_kg' => 5,
                'total_estimasi_poin' => (2 * 10) + (3 * 8),
                'status' => PengajuanDaurUlang::STATUS_MENUNGGU_JADWAL,
                'catatan' => 'Pastikan sudah dipisah per kategori',
            ]
        );

        // Item 1: Minyak jelantah ± 2 kg
        PengajuanDaurUlangItem::updateOrCreate(
            [
                'pengajuan_id' => $pengajuan->id,
                'kategori_id' => $minyak->id,
            ],
            [
                'estimasi_berat_kg' => 2,
                'poin_per_kg_snapshot' => 10,
                'estimasi_poin' => 20,
            ]
        );

        // Item 2: Botol PET bening ± 3 kg
        PengajuanDaurUlangItem::updateOrCreate(
            [
                'pengajuan_id' => $pengajuan->id,
                'kategori_id' => $petBening->id,
            ],
            [
                'estimasi_berat_kg' => 3,
                'poin_per_kg_snapshot' => 8,
                'estimasi_poin' => 24,
            ]
        );
    }
}
