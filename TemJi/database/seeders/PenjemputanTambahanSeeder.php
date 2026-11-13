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

        $admin = User::where('role', User::ROLE_ADMIN)->value('id');

        // Hasil proses admin (PBI 09): dijadwalkan ke petugas, atau ditolak dengan alasan
        $dijadwalkan = [
            'petugas_id' => User::where('name', 'Pak Dedi')->value('id'),
            'tanggal_jemput' => $hariIni->copy()->addDays(4),
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '14:00:00',
            'diproses_oleh' => $admin,
            'diproses_pada' => now(),
        ];
        $ditolak = [
            'alasan_penolakan' => 'Jadwal rutin wilayah Anda jatuh sehari setelah tanggal yang diminta.',
            'diproses_oleh' => $admin,
            'diproses_pada' => now(),
        ];

        // [kode, email warga, selisih hari dari hari ini, alasan, status, data proses admin]
        $pengajuan = [
            ['PT-SEED-0001', 'budi.santoso@temji.id', 2, 'Ada acara keluarga, sampah menumpuk lebih banyak dari biasanya.', PenjemputanTambahan::STATUS_MENUNGGU, []],
            ['PT-SEED-0002', 'warga@temji.id', 3, 'Sampah sisa renovasi dapur belum terangkut di jadwal rutin.', PenjemputanTambahan::STATUS_DIBATALKAN, []],
            ['PT-SEED-0003', 'warga@temji.id', 4, 'Sampah kebun setelah pangkas pohon tidak muat di tempat sampah.', PenjemputanTambahan::STATUS_DISETUJUI, $dijadwalkan],
            ['PT-SEED-0004', 'siti.aminah@temji.id', 1, 'Tempat sampah penuh sebelum jadwal rutin berikutnya.', PenjemputanTambahan::STATUS_DITOLAK, $ditolak],
        ];

        foreach ($pengajuan as [$kode, $email, $selisihHari, $alasan, $status, $proses]) {
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
                ] + $proses
            );
        }
    }
}
