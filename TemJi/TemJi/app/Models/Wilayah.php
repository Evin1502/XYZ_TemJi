<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $fillable = ['nama', 'jumlah_kk', 'is_aktif'];

    protected $casts = [
        'jumlah_kk' => 'integer',
        'is_aktif'  => 'boolean',
    ];

    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
