<?php

namespace Database\Seeders;

use App\Models\AturanPenukaranPoin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AturanPenukaranPoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan akun admin kanonik dari UserSeeder jika sudah tersedia (tanpa membuat admin baru)
        $adminId = User::where('role', 'admin')->value('id');

        // 1. Riwayat Aturan Masa Lalu (Selesai): 1 Jul - 31 Agu 2026
        AturanPenukaranPoin::updateOrCreate(
            ['label_periode' => '1 Jul - 31 Agu 2026'],
            [
                'nilai_rupiah_per_poin' => 100,
                'batas_maksimal_potongan_persen' => 30,
                'minimal_poin_tukar' => 100,
                'berlaku_mulai' => '2026-07-01',
                'berlaku_sampai' => '2026-08-31',
                'diubah_oleh' => $adminId,
                'created_at' => Carbon::parse('2026-06-25 09:00:00'),
                'updated_at' => Carbon::parse('2026-08-28 14:30:00'),
            ]
        );

        // 2. Aturan Aktif Saat Ini (Berlaku): 1 Sep 2026 - sekarang
        AturanPenukaranPoin::updateOrCreate(
            ['label_periode' => '1 Sep 2026 - sekarang'],
            [
                'nilai_rupiah_per_poin' => 100,
                'batas_maksimal_potongan_persen' => 50,
                'minimal_poin_tukar' => 50,
                'berlaku_mulai' => '2026-09-01',
                'berlaku_sampai' => null,
                'diubah_oleh' => $adminId,
                'created_at' => Carbon::parse('2026-08-28 14:30:00'),
                'updated_at' => Carbon::parse('2026-08-28 14:30:00'),
            ]
        );
    }
}
