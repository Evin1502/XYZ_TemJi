<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sukapura = Wilayah::firstOrCreate(
            ['nama' => 'Sukapura'],
            ['jumlah_kk' => 0, 'is_aktif' => true]
        );

        $sukabirus = Wilayah::firstOrCreate(
            ['nama' => 'Sukabirus'],
            ['jumlah_kk' => 0, 'is_aktif' => true]
        );

        // Gunakan akun admin kanonik dari UserSeeder jika sudah tersedia (tanpa membuat admin baru)
        $adminId = User::where('role', 'admin')->value('id');

        // 1. Warga Terverifikasi: Budi Santoso (Sukapura)
        $userBudi = User::updateOrCreate(
            ['email' => 'budi.santoso@temji.id'],
            [
                'name' => 'Budi Santoso',
                'role' => 'warga',
                'password' => Hash::make('password123'),
            ]
        );

        Warga::updateOrCreate(
            ['nik' => '3204081205850001'],
            [
                'user_id' => $userBudi->id,
                'no_kk' => '3204081205100001',
                'nama_lengkap' => 'Budi Santoso',
                'no_hp' => '081223344556',
                'alamat' => 'Jl. Mengger Girang No. 14, RT 02/RW 04, Desa Sukapura, Kec. Dayeuhkolot',
                'wilayah_id' => $sukapura->id,
                'foto_identitas' => 'identitas_warga/sample_ktp_budi.jpg',
                'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
                'catatan_verifikasi' => 'Data KTP dan KK sesuai domisili RW 04 Sukapura.',
                'diverifikasi_oleh' => $adminId,
                'diverifikasi_pada' => Carbon::now()->subDays(3),
            ]
        );

        // 2. Warga Menunggu Verifikasi: Siti Aminah (Sukabirus)
        $userSiti = User::updateOrCreate(
            ['email' => 'siti.aminah@temji.id'],
            [
                'name' => 'Siti Aminah',
                'role' => 'warga',
                'password' => Hash::make('password123'),
            ]
        );

        Warga::updateOrCreate(
            ['nik' => '3204084509920002'],
            [
                'user_id' => $userSiti->id,
                'no_kk' => '3204084509180002',
                'nama_lengkap' => 'Siti Aminah',
                'no_hp' => '081398765432',
                'alamat' => 'Jl. Umayah II No. 8, RT 05/RW 03, Desa Sukabirus, Kec. Dayeuhkolot',
                'wilayah_id' => $sukabirus->id,
                'foto_identitas' => 'identitas_warga/sample_ktp_siti.jpg',
                'status_verifikasi' => Warga::STATUS_MENUNGGU,
                'catatan_verifikasi' => null,
                'diverifikasi_oleh' => null,
                'diverifikasi_pada' => null,
            ]
        );

        // 3. Warga Ditolak: Rian Pratama (Sukapura)
        $userRian = User::updateOrCreate(
            ['email' => 'rian.pratama@temji.id'],
            [
                'name' => 'Rian Pratama',
                'role' => 'warga',
                'password' => Hash::make('password123'),
            ]
        );

        Warga::updateOrCreate(
            ['nik' => '3204082111980003'],
            [
                'user_id' => $userRian->id,
                'no_kk' => '3204082111200003',
                'nama_lengkap' => 'Rian Pratama',
                'no_hp' => '085711223344',
                'alamat' => 'Gang PGA Blok C No. 21, RT 01/RW 02, Desa Sukapura, Kec. Dayeuhkolot',
                'wilayah_id' => $sukapura->id,
                'foto_identitas' => 'identitas_warga/sample_ktp_rian.jpg',
                'status_verifikasi' => Warga::STATUS_DITOLAK,
                'catatan_verifikasi' => 'Foto KTP buram dan angka NIK tidak terbaca jelas. Silakan unggah ulang foto KTP asli.',
                'diverifikasi_oleh' => $adminId,
                'diverifikasi_pada' => Carbon::now()->subDay(),
            ]
        );
    }
}
