<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranWilayah extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel baru yang bersih
    protected $table = 'iuran_wilayah';

    protected $fillable = [
        'wilayah',
        'nominal',
        'rumah_tertagih',
        'berlaku_sejak'
    ];
}