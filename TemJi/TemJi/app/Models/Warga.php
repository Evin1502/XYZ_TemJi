<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warga extends Model
{
    protected $table = 'warga';

    protected $fillable = ['no_kk', 'nama_kepala_keluarga', 'alamat', 'wilayah_id'];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function scopeBelumBerwilayah($query)
    {
        return $query->whereNull('wilayah_id');
    }
}
