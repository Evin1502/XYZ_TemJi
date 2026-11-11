<?php

namespace Database\Seeders;

use App\Models\IuranWilayah;
use Illuminate\Database\Seeder;

class IuranWilayahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['wilayah' => 'Sukapura', 'nominal' => 20000, 'rumah_tertagih' => 316, 'berlaku_sejak' => '2026-11-01'],
            ['wilayah' => 'Sukabirus', 'nominal' => 25000, 'rumah_tertagih' => 250, 'berlaku_sejak' => '2026-11-01'],
        ];

        foreach ($data as $item) {
            IuranWilayah::updateOrCreate(
                ['wilayah' => $item['wilayah']],
                $item
            );
        }
    }
}
