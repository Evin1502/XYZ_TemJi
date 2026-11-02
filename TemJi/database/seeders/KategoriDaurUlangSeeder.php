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
        $minyak = KategoriDaurUlang::create([
            'nama' => 'Minyak jelantah',
            'tipe' => 'Minyak jelantah',
            'satuan' => 'liter',
            'icon' => 'droplet',
            'deskripsi' => 'Minyak goreng sisa pakai yang bersih dan telah disaring ke dalam jeriken atau botol.',
            'is_active' => true,
        ]);

        // a. 8 poin/kg (1 Jul – 31 Agu 2026) -> Selesai
        RiwayatTarifPoin::create([
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 8,
            'berlaku_sejak' => Carbon::create(2026, 7, 1, 0, 0, 0),
            'berlaku_hingga' => Carbon::create(2026, 8, 31, 23, 59, 59),
            'keterangan' => 'Tarif awal periode Juli - Agustus',
        ]);

        // b. 10 poin/kg (1 Sep – 10 Nov 2026) -> Berlaku saat ini
        RiwayatTarifPoin::create([
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 10,
            'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            'berlaku_hingga' => Carbon::create(2026, 11, 10, 0, 0, 0),
            'keterangan' => 'Tarif aktif operasional',
        ]);

        // c. 12 poin/kg (Mulai 10 Nov 2026) -> Terjadwal di masa depan
        RiwayatTarifPoin::create([
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 12,
            'berlaku_sejak' => Carbon::create(2026, 11, 10, 0, 0, 0),
            'berlaku_hingga' => null,
            'keterangan' => 'Penyesuaian kemitraan biodiesel terjadwal',
        ]);

        // 2. Botol PET bening
        $petBening = KategoriDaurUlang::create([
            'nama' => 'Botol PET bening',
            'tipe' => 'Botol plastik',
            'satuan' => 'kg',
            'icon' => 'bottle',
            'deskripsi' => 'Botol plastik transparan/bening bebas label dan tutup dalam keadaan kering.',
            'is_active' => true,
        ]);

        RiwayatTarifPoin::create([
            'kategori_id' => $petBening->id,
            'poin_per_kg' => 8,
            'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            'berlaku_hingga' => null,
            'keterangan' => 'Tarif aktif daur ulang botol plastik PET bening',
        ]);

        // 3. Botol PET berwarna
        $petWarna = KategoriDaurUlang::create([
            'nama' => 'Botol PET berwarna',
            'tipe' => 'Botol plastik',
            'satuan' => 'kg',
            'icon' => 'bottle',
            'deskripsi' => 'Botol plastik PET warna hijau, biru, atau varian lainnya dalam kondisi bersih.',
            'is_active' => true,
        ]);

        RiwayatTarifPoin::create([
            'kategori_id' => $petWarna->id,
            'poin_per_kg' => 5,
            'berlaku_sejak' => Carbon::create(2026, 9, 1, 0, 0, 0),
            'berlaku_hingga' => null,
            'keterangan' => 'Tarif aktif botol PET berwarna',
        ]);

        // 4. Gelas plastik (Nonaktif)
        $gelas = KategoriDaurUlang::create([
            'nama' => 'Gelas plastik',
            'tipe' => 'Botol plastik',
            'satuan' => 'kg',
            'icon' => 'cup',
            'deskripsi' => 'Gelas plastik minuman kemasan / cup sekali pakai.',
            'is_active' => false,
        ]);

        RiwayatTarifPoin::create([
            'kategori_id' => $gelas->id,
            'poin_per_kg' => 4,
            'berlaku_sejak' => Carbon::create(2026, 8, 1, 0, 0, 0),
            'berlaku_hingga' => null,
            'keterangan' => 'Tarif sebelum kategori dinonaktifkan sementara',
        ]);
    }
}
