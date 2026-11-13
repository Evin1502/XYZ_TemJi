<?php

namespace Database\Seeders;

use App\Models\KategoriDaurUlang;
use App\Models\RiwayatPoin;
use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RiwayatPoinWargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sukapura = Wilayah::where('nama', 'Sukapura')->first();
        $admin = User::where('role', 'admin')->first();
        $minyakJelantah = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $petBening = KategoriDaurUlang::where('nama', 'Botol PET bening')->first();

        // 1. Akun Warga Utama Sesuai Mockup PBI 06: Dewi Lestari · Sukapura
        $userDewi = User::updateOrCreate(
            ['email' => 'dewi.lestari@temji.id'],
            [
                'name' => 'Dewi Lestari',
                'role' => 'warga',
                'password' => Hash::make('password123'),
            ]
        );

        $wargaDewi = Warga::updateOrCreate(
            ['nik' => '3204085501980005'],
            [
                'user_id' => $userDewi->id,
                'no_kk' => '3204085501180005',
                'nama_lengkap' => 'Dewi Lestari',
                'no_hp' => '081277776666',
                'alamat' => 'Jl. Sukapura No. 25, RT 02/RW 03, Desa Sukapura',
                'wilayah_id' => $sukapura?->id,
                'foto_identitas' => 'identitas_warga/sample_ktp_dewi.jpg',
                'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
                'catatan_verifikasi' => 'Terverifikasi warga resmi Sukapura.',
                'diverifikasi_oleh' => $admin?->id,
                'diverifikasi_pada' => Carbon::now()->subMonths(2),
                'saldo_poin' => 180,
            ]
        );

        // Hapus riwayat lama Dewi Lestari sebelum mengisi seeder
        RiwayatPoin::where('warga_id', $wargaDewi->id)->delete();

        // Riwayat 1: Setoran Awal Botol Plastik (28 Sep) -> Saldo 256
        RiwayatPoin::create([
            'warga_id' => $wargaDewi->id,
            'jenis' => 'masuk',
            'jumlah_poin' => 256,
            'saldo_akhir' => 256,
            'judul' => 'Botol PET bening 32 kg',
            'keterangan' => 'DU-2609-0094 · 28 Sep',
            'kode_pengajuan' => 'DU-2609-0094',
            'kategori_id' => $petBening?->id,
            'berat_kg' => 32.00,
            'created_at' => Carbon::parse('2026-09-28 10:15:00'),
            'updated_at' => Carbon::parse('2026-09-28 10:15:00'),
        ]);

        // Riwayat 2: Penukaran Potongan Iuran Oktober (5 Okt) -> Saldo 156
        RiwayatPoin::create([
            'warga_id' => $wargaDewi->id,
            'jenis' => 'keluar',
            'jumlah_poin' => 100,
            'saldo_akhir' => 156,
            'judul' => 'Potongan iuran Oktober',
            'keterangan' => 'Ditukar · 5 Okt',
            'kode_pengajuan' => null,
            'kategori_id' => null,
            'berat_kg' => null,
            'created_at' => Carbon::parse('2026-10-05 08:30:00'),
            'updated_at' => Carbon::parse('2026-10-05 08:30:00'),
        ]);

        // Riwayat 3: Setoran Minyak Jelantah 2,4 kg (22 Okt) -> Saldo 180 (Sesuai Mockup)
        RiwayatPoin::create([
            'warga_id' => $wargaDewi->id,
            'jenis' => 'masuk',
            'jumlah_poin' => 24,
            'saldo_akhir' => 180,
            'judul' => 'Minyak jelantah 2,4 kg',
            'keterangan' => 'DU-2610-0187 · 22 Okt',
            'kode_pengajuan' => 'DU-2610-0187',
            'kategori_id' => $minyakJelantah?->id,
            'berat_kg' => 2.40,
            'created_at' => Carbon::parse('2026-10-22 14:20:00'),
            'updated_at' => Carbon::parse('2026-10-22 14:20:00'),
        ]);

        // Update saldo poin Dewi Lestari
        $wargaDewi->update(['saldo_poin' => 180]);

        // 2. Beri saldo awal contoh untuk akun Budi Santoso (60 poin)
        $wargaBudi = Warga::where('nik', '3204081205850001')->first();
        if ($wargaBudi) {
            RiwayatPoin::where('warga_id', $wargaBudi->id)->delete();

            RiwayatPoin::create([
                'warga_id' => $wargaBudi->id,
                'jenis' => 'masuk',
                'jumlah_poin' => 60,
                'saldo_akhir' => 60,
                'judul' => 'Minyak jelantah 6 liter',
                'keterangan' => 'DU-2610-0102 · 12 Okt',
                'kode_pengajuan' => 'DU-2610-0102',
                'kategori_id' => $minyakJelantah?->id,
                'berat_kg' => 6.00,
                'created_at' => Carbon::parse('2026-10-12 09:00:00'),
                'updated_at' => Carbon::parse('2026-10-12 09:00:00'),
            ]);

            $wargaBudi->update(['saldo_poin' => 60]);
        }
    }
}
