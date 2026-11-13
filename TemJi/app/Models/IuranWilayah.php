<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranWilayah extends Model
{
    use HasFactory;

    protected $table = 'iuran_wilayahs';

    protected $fillable = [
        'wilayah',
        'nominal',
        'rumah_tertagih',
        'berlaku_sejak',
    ];

    protected $casts = [
        'nominal'        => 'decimal:2',
        'rumah_tertagih' => 'integer',
        'berlaku_sejak'  => 'date',
    ];
}