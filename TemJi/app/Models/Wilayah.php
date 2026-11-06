<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = ['nama', 'jumlah_kk', 'is_aktif'];

    protected $casts = [
        'jumlah_kk' => 'integer',
        'is_aktif'  => 'boolean',
    ];
}
