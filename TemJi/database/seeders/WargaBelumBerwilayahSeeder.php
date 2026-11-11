<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/** Data uji PBI 1: warga lama yang belum punya wilayah. */
class WargaBelumBerwilayahSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('warga')) {
            return;   // tabel warga baru ada setelah branch Farhan di-merge
        }

        $data = [
            ['email' => 'warga.lama1@temji.id', 'nama' => 'Warga Lama Satu', 'nik' => '3273000000000101', 'kk' => '3273000000000901'],
            ['email' => 'warga.lama2@temji.id', 'nama' => 'Warga Lama Dua',  'nik' => '3273000000000102', 'kk' => '3273000000000902'],
            ['email' => 'warga.lama3@temji.id', 'nama' => 'Warga Lama Tiga', 'nik' => '3273000000000103', 'kk' => '3273000000000903'],
        ];

        foreach ($data as $d) {
            $user = User::updateOrCreate(
                ['email' => $d['email']],
                ['name' => $d['nama'], 'role' => User::ROLE_WARGA, 'password' => 'password123']
            );

            Warga::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $d['nik'], 'no_kk' => $d['kk'], 'nama_lengkap' => $d['nama'],
                    'no_hp' => '081200000000', 'alamat' => 'Alamat belum lengkap',
                    'wilayah_id' => null,
                    'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
                ]
            );
        }
    }
}
