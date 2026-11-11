<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanIuran extends Model
{
    use HasFactory;

    protected $table = 'tagihan_iurans';

    protected $fillable = [
        'nama_warga',
        'wilayah',
        'bulan_tagihan',
        'nominal_asal',
        'poin_digunakan',
        'potongan_poin',
        'total_dibayar',
        'jatuh_tempo',
        'status',
        'metode_pembayaran',
        'bukti_transfer',
    ];

    protected $casts = [
        'nominal_asal'   => 'decimal:2',
        'potongan_poin'  => 'decimal:2',
        'total_dibayar'  => 'decimal:2',
        'poin_digunakan' => 'integer',
        'jatuh_tempo'    => 'date',
    ];

    public function isLunas(): bool
    {
        return $this->status === 'Lunas';
    }
}
