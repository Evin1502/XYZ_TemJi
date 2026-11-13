<?php

namespace Database\Seeders;

use App\Models\PenjemputanTambahan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PenjemputanTambahanSeeder extends Seeder
{
    public function run(): void
    {
        $hariIni = Carbon::today('Asia/Jakarta');

        // [kode, email warga, selisih hari dari hari ini, alasan, status]
        $pengajuan = [
            ['PT-SEED-0001', 'budi.santoso@temji.id', 2, 'Ada acara keluarga, sampah menumpuk lebih banyak dari biasanya.', PenjemputanTambahan::STATUS_MENUNGGU],
            ['PT-SEED-0002', 'warga@temji.id', 3, 'Sampah sisa renovasi dapur belum terangkut di jadwal rutin.', PenjemputanTambahan::STATUS_DIBATALKAN],
        ];

        foreach ($pengajuan as [$kode, $email, $selisihHari, $alasan, $status]) {
            $warga = User::with('warga')->where('email', $email)->first();

            // Akun warga contoh berasal dari seeder lain; lewati bila belum ada.
            if (! $warga?->wilayah_id_efektif) {
                continue;
            }

            PenjemputanTambahan::updateOrCreate(
                ['kode' => $kode],
                [
                    'user_id' => $warga->id,
                    'wilayah_id' => $warga->wilayah_id_efektif,
                    'tanggal_diminta' => $hariIni->copy()->addDays($selisihHari),
                    'alasan' => $alasan,
                    'status' => $status,
                ]
            );
        }
    }
}
