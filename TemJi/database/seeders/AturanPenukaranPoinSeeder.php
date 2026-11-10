<?php

namespace Database\Seeders;

use App\Models\AturanPenukaranPoin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AturanPenukaranPoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@temji.id'],
            [
                'name' => 'Admin TemJi',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 1. Riwayat Aturan Masa Lalu (Selesai): 1 Jul - 31 Agu 2026
        AturanPenukaranPoin::updateOrCreate(
            [
                'berlaku_mulai' => '2026-07-01',
                'berlaku_sampai' => '2026-08-31',
            ],
            [
                'nilai_rupiah_per_poin' => 100,
                'batas_maksimal_potongan_persen' => 30,
                'minimal_poin_tukar' => 100,
                'label_periode' => '1 Jul - 31 Agu 2026',
                'diubah_oleh' => $admin->id,
                'created_at' => Carbon::parse('2026-06-25 09:00:00'),
                'updated_at' => Carbon::parse('2026-08-28 14:30:00'),
            ]
        );

        // 2. Aturan Aktif Saat Ini (Berlaku): 1 Sep 2026 - sekarang
        AturanPenukaranPoin::updateOrCreate(
            [
                'berlaku_mulai' => '2026-09-01',
                'berlaku_sampai' => null,
            ],
            [
                'nilai_rupiah_per_poin' => 100,
                'batas_maksimal_potongan_persen' => 50,
                'minimal_poin_tukar' => 50,
                'label_periode' => '1 Sep 2026 - sekarang',
                'diubah_oleh' => $admin->id,
                'created_at' => Carbon::parse('2026-08-28 14:30:00'),
                'updated_at' => Carbon::parse('2026-08-28 14:30:00'),
            ]
        );
    }
}
