<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = [
        'nama',
        'jumlah_kk',
        'is_aktif',
    ];

    protected $casts = [
        'jumlah_kk' => 'integer',
        'is_aktif' => 'boolean',
    ];

    /**
     * Daftar warga yang terdaftar di wilayah ini.
     */
    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class, 'wilayah_id');
    }

    /**
     * Scope untuk mengambil wilayah yang sedang aktif (Sukapura, Sukabirus, dll).
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
