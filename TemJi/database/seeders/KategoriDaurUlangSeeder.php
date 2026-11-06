<?php

namespace Database\Seeders;

use App\Models\KategoriDaurUlang;
use App\Models\RiwayatTarifPoin;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KategoriDaurUlangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Minyak Jelantah
        $minyak = KategoriDaurUlang::updateOrCreate(
            ['nama' => 'Minyak jelantah'],
            [
                'tipe' => 'Minyak jelantah',
                'satuan' => 'liter',
                'icon' => 'droplet',
                'deskripsi' => 'Minyak goreng sisa pakai yang bersih dan telah disaring ke dalam jeriken atau botol.',
                'is_active' => true,
            ]
        );

        RiwayatTarifPoin::updateOrCreate(
            [
                'kategori_id' => $minyak->id,
                'poin_per_kg' => 10,
                'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            ],
            [
                'berlaku_hingga' => null,
                'keterangan' => 'Tarif aktif operasional',
            ]
        );

        // 2. Botol PET bening
        $petBening = KategoriDaurUlang::updateOrCreate(
            ['nama' => 'Botol PET bening'],
            [
                'tipe' => 'Botol plastik',
                'satuan' => 'kg',
                'icon' => 'bottle',
                'deskripsi' => 'Botol plastik transparan/bening bebas label dan tutup dalam keadaan kering.',
                'is_active' => true,
            ]
        );

        RiwayatTarifPoin::updateOrCreate(
            [
                'kategori_id' => $petBening->id,
                'poin_per_kg' => 8,
                'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            ],
            [
                'berlaku_hingga' => null,
                'keterangan' => 'Tarif aktif daur ulang botol plastik PET bening',
            ]
        );

        // 3. Botol PET berwarna
        $petWarna = KategoriDaurUlang::updateOrCreate(
            ['nama' => 'Botol PET berwarna'],
            [
                'tipe' => 'Botol plastik',
                'satuan' => 'kg',
                'icon' => 'bottle',
                'deskripsi' => 'Botol plastik PET warna hijau, biru, atau varian lainnya dalam kondisi bersih.',
                'is_active' => true,
            ]
        );

        RiwayatTarifPoin::updateOrCreate(
            [
                'kategori_id' => $petWarna->id,
                'poin_per_kg' => 5,
                'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            ],
            [
                'berlaku_hingga' => null,
                'keterangan' => 'Tarif aktif botol PET berwarna',
            ]
        );

        // 4. Gelas plastik (Nonaktif)
        $gelas = KategoriDaurUlang::updateOrCreate(
            ['nama' => 'Gelas plastik'],
            [
                'tipe' => 'Botol plastik',
                'satuan' => 'kg',
                'icon' => 'cup',
                'deskripsi' => 'Gelas plastik minuman kemasan / cup sekali pakai.',
                'is_active' => false,
            ]
        );

        RiwayatTarifPoin::updateOrCreate(
            [
                'kategori_id' => $gelas->id,
                'poin_per_kg' => 4,
                'berlaku_sejak' => Carbon::create(2026, 8, 1, 0, 0, 0),
            ],
            [
                'berlaku_hingga' => null,
                'keterangan' => 'Tarif sebelum kategori dinonaktifkan sementara',
            ]
        );
    }
}
